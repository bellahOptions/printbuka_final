<?php

namespace App\Support;

use App\Models\BlogPost;
use App\Models\DailyTodo;
use App\Models\FinanceEntry;
use App\Models\InventoryItem;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ShopOrder;
use App\Models\ShopProduct;
use App\Models\StaffQuery;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * Turns a request that hit an `admin.*` route into a plain-English sentence
 * fragment ("created an inventory item", "approved the move-forward request
 * for order #JOB-2026-0001") for the Activity Log — so staff/super admin can
 * read what happened without decoding route names and HTTP verbs.
 *
 * Deliberately best-effort: every route gets SOME readable sentence (falling
 * back to a generic verb + humanized resource name), rather than a curated
 * mapping for only a handful of actions. View-only requests (index/show/
 * create/edit, and anything not under `admin.*`) return null and are left to
 * display just their existing raw route/action text.
 */
class ActivityNarrator
{
    /**
     * Route "action" segment (the last dot-separated part of the route name)
     * → human verb phrase. Anything not listed falls back to a generic verb
     * derived from the HTTP method.
     *
     * @var array<string, string>
     */
    private const ACTION_VERBS = [
        'store' => 'created',
        'update' => 'updated',
        'destroy' => 'deleted',
        'close' => 'closed',
        'respond' => 'responded to',
        'send' => 'sent',
        'verify' => 'verified',
        'disable' => 'disabled',
        'enable' => 'enabled',
        'decide' => 'made a decision on',
        'conclude' => 'concluded',
        'correct' => 'corrected',
        'reject' => 'rejected',
        'approve' => 'approved',
        'finalize' => 'finalized',
        'reset' => 'reset',
        'acknowledge' => 'acknowledged',
        'regenerate' => 'regenerated',
        'override' => 'overrode',
        'refund' => 'refunded',
        'unrefund' => 'reversed the refund on',
        'approve-forward' => 'approved the move-forward request for',
        'move-forward' => 'requested to move forward',
        'adjust-stock' => 'recorded a stock movement for',
        'mark-paid' => 'confirmed payment for',
        'mark-done' => 'completed',
        'mark-working' => 'started work on',
        'update-status' => 'updated the status of',
        'update-entry' => 'updated an entry in',
        'destroy-run' => 'deleted',
        'update-run' => 'updated',
        'store-run' => 'created',
        'payment-terms' => 'updated payment terms for',
        'access-restriction' => 'updated access restriction for',
        'employment-status' => 'updated employment status for',
        'manual-entry' => 'added a manual entry to',
        'send-message' => 'sent a message to',
        'report-email' => 'emailed a report for',
        'import-csv' => 'imported a CSV into',
        'record-payment' => 'recorded a payment for',
        'receive-brief' => 'received the job brief for',
        'email-ceo' => 'emailed the CEO about',
        'salary-store' => 'saved salary details for',
        'send-payslips' => 'sent payslips for',
        'kyc-complete' => 'marked KYC complete for',
        'kyc-reminders' => 'sent KYC reminders to',
        'kyc-review' => 'reviewed KYC for',
    ];

    /**
     * Resource segments (joined with a space, hyphens intact) → [article, noun phrase].
     *
     * @var array<string, array{0: string, 1: string}>
     */
    private const RESOURCE_LABELS = [
        'inventory' => ['an', 'inventory item'],
        'staff' => ['a', 'staff member'],
        'staff-queries' => ['a', 'staff query'],
        'staff roles' => ['a', 'staff role'],
        'staff work-mode' => ['a', "staff member's work arrangement"],
        'staff permission-overrides' => ['a', "staff member's permission overrides"],
        'staff profile' => ['a', 'staff profile'],
        'finance' => ['a', 'finance entry'],
        'invoices' => ['an', 'invoice'],
        'invoices quotations' => ['a', 'quotation'],
        'orders' => ['a', 'job order'],
        'shop-orders' => ['a', 'shop order'],
        'shop-products' => ['a', 'shop product'],
        'products' => ['a', 'product'],
        'product-categories' => ['a', 'product category'],
        'customers' => ['a', 'customer'],
        'tasks' => ['a', 'task'],
        'payroll' => ['a', 'payroll run'],
        'blog' => ['a', 'blog post'],
        'large-format' => ['a', 'large format job'],
        'attendance' => ['an', 'attendance record'],
        'attendance holidays' => ['a', 'holiday'],
        'evaluations' => ['a', 'staff evaluation'],
        'training' => ['a', 'training application'],
        'advertisements' => ['an', 'advertisement'],
        'newsletters' => ['a', 'newsletter'],
        'support' => ['a', 'support ticket'],
        'memos' => ['a', 'memo'],
        'devices' => ['a', 'push notification device'],
        'email-templates' => ['an', 'email template'],
        'pdf-templates' => ['a', 'PDF template'],
        'two-factor' => ['their', 'two-factor settings'],
        'two-factor recovery-codes' => ['their', 'two-factor recovery codes'],
        'profile' => ['their', 'profile'],
        'settings' => ['the', 'site settings'],
        'policies' => ['a', 'policy document'],
        'pricelist' => ['the', 'pricelist'],
        'pricelist custom' => ['a', 'custom pricelist item'],
        'pricelist defaults' => ['the', 'default pricing'],
        'pricelist products' => ['a', "product's pricing"],
        'pricelist services' => ['a', "service's pricing"],
        'pricelist surcharges' => ['the', 'pricing surcharges'],
        'notifications' => ['a', 'broadcast notification'],
        'otp' => ['an', 'OTP code'],
    ];

    public static function describe(Request $request): ?string
    {
        $route = $request->route();

        if (! $route) {
            return null;
        }

        $name = (string) ($route->getName() ?? '');

        if (! Str::startsWith($name, 'admin.')) {
            return null;
        }

        $segments = explode('.', substr($name, 6));
        $action = array_pop($segments);

        if (in_array($action, ['index', 'show', 'create', 'edit'], true) || $action === '') {
            return null;
        }

        $subject = self::resolveSubjectLabel($route->parameters());
        $verb = self::ACTION_VERBS[$action] ?? self::fallbackVerb($request->method());
        $resource = self::humanizeResource($segments, $subject !== null);

        $sentence = trim($verb.' '.$resource);

        return $subject ? $sentence.' '.$subject : $sentence;
    }

    private static function fallbackVerb(string $method): string
    {
        return match (strtoupper($method)) {
            'POST' => 'submitted',
            'PUT', 'PATCH' => 'updated',
            'DELETE' => 'deleted',
            default => 'performed an action on',
        };
    }

    /**
     * @param  array<int, string>  $segments
     */
    private static function humanizeResource(array $segments, bool $hasSubject): string
    {
        if ($segments === []) {
            return 'a record';
        }

        $key = implode(' ', $segments);

        if (isset(self::RESOURCE_LABELS[$key])) {
            [$article, $noun] = self::RESOURCE_LABELS[$key];

            return $hasSubject ? $noun : $article.' '.$noun;
        }

        $words = collect($segments)
            ->flatMap(fn (string $segment) => explode('-', $segment))
            ->map(fn (string $word) => strtolower($word))
            ->values()
            ->all();

        $words[count($words) - 1] = Str::singular((string) end($words));
        $phrase = implode(' ', $words);

        if ($hasSubject) {
            return $phrase;
        }

        return (preg_match('/^[aeiou]/i', $phrase) ? 'an ' : 'a ').$phrase;
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    private static function resolveSubjectLabel(array $parameters): ?string
    {
        foreach ($parameters as $value) {
            $label = match (true) {
                $value instanceof Order => '#'.($value->job_order_number ?: $value->id),
                $value instanceof Invoice => '#'.($value->invoice_number ?: $value->id),
                $value instanceof FinanceEntry => '"'.($value->description ?: $value->category).'"',
                $value instanceof InventoryItem => '"'.$value->name.'"',
                $value instanceof ShopOrder => '#'.($value->reference ?: $value->id),
                $value instanceof ShopProduct => '"'.$value->name.'"',
                $value instanceof Product => '"'.$value->name.'"',
                $value instanceof ProductCategory => '"'.$value->name.'"',
                $value instanceof StaffQuery => '#'.($value->query_number ?: $value->id),
                $value instanceof DailyTodo => '"'.Str::limit((string) $value->task, 40).'"',
                $value instanceof BlogPost => '"'.($value->title ?? '').'"',
                $value instanceof User => '"'.$value->displayName().'"',
                default => null,
            };

            if ($label !== null) {
                return $label;
            }
        }

        return null;
    }
}

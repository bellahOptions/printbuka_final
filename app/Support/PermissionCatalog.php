<?php

namespace App\Support;

/**
 * The full set of permission strings the app actually gates on (route
 * middleware `admin.permission:*` plus ad-hoc `$user->canAdmin('...')`
 * checks in controllers). Used to render the permission checkboxes when
 * Super Admin creates or edits a role — grouped so the picker reads like a
 * short form, not a wall of checkboxes.
 */
class PermissionCatalog
{
    /**
     * @return array<string, array<string, string>> group label => [permission => description]
     */
    public static function grouped(): array
    {
        return [
            'Orders & Workflow' => [
                'orders.view' => 'View job orders',
                'orders.create' => 'Create job orders',
                'orders.intake' => 'Receive job briefs (phase 1)',
                'orders.verify' => 'Verify orders',
                'orders.phase_comment' => 'Comment on job phases',
                'workflow.approve' => 'Approve phase advancement',
                'sop.verify' => 'Verify SOP compliance',
                'sop.update' => 'Update SOP checklist',
                'client_review.update' => 'Update client review status',
            ],
            'Design' => [
                'design.update' => 'Update design phase (phase 2)',
                'design.upload' => 'Upload artwork/design files',
            ],
            'Production' => [
                'production.update' => 'Update production phase (phase 3)',
                'qc.update' => 'Update QC & packaging (phase 4)',
                'packaging.update' => 'Update packaging status',
                'delivery.update' => 'Update delivery phase (phase 5)',
            ],
            'Finance & Pricing' => [
                'invoices.manage' => 'Manage invoices',
                'finance.view' => 'View finance records',
                'finance.view_amounts' => 'View finance amounts',
                'payroll.manage' => 'Manage payroll runs',
                'payroll.view' => 'View payroll',
                'pricelist.manage' => 'Manage pricelist',
                'large_format.manage' => 'Manage large format jobs',
                'large_format.calculate' => 'Use large format calculator',
            ],
            'Staff & HR' => [
                'staff.view' => 'View staff directory',
                'staff.kyc' => 'Review staff KYC/bio-data',
                'staff.queries' => 'Manage staff queries',
                'staff.evaluations' => 'Manage staff evaluations',
                'evaluations.view' => 'View evaluations',
                'training.manage' => 'Manage training applications',
                'attendance.manage' => 'Manage team attendance',
            ],
            'Marketing & Customers' => [
                'blog.view' => 'View blog posts',
                'blog.manage' => 'Manage blog posts',
                'newsletters.manage' => 'Manage newsletters',
                'announcements.view' => 'View announcements',
                'customers.manage' => 'Manage customers',
            ],
            'Shop' => [
                'shop-products.manage' => 'Manage shop products',
                'shop-orders.view' => 'View shop orders',
            ],
            'System' => [
                'admin.view' => 'Access the admin portal (required for any staff role)',
                'products.manage' => 'Manage product catalog',
                'product_categories.manage' => 'Manage product categories',
                'site_settings.manage' => 'Manage site settings',
            ],
        ];
    }

    /**
     * @return list<string>
     */
    public static function all(): array
    {
        return collect(self::grouped())->flatMap(fn (array $perms) => array_keys($perms))->all();
    }
}

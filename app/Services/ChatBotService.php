<?php

namespace App\Services;

use App\Models\Order;
use App\Models\User;
use App\Support\SiteSettings;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ChatBotService
{
    /**
     * @param  array<int, array{sender: string, message: string}>  $history
     * @return array{reply: string, escalate: bool}
     */
    public function respond(?User $user, array $history, string $message): array
    {
        return $this->matchFaq($user, $message) ?? $this->askGemini($history, $message);
    }

    /**
     * @return array{reply: string, escalate: bool}|null
     */
    private function matchFaq(?User $user, string $message): ?array
    {
        $text = Str::lower(trim($message));

        if ($text === '') {
            return null;
        }

        if (Str::contains($text, ['human', 'agent', 'real person', 'representative', 'talk to someone'])) {
            return $this->reply(
                "Sure — I'll flag this for our team. Tap \"End chat\" whenever you're ready and I'll send them the full conversation as a support ticket.",
                escalate: true,
            );
        }

        if (preg_match('/PB-\d{4}-\d{4}/i', $message, $matches) === 1) {
            return $this->lookupOrder($user, strtoupper($matches[0]));
        }

        if (Str::contains($text, ['hello', 'good morning', 'good afternoon', 'good evening']) || in_array($text, ['hi', 'hey'], true)) {
            return $this->reply(
                "Hi! I'm Printbuka's assistant. I can help with order status, our services, pricing, and contact details — what do you need?",
                escalate: false,
            );
        }

        if (Str::contains($text, ['service', 'what do you offer', 'what can you print', 'what do you print'])) {
            $names = collect(config('printbuka_services.services'))->pluck('name')->implode(', ');

            return $this->reply(
                "We offer: {$names}. Want details on any of these, or should I point you to that product page?",
                escalate: false,
            );
        }

        if (Str::contains($text, ['price', 'cost', 'how much', 'pricing', 'quote'])) {
            return $this->reply(
                'Pricing depends on the service, material, and quantity, so exact figures come from the product/order page or a quote from our team. Tell me the service and quantity and I can point you to the right page.',
                escalate: false,
            );
        }

        if (Str::contains($text, ['contact', 'phone number', 'email address', 'reach you', 'call you'])) {
            return $this->reply(
                sprintf(
                    'You can reach us at %s or %s.',
                    SiteSettings::get('contact_email'),
                    SiteSettings::get('contact_phone'),
                ),
                escalate: false,
            );
        }

        return null;
    }

    private function lookupOrder(?User $user, string $reference): array
    {
        if (! $user) {
            return $this->reply(
                "I can look up order {$reference} once you're logged in. Please sign in and ask again, or I can pass this to our team.",
                escalate: true,
            );
        }

        $order = Order::query()
            ->where('job_order_number', $reference)
            ->where('user_id', $user->id)
            ->first();

        if (! $order) {
            return $this->reply(
                "I couldn't find order {$reference} on your account. Please double-check the reference, or I can pass this to our support team.",
                escalate: true,
            );
        }

        return $this->reply(
            "Order {$reference} is currently at: \"{$order->status}\".",
            escalate: false,
        );
    }

    /**
     * @param  array<int, array{sender: string, message: string}>  $history
     * @return array{reply: string, escalate: bool}
     */
    private function askGemini(array $history, string $message): array
    {
        $key = config('services.gemini.key');

        if (! $key) {
            return $this->fallback();
        }

        try {
            $model = config('services.gemini.model', 'gemini-2.5-flash');

            $response = Http::timeout(10)->post(
                "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$key}",
                [
                    'systemInstruction' => [
                        'parts' => [['text' => $this->systemPrompt()]],
                    ],
                    'contents' => $this->buildContents($history, $message),
                ],
            );

            if (! $response->successful()) {
                Log::warning('Gemini chatbot call failed.', ['status' => $response->status()]);

                return $this->fallback();
            }

            $text = data_get($response->json(), 'candidates.0.content.parts.0.text');

            if (! is_string($text) || trim($text) === '') {
                return $this->fallback();
            }

            return $this->reply(trim($text), escalate: false);
        } catch (\Throwable $exception) {
            Log::error('Gemini chatbot call errored.', ['message' => $exception->getMessage()]);

            return $this->fallback();
        }
    }

    private function fallback(): array
    {
        return $this->reply(
            "I don't have a confident answer for that yet. I'll pass this conversation to our support team — tap \"End chat\" and they'll follow up by email.",
            escalate: true,
        );
    }

    private function reply(string $text, bool $escalate): array
    {
        return ['reply' => $text, 'escalate' => $escalate];
    }

    private function systemPrompt(): string
    {
        return "You are Printbuka's customer support assistant for a B2B2C print shop offering Direct Image Printing, UV DTF, DTF, and Laser Engraving. "
            .'Answer in 2-4 concise, friendly sentences. Never invent exact prices, order statuses, or policies you are not given — '
            .'instead tell the customer you will escalate to a human. Never ask for passwords, OTPs, or payment details.';
    }

    /**
     * @param  array<int, array{sender: string, message: string}>  $history
     */
    private function buildContents(array $history, string $message): array
    {
        $contents = [];

        foreach (array_slice($history, -10) as $entry) {
            if ($entry['sender'] === 'staff') {
                continue;
            }

            $contents[] = [
                'role' => $entry['sender'] === 'user' ? 'user' : 'model',
                'parts' => [['text' => $entry['message']]],
            ];
        }

        $contents[] = ['role' => 'user', 'parts' => [['text' => $message]]];

        return $contents;
    }
}

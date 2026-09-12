<?php

use App\Models\ChatMessage;
use App\Models\ChatSession;
use App\Services\ChatBotService;
use App\Services\ChatConclusionService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

new class extends Component
{
    public bool $open = false;

    public string $draft = '';

    public ?int $chatSessionId = null;

    public bool $concluded = false;

    public ?string $ticketNumber = null;

    public bool $showLoginPrompt = false;

    public function mount(): void
    {
        $this->chatSessionId = $this->resolveSession()->id;
    }

    public function toggleOpen(): void
    {
        $this->open = ! $this->open;
    }

    public function send(): void
    {
        $text = trim($this->draft);

        if ($text === '' || $this->concluded) {
            return;
        }

        $session = ChatSession::query()->find($this->chatSessionId);

        if (! $session || ! $session->isActive()) {
            $session = $this->resolveSession();
            $this->chatSessionId = $session->id;
        }

        ChatMessage::create([
            'chat_session_id' => $session->id,
            'sender' => 'user',
            'message' => $text,
        ]);

        $history = $session->messages()
            ->latest('id')
            ->limit(10)
            ->get(['sender', 'message'])
            ->reverse()
            ->map(fn (ChatMessage $message): array => ['sender' => $message->sender, 'message' => $message->message])
            ->values()
            ->all();

        $result = app(ChatBotService::class)->respond(Auth::user(), $history, $text);

        ChatMessage::create([
            'chat_session_id' => $session->id,
            'sender' => 'bot',
            'message' => $result['reply'],
        ]);

        if ($result['escalate'] && ! $session->escalation_requested) {
            $session->update(['escalation_requested' => true]);
        } else {
            $session->touch();
        }

        $this->draft = '';
    }

    public function endChat(): void
    {
        if (! Auth::check()) {
            $this->showLoginPrompt = true;

            return;
        }

        $session = ChatSession::query()->find($this->chatSessionId);

        if ($session) {
            $ticket = app(ChatConclusionService::class)->conclude($session);
            $this->ticketNumber = $ticket?->ticket_number;
        }

        $this->concluded = true;
        session()->forget('chat_session_id');
    }

    public function startNewChat(): void
    {
        $this->concluded = false;
        $this->ticketNumber = null;
        $this->showLoginPrompt = false;
        $this->draft = '';
        $this->chatSessionId = $this->resolveSession()->id;
    }

    public function render()
    {
        $session = $this->concluded ? null : ChatSession::with('messages')->find($this->chatSessionId);

        return $this->view([
            'messages' => $session?->messages ?? collect(),
        ]);
    }

    private function resolveSession(): ChatSession
    {
        $existingId = session('chat_session_id');
        $existing = $existingId ? ChatSession::where('status', 'active')->find($existingId) : null;

        if ($existing) {
            if ($existing->user_id === Auth::id()) {
                return $existing;
            }

            if ($existing->user_id === null && Auth::check()) {
                $existing->update(['user_id' => Auth::id()]);

                return $existing;
            }

            if ($existing->user_id === null && ! Auth::check()) {
                return $existing;
            }
        }

        $session = ChatSession::create([
            'user_id' => Auth::id(),
            'status' => 'active',
            'started_at' => now(),
        ]);

        session(['chat_session_id' => $session->id]);

        return $session;
    }
}
?>

<div class="fixed bottom-5 right-5 z-50" wire:ignore.self>
    @if ($open)
        <div class="mb-3 flex h-[32rem] w-[22rem] max-w-[calc(100vw-2.5rem)] flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-2xl shadow-slate-900/20">
            <div class="flex items-center justify-between bg-slate-900 px-4 py-3 text-white">
                <div>
                    <p class="text-sm font-black">Printbuka Assistant</p>
                    <p class="text-[11px] text-slate-300">Usually replies instantly</p>
                </div>
                <button type="button" wire:click="toggleOpen" class="rounded-full p-1 text-slate-300 hover:bg-white/10 hover:text-white" aria-label="Close chat">
                    <x-heroicon-o-x-mark class="h-5 w-5" />
                </button>
            </div>

            <div class="flex-1 space-y-3 overflow-y-auto bg-slate-50 p-4">
                @if ($concluded)
                    <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800">
                        <p class="font-black">Conversation sent to our team{{ $ticketNumber ? " — ticket #{$ticketNumber}" : '' }}.</p>
                        <p class="mt-1 text-emerald-700">We'll follow up by email. You can start a new chat any time.</p>
                        <button type="button" wire:click="startNewChat" class="mt-3 rounded-lg bg-emerald-600 px-3 py-1.5 text-xs font-black text-white hover:bg-emerald-700">
                            Start new chat
                        </button>
                    </div>
                @else
                    @if ($messages->isEmpty())
                        <div class="chat chat-start">
                            <div class="chat-bubble bg-pink-600 text-white">
                                Hi! I'm Printbuka's assistant. Ask me about order status, our services, pricing, or how to reach us.
                            </div>
                        </div>
                    @endif

                    @foreach ($messages as $message)
                        <div class="chat {{ $message->sender === 'user' ? 'chat-end' : 'chat-start' }}">
                            <div @class([
                                'chat-bubble',
                                'bg-slate-900 text-white' => $message->sender === 'user',
                                'bg-pink-600 text-white' => $message->sender === 'bot',
                                'bg-amber-100 text-amber-900' => $message->sender === 'staff',
                            ])>
                                {{ $message->message }}
                            </div>
                        </div>
                    @endforeach

                    <div wire:loading wire:target="send" class="chat chat-start">
                        <div class="chat-bubble bg-pink-100 text-pink-600">Typing…</div>
                    </div>

                    @if ($showLoginPrompt)
                        <div class="rounded-xl border border-amber-200 bg-amber-50 p-3 text-xs text-amber-800">
                            Please
                            <a href="{{ route('login') }}" class="font-black underline">sign in</a>
                            so I can send this conversation to our support team as a ticket.
                        </div>
                    @endif
                @endif
            </div>

            @unless ($concluded)
                <form wire:submit.prevent="send" class="flex items-center gap-2 border-t border-slate-100 bg-white p-3">
                    <input
                        type="text"
                        wire:model="draft"
                        placeholder="Type a message…"
                        class="flex-1 rounded-full border border-slate-200 px-4 py-2 text-sm outline-none focus:border-pink-500 focus:ring-4 focus:ring-pink-100"
                    />
                    <button type="submit" class="rounded-full bg-pink-600 px-4 py-2 text-xs font-black text-white hover:bg-pink-700" wire:loading.attr="disabled" wire:target="send">
                        Send
                    </button>
                </form>
                <button type="button" wire:click="endChat" class="border-t border-slate-100 bg-slate-50 px-4 py-2 text-center text-[11px] font-black uppercase tracking-wide text-slate-400 hover:text-pink-600">
                    End chat &amp; email our team
                </button>
            @endunless
        </div>
    @endif

    <button
        type="button"
        wire:click="toggleOpen"
        class="flex h-14 w-14 items-center justify-center rounded-full bg-pink-600 text-white shadow-xl shadow-pink-600/30 transition hover:bg-pink-700"
        aria-label="{{ $open ? 'Close chat' : 'Open chat' }}"
    >
        @if ($open)
            <x-heroicon-o-chevron-down class="h-6 w-6" />
        @else
            <x-heroicon-o-chat-bubble-left-right class="h-6 w-6" />
        @endif
    </button>
</div>

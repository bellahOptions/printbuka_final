@extends('layouts.admin')

@section('title', 'Policy Management | Printbuka Admin')

@section('content')
    <div class="mx-auto max-w-6xl space-y-8">
        {{-- Header Section --}}
        <div class="rounded-2xl bg-gradient-to-r from-slate-900 to-slate-800 p-6 text-white shadow-xl lg:p-8">
            <div class="flex items-center gap-2 text-sm mb-2">
                <a href="{{ route('admin.dashboard') }}" class="text-cyan-300 hover:text-cyan-200 transition flex items-center gap-1">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    Admin Dashboard
                </a>
                <span class="text-slate-500">/</span>
                <span class="text-slate-300">Policy Management</span>
            </div>
            <h1 class="mt-3 text-4xl font-bold">Policy Management</h1>
            <p class="mt-3 max-w-3xl text-sm leading-6 text-slate-300">
                Super Admin only area for managing legal documents shown to customers and staff.
            </p>
            {{-- Super Admin Badge --}}
            <div class="mt-4 inline-flex items-center gap-2 rounded-full bg-pink-500/20 px-3 py-1 text-xs font-semibold text-pink-300">
                <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
                Super Admin Access Only
            </div>
        </div>

        {{-- Success Message Alert --}}
        @if (session('status'))
            <div class="alert alert-success shadow-lg rounded-2xl">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 shrink-0 stroke-current" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ session('status') }}</span>
            </div>
        @endif

        {{-- Terms & Conditions Section --}}
        <section class="card bg-white rounded-2xl shadow-xl border border-slate-100 overflow-hidden">
            <div class="card-body p-6 sm:p-8">
                <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
                    <div>
                        <div class="flex items-center gap-2 mb-2">
                            <div class="h-8 w-8 rounded-lg bg-pink-100 flex items-center justify-center">
                                <svg class="h-4 w-4 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                            <h2 class="text-2xl font-bold text-slate-900">Terms & Conditions</h2>
                        </div>
                        <p class="text-sm text-slate-500">Legal agreement between PrintBuka and its customers</p>
                    </div>
                    <div class="badge {{ $terms->is_published ? 'badge-success' : 'badge-warning' }} badge-lg">
                        {{ $terms->is_published ? 'Published' : 'Draft' }}
                    </div>
                </div>

                <form action="{{ route('admin.policies.terms.update') }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')
                    
                    {{-- Title Field --}}
                    <div class="form-control w-full">
                        <label class="label">
                            <span class="label-text font-semibold text-slate-700">Document Title</span>
                        </label>
                        <input type="text" name="title" value="{{ old('title', $terms->title) }}" 
                            class="input input-bordered w-full focus:input-primary"
                            placeholder="Terms & Conditions" />
                    </div>

                    {{-- Content Editor --}}
                    <div class="form-control w-full">
                        <label class="label">
                            <span class="label-text font-semibold text-slate-700">Document Content</span>
                        </label>
                        <div class="space-y-3">
                            {{-- Toolbar --}}
                            <div class="flex flex-wrap items-center gap-2 rounded-xl border border-slate-200 bg-slate-50 p-3" data-policy-editor-toolbar>
                                <button type="button" data-editor-command="bold" class="btn btn-sm btn-ghost text-slate-700 hover:text-pink-600" title="Bold">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 4h8a4 4 0 014 4 4 4 0 01-4 4H6z M6 12h9a4 4 0 014 4 4 4 0 01-4 4H6z"/>
                                    </svg>
                                </button>
                                <button type="button" data-editor-command="italic" class="btn btn-sm btn-ghost text-slate-700 hover:text-pink-600" title="Italic">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 4h4M6 20h4M14 4l-4 16"/>
                                    </svg>
                                </button>
                                <div class="w-px h-6 bg-slate-200"></div>
                                <button type="button" data-editor-command="formatBlock" data-editor-value="h2" class="btn btn-sm btn-ghost text-slate-700 hover:text-pink-600">H2</button>
                                <button type="button" data-editor-command="formatBlock" data-editor-value="h3" class="btn btn-sm btn-ghost text-slate-700 hover:text-pink-600">H3</button>
                                <div class="w-px h-6 bg-slate-200"></div>
                                <button type="button" data-editor-command="insertUnorderedList" class="btn btn-sm btn-ghost text-slate-700 hover:text-pink-600" title="Bullet List">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                                    </svg>
                                </button>
                                <button type="button" data-editor-command="insertOrderedList" class="btn btn-sm btn-ghost text-slate-700 hover:text-pink-600" title="Numbered List">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20h10M7 4h10M4 8h16M4 16h16"/>
                                    </svg>
                                </button>
                                <div class="w-px h-6 bg-slate-200"></div>
                                <button type="button" data-editor-command="createLink" class="btn btn-sm btn-ghost text-slate-700 hover:text-pink-600" title="Insert Link">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.102m3.172-3.172a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                                    </svg>
                                </button>
                                <button type="button" data-editor-command="removeFormat" class="btn btn-sm btn-ghost text-slate-700 hover:text-pink-600" title="Clear Formatting">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                    </svg>
                                </button>
                            </div>
                            
                            {{-- Editor Area --}}
                            <div data-policy-editor class="min-h-[400px] w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm leading-7 text-slate-700 focus:outline-none focus:border-pink-300 focus:ring-2 focus:ring-pink-100 overflow-y-auto"></div>
                            <textarea name="content" rows="12" data-policy-editor-source class="hidden">{{ old('content', $terms->content) }}</textarea>
                            <p class="text-xs text-slate-500 flex items-center gap-1">
                                <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Tip: Select text first, then apply formatting or add a link.
                            </p>
                        </div>
                    </div>

                    {{-- Publish Toggle --}}
                    <div class="form-control">
                        <label class="label cursor-pointer justify-start gap-3">
                            <input type="checkbox" name="is_published" value="1" @checked(old('is_published', $terms->is_published)) class="checkbox checkbox-pink-600" />
                            <span class="label-text font-semibold text-slate-700">Publish document (visible to customers)</span>
                        </label>
                    </div>

                    {{-- Submit Button --}}
                    <button type="submit" class="btn bg-pink-600 hover:bg-pink-700 border-0 text-white font-bold shadow-md shadow-pink-200">
                        <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Save Terms & Conditions
                    </button>
                </form>
            </div>
        </section>

        {{-- Privacy Policy Section --}}
        <section class="card bg-white rounded-2xl shadow-xl border border-slate-100 overflow-hidden">
            <div class="card-body p-6 sm:p-8">
                <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
                    <div>
                        <div class="flex items-center gap-2 mb-2">
                            <div class="h-8 w-8 rounded-lg bg-cyan-100 flex items-center justify-center">
                                <svg class="h-4 w-4 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                            </div>
                            <h2 class="text-2xl font-bold text-slate-900">Privacy Policy</h2>
                        </div>
                        <p class="text-sm text-slate-500">How we collect, use, and protect customer data</p>
                    </div>
                    <div class="badge {{ $privacy->is_published ? 'badge-success' : 'badge-warning' }} badge-lg">
                        {{ $privacy->is_published ? 'Published' : 'Draft' }}
                    </div>
                </div>

                <form action="{{ route('admin.policies.privacy.update') }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')
                    
                    <div class="form-control w-full">
                        <label class="label">
                            <span class="label-text font-semibold text-slate-700">Document Title</span>
                        </label>
                        <input type="text" name="title" value="{{ old('title', $privacy->title) }}" 
                            class="input input-bordered w-full focus:input-primary"
                            placeholder="Privacy Policy" />
                    </div>

                    <div class="form-control w-full">
                        <label class="label">
                            <span class="label-text font-semibold text-slate-700">Document Content</span>
                        </label>
                        <div class="space-y-3">
                            <div class="flex flex-wrap items-center gap-2 rounded-xl border border-slate-200 bg-slate-50 p-3" data-policy-editor-toolbar>
                                <button type="button" data-editor-command="bold" class="btn btn-sm btn-ghost text-slate-700 hover:text-pink-600">Bold</button>
                                <button type="button" data-editor-command="italic" class="btn btn-sm btn-ghost text-slate-700 hover:text-pink-600">Italic</button>
                                <div class="w-px h-6 bg-slate-200"></div>
                                <button type="button" data-editor-command="formatBlock" data-editor-value="h2" class="btn btn-sm btn-ghost text-slate-700 hover:text-pink-600">H2</button>
                                <button type="button" data-editor-command="formatBlock" data-editor-value="h3" class="btn btn-sm btn-ghost text-slate-700 hover:text-pink-600">H3</button>
                                <div class="w-px h-6 bg-slate-200"></div>
                                <button type="button" data-editor-command="insertUnorderedList" class="btn btn-sm btn-ghost text-slate-700 hover:text-pink-600">Bullets</button>
                                <button type="button" data-editor-command="insertOrderedList" class="btn btn-sm btn-ghost text-slate-700 hover:text-pink-600">Numbers</button>
                                <div class="w-px h-6 bg-slate-200"></div>
                                <button type="button" data-editor-command="createLink" class="btn btn-sm btn-ghost text-slate-700 hover:text-pink-600">Link</button>
                                <button type="button" data-editor-command="removeFormat" class="btn btn-sm btn-ghost text-slate-700 hover:text-pink-600">Clear</button>
                            </div>
                            <div data-policy-editor class="min-h-[400px] w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm leading-7 text-slate-700 focus:outline-none focus:border-pink-300 focus:ring-2 focus:ring-pink-100 overflow-y-auto"></div>
                            <textarea name="content" rows="12" data-policy-editor-source class="hidden">{{ old('content', $privacy->content) }}</textarea>
                            <p class="text-xs text-slate-500 flex items-center gap-1">
                                <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Tip: Select text first, then apply formatting or add a link.
                            </p>
                        </div>
                    </div>

                    <div class="form-control">
                        <label class="label cursor-pointer justify-start gap-3">
                            <input type="checkbox" name="is_published" value="1" @checked(old('is_published', $privacy->is_published)) class="checkbox checkbox-pink-600" />
                            <span class="label-text font-semibold text-slate-700">Publish document (visible to customers)</span>
                        </label>
                    </div>

                    <button type="submit" class="btn bg-pink-600 hover:bg-pink-700 border-0 text-white font-bold shadow-md shadow-pink-200">
                        <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Save Privacy Policy
                    </button>
                </form>
            </div>
        </section>

        {{-- Refund Policy Section --}}
        <section class="card bg-white rounded-2xl shadow-xl border border-slate-100 overflow-hidden">
            <div class="card-body p-6 sm:p-8">
                <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
                    <div>
                        <div class="flex items-center gap-2 mb-2">
                            <div class="h-8 w-8 rounded-lg bg-emerald-100 flex items-center justify-center">
                                <svg class="h-4 w-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <h2 class="text-2xl font-bold text-slate-900">Refund Policy</h2>
                        </div>
                        <p class="text-sm text-slate-500">Return, refund, and cancellation terms</p>
                    </div>
                    <div class="badge {{ $refund->is_published ? 'badge-success' : 'badge-warning' }} badge-lg">
                        {{ $refund->is_published ? 'Published' : 'Draft' }}
                    </div>
                </div>

                <form action="{{ route('admin.policies.refund.update') }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')
                    
                    <div class="form-control w-full">
                        <label class="label">
                            <span class="label-text font-semibold text-slate-700">Document Title</span>
                        </label>
                        <input type="text" name="title" value="{{ old('title', $refund->title) }}" 
                            class="input input-bordered w-full focus:input-primary"
                            placeholder="Refund Policy" />
                    </div>

                    <div class="form-control w-full">
                        <label class="label">
                            <span class="label-text font-semibold text-slate-700">Document Content</span>
                        </label>
                        <div class="space-y-3">
                            <div class="flex flex-wrap items-center gap-2 rounded-xl border border-slate-200 bg-slate-50 p-3" data-policy-editor-toolbar>
                                <button type="button" data-editor-command="bold" class="btn btn-sm btn-ghost text-slate-700 hover:text-pink-600">Bold</button>
                                <button type="button" data-editor-command="italic" class="btn btn-sm btn-ghost text-slate-700 hover:text-pink-600">Italic</button>
                                <div class="w-px h-6 bg-slate-200"></div>
                                <button type="button" data-editor-command="formatBlock" data-editor-value="h2" class="btn btn-sm btn-ghost text-slate-700 hover:text-pink-600">H2</button>
                                <button type="button" data-editor-command="formatBlock" data-editor-value="h3" class="btn btn-sm btn-ghost text-slate-700 hover:text-pink-600">H3</button>
                                <div class="w-px h-6 bg-slate-200"></div>
                                <button type="button" data-editor-command="insertUnorderedList" class="btn btn-sm btn-ghost text-slate-700 hover:text-pink-600">Bullets</button>
                                <button type="button" data-editor-command="insertOrderedList" class="btn btn-sm btn-ghost text-slate-700 hover:text-pink-600">Numbers</button>
                                <div class="w-px h-6 bg-slate-200"></div>
                                <button type="button" data-editor-command="createLink" class="btn btn-sm btn-ghost text-slate-700 hover:text-pink-600">Link</button>
                                <button type="button" data-editor-command="removeFormat" class="btn btn-sm btn-ghost text-slate-700 hover:text-pink-600">Clear</button>
                            </div>
                            <div data-policy-editor class="min-h-[400px] w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm leading-7 text-slate-700 focus:outline-none focus:border-pink-300 focus:ring-2 focus:ring-pink-100 overflow-y-auto"></div>
                            <textarea name="content" rows="12" data-policy-editor-source class="hidden">{{ old('content', $refund->content) }}</textarea>
                            <p class="text-xs text-slate-500 flex items-center gap-1">
                                <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Tip: Select text first, then apply formatting or add a link.
                            </p>
                        </div>
                    </div>

                    <div class="form-control">
                        <label class="label cursor-pointer justify-start gap-3">
                            <input type="checkbox" name="is_published" value="1" @checked(old('is_published', $refund->is_published)) class="checkbox checkbox-pink-600" />
                            <span class="label-text font-semibold text-slate-700">Publish document (visible to customers)</span>
                        </label>
                    </div>

                    <button type="submit" class="btn bg-pink-600 hover:bg-pink-700 border-0 text-white font-bold shadow-md shadow-pink-200">
                        <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Save Refund Policy
                    </button>
                </form>
            </div>
        </section>
    </div>

    <script>
        (() => {
            const initPolicyEditor = (form) => {
                const source = form.querySelector('[data-policy-editor-source]');
                const editor = form.querySelector('[data-policy-editor]');
                const toolbar = form.querySelector('[data-policy-editor-toolbar]');

                if (!source || !editor || !toolbar) {
                    return;
                }

                editor.contentEditable = 'true';
                editor.innerHTML = (source.value || '').trim() || '<p></p>';

                {{-- Save cursor position before formatting --}}
                let savedRange = null;

                const saveSelection = () => {
                    const selection = window.getSelection();
                    if (selection.rangeCount > 0) {
                        savedRange = selection.getRangeAt(0).cloneRange();
                    }
                };

                const restoreSelection = () => {
                    if (savedRange) {
                        const selection = window.getSelection();
                        selection.removeAllRanges();
                        selection.addRange(savedRange);
                    }
                };

                toolbar.querySelectorAll('[data-editor-command]').forEach((button) => {
                    button.addEventListener('click', (e) => {
                        e.preventDefault();
                        const command = button.dataset.editorCommand || '';
                        const value = button.dataset.editorValue || null;

                        saveSelection();
                        editor.focus();
                        restoreSelection();

                        if (command === 'createLink') {
                            const url = window.prompt('Enter a full URL (https://...)');
                            if (!url) {
                                return;
                            }
                            document.execCommand('createLink', false, url);
                        } else if (command === 'formatBlock') {
                            document.execCommand('formatBlock', false, `<${value}>`);
                        } else {
                            document.execCommand(command, false, value);
                        }
                        
                        editor.focus();
                    });
                });

                form.addEventListener('submit', () => {
                    source.value = editor.innerHTML.trim();
                });
            };

            {{-- Initialize editors for all policy forms --}}
            document.querySelectorAll('form[action*="/admin/policies/"]').forEach((form) => {
                initPolicyEditor(form);
            });
        })();
    </script>
@endsection
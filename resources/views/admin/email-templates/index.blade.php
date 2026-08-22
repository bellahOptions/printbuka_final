@extends('layouts.admin')
@section('title', 'Email Templates | Printbuka')

@section('content')
<div class="mx-auto max-w-4xl space-y-6">

    <div>
        <h1 class="text-2xl font-black text-slate-950">Email Templates</h1>
        <p class="text-sm text-slate-500 mt-1">Customize the intro, footer, and subject line of system emails. The core content — invoice line items, payslip figures, etc. — always stays accurate.</p>
    </div>

    @if (session('status'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-bold text-emerald-800">{{ session('status') }}</div>
    @endif

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <table class="w-full">
            <thead class="border-b border-slate-200 bg-slate-50">
                <tr class="text-xs font-black uppercase tracking-wide text-slate-500">
                    <th class="px-5 py-3.5 text-left">Template</th>
                    <th class="px-5 py-3.5 text-left">Status</th>
                    <th class="px-5 py-3.5"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach ($templates as $template)
                <tr class="hover:bg-slate-50/70 transition">
                    <td class="px-5 py-4 font-black text-slate-900">{{ $template['name'] }}</td>
                    <td class="px-5 py-4">
                        @if ($template['is_customized'])
                            <span class="pb-badge pb-badge-success">Customized · {{ $template['updated_at']?->diffForHumans() }}</span>
                        @else
                            <span class="pb-badge pb-badge-outline">Default</span>
                        @endif
                    </td>
                    <td class="px-5 py-4 text-right">
                        <a href="{{ route('admin.email-templates.edit', $template['key']) }}" class="text-sm font-black text-slate-700 hover:text-pink-600">Edit</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</div>
@endsection

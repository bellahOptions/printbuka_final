@extends('layouts.admin')
@section('title', 'Email Templates | Printbuka')

@section('content')
<div class="mx-auto max-w-4xl space-y-6">

    <div>
        <h1 class="pb-page-title">Email Templates</h1>
        <p class="pb-page-subtitle">Customize the intro, footer, and subject line of system emails. The core content — invoice line items, payslip figures, etc. — always stays accurate.</p>
    </div>

    @if (session('status'))
        <div class="pb-alert pb-alert-success">{{ session('status') }}</div>
    @endif

    <div class="pb-table-wrapper">
        <table class="pb-table pb-table--cards w-full">
            <thead>
                <tr>
                    <th>Template</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($templates as $template)
                <tr>
                    <td data-label="Template" class="font-semibold text-slate-900">{{ $template['name'] }}</td>
                    <td data-label="Status">
                        @if ($template['is_customized'])
                            <span class="pb-badge pb-badge-success">Customized · {{ $template['updated_at']?->diffForHumans() }}</span>
                        @else
                            <span class="pb-badge pb-badge-outline">Default</span>
                        @endif
                    </td>
                    <td class="text-right">
                        <a href="{{ route('admin.email-templates.edit', $template['key']) }}" class="pb-btn pb-btn-sm pb-btn-ghost">Edit</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</div>
@endsection

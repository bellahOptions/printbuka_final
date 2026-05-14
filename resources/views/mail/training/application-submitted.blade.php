@extends('mail.layouts.training')

@section('title', 'New PGTP Application')

@php
    $applicantName = trim($application->first_name.' '.$application->last_name);
@endphp

@section('content')
    <h1 style="margin:18px 0 0;font-size:28px;line-height:1.2;">{{ $applicantName }} applied for PGTP.</h1>
    <p style="margin:10px 0 0;color:#cbd5e1;font-size:14px;line-height:1.7;">Preferred track: <strong style="color:#ffffff;">{{ $application->desired_skill }}</strong></p>
    <div style="margin:0 0 20px;padding:16px;border:1px solid #bae6fd;background:#f0f9ff;border-radius:10px;">
        <p style="margin:0;font-size:13px;line-height:1.7;color:#0c4a6e;">A new training application has been submitted through the public PGTP form. Review the details below and follow up with the applicant if they fit the cohort requirements.</p>
    </div>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;margin:0 0 20px;">
        @foreach ([
            'Name' => $applicantName,
            'Email' => $application->email,
            'Phone / WhatsApp' => $application->phone_whatsapp,
            'Date of birth' => $application->date_of_birth?->format('F j, Y'),
            'Gender' => $application->gender ?: 'Not provided',
            'City / State' => $application->city_state,
            'Contact address' => $application->contact_address,
            'Educational qualification' => $application->educational_qualification,
            'Desired skill' => $application->desired_skill,
            'Current status' => $application->employment_status ?: 'Not provided',
            'Experience level' => $application->experience_level ?: 'Not provided',
            'Has laptop' => $application->has_laptop ? 'Yes' : 'No',
            'Availability' => $application->availability,
            'Portfolio / social link' => $application->portfolio_url ?: 'Not provided',
            'Referral source' => $application->referral_source ?: 'Not provided',
            'Submitted' => $application->created_at?->format('F j, Y g:i A'),
        ] as $label => $value)
            <tr>
                <td style="width:34%;padding:11px;border:1px solid #e2e8f0;background:#f8fafc;font-size:12px;font-weight:700;vertical-align:top;">{{ $label }}</td>
                <td style="padding:11px;border:1px solid #e2e8f0;font-size:13px;line-height:1.6;vertical-align:top;">{{ $value }}</td>
            </tr>
        @endforeach
    </table>

    <div style="padding:16px;border:1px solid #fbcfe8;background:#fdf2f8;border-radius:10px;">
        <p style="margin:0 0 8px;font-size:12px;font-weight:700;color:#be185d;text-transform:uppercase;letter-spacing:0.06em;">Motivation</p>
        <p style="margin:0;font-size:13px;line-height:1.7;color:#831843;">{{ $application->motivation }}</p>
    </div>
@endsection

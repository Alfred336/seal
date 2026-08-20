<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New SealTech Plan Inquiry</title>
</head>
<body style="margin:0;background:#f8fafc;font-family:Inter,Arial,sans-serif;color:#0f172a;">
    <div style="max-width:700px;margin:40px auto;background:#fff;border:1px solid #e2e8f0;border-radius:16px;overflow:hidden;">
        <div style="padding:32px;background:#0f172a;color:#fff;">
            <div style="font-size:24px;font-weight:800;">SealTech Dashboard</div>
            <div style="margin-top:6px;color:#22d3ee;">New Plan Inquiry</div>
        </div>
        <div style="padding:36px;">
            <h2 style="margin-top:0;">{{ ucfirst($inquiry->plan) }} Plan</h2>
            <table style="width:100%;border-collapse:collapse;">
                @foreach([
                    'Reference' => 'ST-PI-'.str_pad((string) $inquiry->id, 5, '0', STR_PAD_LEFT),
                    'Name' => $inquiry->full_name,
                    'Business' => $inquiry->business_name,
                    'Phone' => $inquiry->phone,
                    'Email' => $inquiry->email,
                    'Status' => $inquiry->status->value,
                    'Submitted' => $inquiry->created_at->toDayDateTimeString(),
                ] as $label => $value)
                    <tr>
                        <td style="padding:11px;border-bottom:1px solid #e2e8f0;color:#64748b;width:30%;">{{ $label }}</td>
                        <td style="padding:11px;border-bottom:1px solid #e2e8f0;font-weight:700;">{{ $value ?: '—' }}</td>
                    </tr>
                @endforeach
            </table>

            <h3 style="margin-top:28px;">Requirements</h3>
            <div style="white-space:pre-wrap;padding:18px;background:#f8fafc;border-radius:10px;border:1px solid #e2e8f0;color:#334155;line-height:1.7;">
                {{ $inquiry->website_purpose ?: $inquiry->project_description ?: $inquiry->additional_requirements ?: $inquiry->additional_message ?: 'No additional requirements provided.' }}
            </div>

            <a href="{{ route('manage.plan-inquiries.index') }}" style="display:inline-block;margin-top:24px;padding:13px 22px;background:#0f172a;color:#fff;text-decoration:none;border-radius:8px;font-weight:700;">
                Open Plan Inquiries
            </a>
        </div>
    </div>
</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SealTech Plan Inquiry</title>
</head>
<body style="margin:0;background:#f8fafc;font-family:Inter,Arial,sans-serif;color:#0f172a;">
    <div style="max-width:620px;margin:40px auto;background:#fff;border:1px solid #e2e8f0;border-radius:16px;overflow:hidden;">
        <div style="padding:32px;background:#0f172a;color:#fff;">
            <div style="font-size:24px;font-weight:800;">SealTech</div>
            <div style="margin-top:6px;color:#94a3b8;">Plan inquiry received</div>
        </div>
        <div style="padding:36px;">
            <h2 style="margin-top:0;">Hello {{ $inquiry->full_name }},</h2>
            <p style="line-height:1.7;color:#475569;">
                Thank you for choosing SealTech. We have received your
                <strong>{{ ucfirst($inquiry->plan) }}</strong> plan inquiry.
                Our team will review your requirements and contact you shortly.
            </p>

            <div style="margin:24px 0;padding:18px;background:#f8fafc;border-radius:12px;border:1px solid #e2e8f0;">
                <div style="font-size:12px;color:#64748b;text-transform:uppercase;letter-spacing:.08em;">Reference</div>
                <div style="font-size:20px;font-weight:800;margin-top:5px;">
                    ST-PI-{{ str_pad((string) $inquiry->id, 5, '0', STR_PAD_LEFT) }}
                </div>
            </div>

            <p style="color:#475569;line-height:1.7;">
                Selected plan: <strong>{{ ucfirst($inquiry->plan) }}</strong><br>
                Submitted: <strong>{{ $inquiry->created_at->toDayDateTimeString() }}</strong>
            </p>
        </div>
        <div style="padding:22px 36px;background:#f1f5f9;color:#64748b;font-size:12px;">
            &copy; {{ date('Y') }} SealTech. All rights reserved.
        </div>
    </div>
</body>
</html>

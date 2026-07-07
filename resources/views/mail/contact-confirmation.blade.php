<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Submission Confirmation</title>
    <style>
        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background-color: #f8fafc;
            color: #1e293b;
            margin: 0;
            padding: 0;
            -webkit-font-smoothing: antialiased;
        }
        .container {
            max-width: 600px;
            margin: 40px auto;
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.02);
            border: 1px solid #e2e8f0;
        }
        .header {
            background-color: #0f172a;
            padding: 32px;
            text-align: center;
        }
        .header h1 {
            color: #ffffff;
            font-size: 24px;
            font-weight: 700;
            margin: 0;
            letter-spacing: -0.025em;
        }
        .header p {
            color: #94a3b8;
            font-size: 14px;
            margin: 8px 0 0 0;
        }
        .content {
            padding: 40px 32px;
        }
        .greeting {
            font-size: 18px;
            font-weight: 600;
            margin-top: 0;
            margin-bottom: 16px;
            color: #0f172a;
        }
        .message-box {
            background-color: #f1f5f9;
            border-left: 4px solid #3b82f6;
            padding: 16px;
            border-radius: 4px;
            margin: 24px 0;
            font-style: italic;
            color: #334155;
        }
        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 24px;
        }
        .details-table th, .details-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
            font-size: 14px;
        }
        .details-table th {
            color: #64748b;
            font-weight: 500;
            width: 30%;
        }
        .details-table td {
            color: #0f172a;
            font-weight: 600;
        }
        .footer {
            background-color: #f1f5f9;
            padding: 24px;
            text-align: center;
            font-size: 12px;
            color: #64748b;
            border-top: 1px solid #e2e8f0;
        }
        .footer a {
            color: #3b82f6;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>SealTech</h1>
            <p>Innovative Software Engineering & Digital Solutions</p>
        </div>
        <div class="content">
            <p class="greeting">Hello {{ $submission->name }},</p>
            <p>Thank you for reaching out to SealTech. We have received your contact inquiry and our team is already reviewing it. We will get back to you within 24 business hours.</p>
            
            <p>Here is a summary of the message you sent us:</p>
            <div class="message-box">
                "{{ $submission->message }}"
            </div>

            <table class="details-table">
                @if($submission->company)
                    <tr>
                        <th>Company</th>
                        <td>{{ $submission->company }}</td>
                    </tr>
                @endif
                @if($submission->phone)
                    <tr>
                        <th>Phone</th>
                        <td>{{ $submission->phone }}</td>
                    </tr>
                @endif
                @if($submission->project_type)
                    <tr>
                        <th>Project Type</th>
                        <td>{{ $submission->project_type }}</td>
                    </tr>
                @endif
                <tr>
                    <th>Submitted At</th>
                    <td>{{ $submission->submitted_at->toDayDateTimeString() }}</td>
                </tr>
            </table>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} SealTech. All rights reserved.</p>
            <p>If you did not submit this request, please ignore this email.</p>
        </div>
    </div>
</body>
</html>

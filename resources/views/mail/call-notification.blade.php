<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Call Request</title>
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
            color: #10b981;
            font-size: 14px;
            font-weight: 600;
            margin: 8px 0 0 0;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .content {
            padding: 40px 32px;
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
        .action-button {
            display: inline-block;
            background-color: #0f172a;
            color: #ffffff !important;
            text-decoration: none;
            padding: 12px 24px;
            border-radius: 6px;
            font-weight: 600;
            margin-top: 24px;
            text-align: center;
        }
        .footer {
            background-color: #f1f5f9;
            padding: 24px;
            text-align: center;
            font-size: 12px;
            color: #64748b;
            border-top: 1px solid #e2e8f0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>SealTech Dashboard</h1>
            <p>New Call Booking Request</p>
        </div>
        <div class="content">
            <p>A new call request has been scheduled on the SealTech website. Here are the details:</p>

            <table class="details-table">
                <tr>
                    <th>Requester Name</th>
                    <td>{{ $requestModel->full_name }}</td>
                </tr>
                <tr>
                    <th>Email Address</th>
                    <td>{{ $requestModel->email }}</td>
                </tr>
                <tr>
                    <th>Phone</th>
                    <td>{{ $requestModel->phone }}</td>
                </tr>
                <tr>
                    <th>Preferred Date</th>
                    <td>{{ \Illuminate\Support\Carbon::parse($requestModel->preferred_date)->toFormattedDateString() }}</td>
                </tr>
                @if($requestModel->notes)
                    <tr>
                        <th>Notes/Message</th>
                        <td>{{ $requestModel->notes }}</td>
                    </tr>
                @endif
                <tr>
                    <th>IP Address</th>
                    <td>{{ $requestModel->ip_address }}</td>
                </tr>
                <tr>
                    <th>Created At</th>
                    <td>{{ $requestModel->created_at->toDayDateTimeString() }}</td>
                </tr>
            </table>

            <a href="{{ route('manage.call-requests.index') }}" class="action-button">View in Admin Panel</a>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} SealTech. All rights reserved.</p>
            <p>This is an automated system notification.</p>
        </div>
    </div>
</body>
</html>

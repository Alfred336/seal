<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accept Your Invitation - SealTech</title>
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
        .details-box {
            background-color: #f1f5f9;
            padding: 16px;
            border-radius: 6px;
            margin: 24px 0;
            border: 1px solid #e2e8f0;
        }
        .details-table {
            width: 100%;
            border-collapse: collapse;
        }
        .details-table th, .details-table td {
            padding: 8px 0;
            text-align: left;
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
        .action-area {
            text-align: center;
            margin: 32px 0;
        }
        .action-button {
            display: inline-block;
            background-color: #3b82f6;
            color: #ffffff !important;
            text-decoration: none;
            padding: 14px 28px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 15px;
            box-shadow: 0 4px 6px -1px rgba(59, 130, 246, 0.2), 0 2px 4px -1px rgba(59, 130, 246, 0.1);
            transition: all 0.2s ease-in-out;
        }
        .action-button:hover {
            background-color: #2563eb;
        }
        .footer {
            background-color: #f1f5f9;
            padding: 24px;
            text-align: center;
            font-size: 12px;
            color: #64748b;
            border-top: 1px solid #e2e8f0;
        }
        .link-text {
            word-break: break-all;
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
            <p class="greeting">Hello {{ $user->name }},</p>
            <p>You have been invited to join the SealTech team as a member of our digital platform. An administrator has set up your account, and it is now ready for activation.</p>
            
            <p>Below are your account details:</p>
            <div class="details-box">
                <table class="details-table">
                    <tr>
                        <th>Email</th>
                        <td>{{ $user->email }}</td>
                    </tr>
                    <tr>
                        <th>Role Assigned</th>
                        <td>{{ ucfirst($roleName) }}</td>
                    </tr>
                </table>
            </div>

            <p>To accept this invitation and secure your account, please click the button below to choose a password and complete your registration:</p>

            <div class="action-area">
                <a href="{{ $invitationUrl }}" class="action-button">Accept Invitation & Set Password</a>
            </div>

            <p style="font-size: 13px; color: #64748b; margin-top: 32px;">
                If you have trouble clicking the button, copy and paste the following URL into your browser:<br>
                <a href="{{ $invitationUrl }}" class="link-text">{{ $invitationUrl }}</a>
            </p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} SealTech. All rights reserved.</p>
            <p>This invitation link will expire in 60 minutes.</p>
        </div>
    </div>
</body>
</html>

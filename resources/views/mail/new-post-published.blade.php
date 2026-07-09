<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Post: {{ $post->title }}</title>
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
        .title {
            font-size: 20px;
            font-weight: 700;
            margin-top: 0;
            margin-bottom: 12px;
            color: #0f172a;
            line-height: 1.4;
        }
        .meta-info {
            margin-bottom: 24px;
            font-size: 13px;
            color: #64748b;
        }
        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 9999px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-right: 8px;
        }
        .excerpt-box {
            background-color: #f1f5f9;
            padding: 20px;
            border-radius: 8px;
            margin: 24px 0;
            border-left: 4px solid #3b82f6;
            color: #334155;
            font-size: 15px;
            line-height: 1.6;
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
        .unsubscribe-link {
            color: #64748b;
            text-decoration: underline;
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
            <h2 class="title">{{ $post->title }}</h2>
            
            <div class="meta-info">
                @if($post->category)
                    <span class="badge" style="background-color: {{ $post->category->color ?? '#3b82f6' }}; color: #ffffff;">
                        {{ $post->category->name }}
                    </span>
                @endif
                @if($post->read_time)
                    <span>&bull;</span>
                    <span style="margin-left: 8px;">{{ $post->read_time }} min read</span>
                @endif
            </div>

            @if($post->excerpt)
                <div class="excerpt-box">
                    {{ $post->excerpt }}
                </div>
            @endif

            <p>We are excited to share our latest insights and digital engineering updates. Click the button below to read the full article on our platform:</p>

            <div class="action-area">
                <a href="{{ config('app.url') }}/blog/{{ $post->slug }}" class="action-button">Read Full Post</a>
            </div>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} SealTech. All rights reserved.</p>
            <p>You received this email because you subscribed to the SealTech newsletter.</p>
            <p><a href="{{ config('app.url') }}/unsubscribe" class="unsubscribe-link">Unsubscribe from this list</a></p>
        </div>
    </div>
</body>
</html>

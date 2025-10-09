<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Notification</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #4F46E5;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .content {
            background-color: #f9fafb;
            padding: 30px;
            border: 1px solid #e5e7eb;
            border-radius: 0 0 5px 5px;
        }
        .message-box {
            background-color: white;
            padding: 20px;
            border-left: 4px solid #4F46E5;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            color: #6b7280;
            font-size: 12px;
        }
        .user-info {
            background-color: #e0e7ff;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>📧 Notification System</h1>
    </div>
    
    <div class="content">
        <div class="user-info">
            <strong>Email:</strong> {{ $user->email }}<br>
            <strong>Position:</strong> {{ $user->position }}
        </div>

        <h2>You have a new notification</h2>
        
        <div class="message-box">
            <p>{{ $messageContent }}</p>
        </div>

        <p>This is an automated notification from our system.</p>
        
        <div class="footer">
            <p>© {{ date('Y') }} Notification System. All rights reserved.</p>
            <p>Sent on {{ now()->format('F d, Y \a\t h:i A') }}</p>
        </div>
    </div>
</body>
</html>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Password Reset</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            padding: 20px;
            text-align: center;
        }
        .email-container {
            max-width: 500px;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin: auto;
        }
        .btn {
            display: inline-block;
            background-color: #3498db;
            color: #ffffff;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            font-size: 16px;
            margin-top: 20px;
        }
        .footer {
            margin-top: 20px;
            font-size: 12px;
            color: #777;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <h2>Hello, {{ $name }}</h2> <!-- Fix the variable usage -->
        <p>You recently requested to reset your password for your blog account. Click the button below to reset it:</p>
        <a href="{{ $actionlink }}" target="_blank" class="btn">Reset Password</a>
        <p>If you did not request a password reset, please ignore this email or contact support if you have any concerns.</p>
        <p class="footer">This link will expire in 60 minutes.</p>
        <p>Thank you for using our blog!</p>
    </div>
</body>
</html>

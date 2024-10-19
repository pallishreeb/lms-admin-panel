<!DOCTYPE html>
<html>
<head>
    <style>
        .container {
            font-family: Arial, sans-serif;
            color: #333;
            max-width: 600px;
            margin: auto;
            padding: 20px;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
        }
        .header {
            background-color: #007bff;
            color: white;
            padding: 10px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }
        .otp {
            font-size: 24px;
            font-weight: bold;
            margin: 20px 0;
            text-align: center;
        }
        .footer {
            font-size: 12px;
            color: #777;
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Verify Your Account</h2>
        </div>
        <p>Dear user,</p>
        <p>Thank you for choosing our service! To complete your verification, please use the following One-Time Password (OTP):</p>
        <div class="otp">{{ $otp }}</div>
        <p>This code is valid for the next 5 minutes. For your security, please do not share this code with anyone.</p>
        <p>If you did not request this verification, please contact our support team immediately.</p>
        <p>Best regards,<br>The Sohojpara Team</p>
        <div class="footer">
            Need help? Reach out to us at <a href="mailto:support@sohojpora.com">support@sohojpora.com</a>.
        </div>
    </div>
</body>
</html>

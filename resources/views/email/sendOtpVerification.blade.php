<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ellas Email Verification</title>
</head>

<body style="margin:0; padding:0; background-color:#eef2ff; font-family:Arial, Helvetica, sans-serif;">

<table width="100%" cellpadding="0" cellspacing="0" border="0" style="padding:50px 15px;">
    <tr>
        <td align="center">

            <!-- Container -->
            <table width="620" cellpadding="0" cellspacing="0" border="0"
                style="background:#ffffff; border-radius:24px; overflow:hidden;">

                <!-- Top Accent -->
                <tr>
                    <td style="background:#4f46e5; height:8px;"></td>
                </tr>

                <!-- Logo / Brand -->
                <tr>
                    <td align="center" style="padding:40px 40px 20px 40px;">

                        <div style="
                            width:70px;
                            height:70px;
                            border-radius:20px;
                            background:#eef2ff;
                            text-align:center;
                            line-height:70px;
                            font-size:30px;
                            font-weight:bold;
                            color:#4f46e5;
                            margin-bottom:20px;
                        ">
                            E
                        </div>

                        <h1 style="
                            margin:0;
                            font-size:34px;
                            color:#111827;
                            font-weight:700;
                        ">
                            Ellas
                        </h1>

                        <p style="
                            margin-top:10px;
                            color:#6b7280;
                            font-size:16px;
                        ">
                            Verify your email address
                        </p>

                    </td>
                </tr>

                <!-- Main Content -->
                <tr>
                    <td style="padding:10px 50px 40px 50px;">

                        <p style="
                            color:#374151;
                            font-size:16px;
                            line-height:1.8;
                            margin-top:0;
                        ">
                            Hi <strong>{{ $user->name ?? 'Customer' }}</strong>,
                        </p>

                        <p style="
                            color:#6b7280;
                            font-size:16px;
                            line-height:1.8;
                        ">
                            Welcome to <strong>Ellas</strong>. 
                            Use the verification code below to confirm your email 
                            address and activate your account.
                        </p>

                        <!-- OTP Card -->
                        <table width="100%" cellpadding="0" cellspacing="0" border="0"
                            style="
                                margin:35px 0;
                                background:#f9fafb;
                                border:1px solid #e5e7eb;
                                border-radius:18px;
                            ">

                            <tr>
                                <td align="center" style="padding:35px 20px;">

                                    <p style="
                                        margin:0 0 12px 0;
                                        color:#9ca3af;
                                        font-size:14px;
                                        letter-spacing:1px;
                                        text-transform:uppercase;
                                    ">
                                        Your OTP Code
                                    </p>

                                    <div style="
                                        font-size:42px;
                                        font-weight:bold;
                                        letter-spacing:14px;
                                        color:#4f46e5;
                                    ">
                                        {{ $otp }}
                                    </div>

                                </td>
                            </tr>

                        </table>

                        <!-- Info Box -->
                        <table width="100%" cellpadding="0" cellspacing="0" border="0"
                            style="
                                background:#eef2ff;
                                border-radius:14px;
                                margin-bottom:30px;
                            ">

                            <tr>
                                <td style="padding:18px 20px;">

                                    <p style="
                                        margin:0;
                                        color:#4338ca;
                                        font-size:14px;
                                        line-height:1.7;
                                    ">
                                        ⏳ This code will expire in 
                                        <strong>10 minutes</strong>.
                                    </p>

                                </td>
                            </tr>

                        </table>

                        <p style="
                            color:#6b7280;
                            font-size:15px;
                            line-height:1.8;
                        ">
                            If you did not request this verification,
                            please ignore this email.
                        </p>

                        <p style="
                            color:#111827;
                            font-size:15px;
                            line-height:1.8;
                            margin-top:30px;
                        ">
                            — The Ellas Team
                        </p>

                    </td>
                </tr>

                <!-- Footer -->
                <tr>
                    <td align="center"
                        style="
                            background:#f9fafb;
                            padding:30px;
                            border-top:1px solid #e5e7eb;
                        ">

                        <p style="
                            margin:0;
                            color:#9ca3af;
                            font-size:13px;
                            line-height:1.8;
                        ">
                            © {{ date('Y') }} Ellas. All rights reserved.
                        </p>

                        <p style="
                            margin-top:8px;
                            color:#c0c4cc;
                            font-size:12px;
                        ">
                            Secure • Fast • Trusted Commerce
                        </p>

                    </td>
                </tr>

            </table>

        </td>
    </tr>
</table>

</body>
</html>
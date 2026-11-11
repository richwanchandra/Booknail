<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kode OTP Reset Password</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f8fafc; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #f8fafc; padding: 40px 0;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" style="max-width: 500px; background-color: #ffffff; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06); overflow: hidden; border: 1px solid #f1f5f9;">
                    <!-- Header -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #ec4899 0%, #be185d 100%); padding: 28px; text-align: center;">
                            <h1 style="color: #ffffff; margin: 0; font-size: 22px; font-weight: 700; letter-spacing: 0.5px;">{{ config('app.name', 'Booknail') }}</h1>
                        </td>
                    </tr>
                    
                    <!-- Content -->
                    <tr>
                        <td style="padding: 36px 32px; text-align: center;">
                            <h2 style="color: #1e293b; font-size: 20px; font-weight: 600; margin-top: 0; margin-bottom: 12px;">Reset Password Akun Anda</h2>
                            <p style="color: #64748b; font-size: 14px; line-height: 1.6; margin-top: 0; margin-bottom: 28px;">
                                Gunakan kode OTP di bawah ini untuk memverifikasi permintaan reset kata sandi Anda:
                            </p>
                            
                            <!-- OTP Box -->
                            <div style="background-color: #fdf2f8; border: 2px dashed #f472b6; border-radius: 10px; padding: 20px; margin-bottom: 28px;">
                                <span style="font-family: 'Courier New', Courier, monospace; font-size: 36px; font-weight: 700; letter-spacing: 8px; color: #be185d; display: inline-block;">{{ $otp }}</span>
                            </div>
                            
                            <p style="color: #64748b; font-size: 13px; margin: 0 0 8px 0;">
                                Kode ini hanya berlaku selama <strong style="color: #be185d;">10 menit</strong>.
                            </p>
                            <p style="color: #94a3b8; font-size: 12px; margin: 0;">
                                Jangan berikan kode ini kepada siapapun demi keamanan akun Anda.
                            </p>
                        </td>
                    </tr>
                    
                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f8fafc; padding: 20px 32px; text-align: center; border-top: 1px solid #f1f5f9;">
                            <p style="color: #94a3b8; font-size: 12px; margin: 0;">
                                &copy; {{ date('Y') }} {{ config('app.name', 'Booknail') }}. All rights reserved.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>

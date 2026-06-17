<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kode Verifikasi Ubah Password</title>
</head>
<body style="margin:0;padding:0;background-color:#f4f4f5;font-family:Arial,sans-serif;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f4f4f5;padding:40px 0;">
        <tr>
            <td align="center">
                <table width="480" cellpadding="0" cellspacing="0" style="background-color:#ffffff;border-radius:12px;overflow:hidden;">
                    <tr>
                        <td style="padding:40px 30px;text-align:center;background:linear-gradient(135deg,#003366,#0055a5);">
                            <h1 style="color:#ffffff;margin:0;font-size:24px;">PAS Market</h1>
                            <p style="color:#e0e0e0;margin:8px 0 0;font-size:14px;">Kode Verifikasi Ubah Password</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:40px 30px;">
                            <h2 style="color:#333;font-size:18px;margin:0 0 16px;">Halo, {{ $customer->full_name }}!</h2>
                            <p style="color:#666;font-size:14px;line-height:1.6;margin:0 0 20px;">
                                Kami menerima permintaan untuk mengubah password akun Anda. Gunakan kode verifikasi berikut:
                            </p>
                            <div style="text-align:center;margin:30px 0;">
                                <div style="display:inline-block;background:#f0f4ff;border-radius:10px;padding:16px 32px;letter-spacing:8px;font-size:32px;font-weight:bold;color:#003366;">
                                    {{ $code }}
                                </div>
                            </div>
                            <p style="color:#999;font-size:12px;line-height:1.5;margin:20px 0 0;">
                                Kode ini berlaku selama 60 menit. Jika Anda tidak meminta perubahan password, abaikan email ini.
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:20px 30px;text-align:center;border-top:1px solid #eee;">
                            <p style="color:#999;font-size:12px;margin:0;">&copy; {{ date('Y') }} PAS Market. All rights reserved.</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>

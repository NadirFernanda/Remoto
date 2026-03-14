<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Código de Verificação</title>
</head>
<body style="margin:0;padding:0;background:#f1f5f9;font-family:'Segoe UI',Arial,sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#f1f5f9;padding:40px 0;">
    <tr>
        <td align="center">
            <table width="600" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:12px;overflow:hidden;box-shadow:0 2px 12px rgba(0,0,0,.08);">
                {{-- Header --}}
                <tr>
                    <td style="background:#0f172a;padding:32px 40px;text-align:center;">
                        <img src="{{ asset('img/logo.png') }}" alt="{{ config('app.name') }}" style="height:48px;object-fit:contain;">
                    </td>
                </tr>
                {{-- Body --}}
                <tr>
                    <td style="padding:40px 40px 32px;">
                        <h1 style="color:#0f172a;font-size:22px;font-weight:700;margin:0 0 16px;">Verificação de acesso</h1>
                        <p style="color:#334155;font-size:16px;line-height:1.7;margin:0 0 24px;">
                            Olá, <strong>{{ $user->name }}</strong>! Use o código abaixo para confirmar o seu acesso.
                            Este código é válido por <strong>10 minutos</strong>.
                        </p>
                        {{-- OTP box --}}
                        <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:24px;">
                            <tr>
                                <td style="background:#f0f9ff;border:2px dashed #00baff;border-radius:10px;padding:28px;text-align:center;">
                                    <span style="font-size:42px;font-weight:800;letter-spacing:12px;color:#0f172a;font-family:'Courier New',monospace;">{{ $otp }}</span>
                                </td>
                            </tr>
                        </table>
                        <p style="color:#ef4444;font-size:14px;line-height:1.6;margin:0 0 16px;">
                            ⚠️ Nunca partilhe este código com ninguém. A nossa equipa jamais solicitará o seu código de verificação.
                        </p>
                        <p style="color:#64748b;font-size:14px;line-height:1.6;margin:0;">
                            Se não foi você a solicitar este código, pode ignorar este e-mail com segurança.
                        </p>
                    </td>
                </tr>
                {{-- Footer --}}
                <tr>
                    <td style="background:#f8fafc;border-top:1px solid #e2e8f0;padding:24px 40px;text-align:center;">
                        <p style="color:#94a3b8;font-size:13px;margin:0;">
                            © {{ date('Y') }} {{ config('app.name') }} · Todos os direitos reservados
                        </p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>

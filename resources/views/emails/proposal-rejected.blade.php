<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Atualização sobre o seu projeto</title>
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
                        <h1 style="color:#0f172a;font-size:22px;font-weight:700;margin:0 0 16px;">Atualização sobre o seu projeto</h1>
                        <p style="color:#334155;font-size:16px;line-height:1.7;margin:0 0 16px;">
                            Olá, <strong>{{ $freelancer->name }}</strong>,
                        </p>
                        {{-- Project name badge --}}
                        <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:20px;">
                            <tr>
                                <td style="background:#f8fafc;border-left:4px solid #00baff;border-radius:4px;padding:14px 18px;">
                                    <span style="font-size:13px;color:#64748b;display:block;margin-bottom:4px;">Projeto</span>
                                    <span style="font-size:16px;font-weight:700;color:#0f172a;">{{ $service->titulo }}</span>
                                </td>
                            </tr>
                        </table>
                        <p style="color:#334155;font-size:16px;line-height:1.7;margin:0 0 24px;">
                            {{ $mensagem }}
                        </p>
                        <p style="color:#334155;font-size:15px;line-height:1.7;margin:0 0 24px;">
                            Não desanime! Há muitos outros projetos disponíveis na plataforma que podem ser uma excelente oportunidade para você.
                        </p>
                        <table cellpadding="0" cellspacing="0" style="margin-bottom:24px;">
                            <tr>
                                <td style="background:#00baff;border-radius:8px;">
                                    <a href="{{ url('/freelancer/projetos') }}" style="display:inline-block;padding:14px 28px;color:#ffffff;font-size:15px;font-weight:700;text-decoration:none;">
                                        Ver Outros Projetos →
                                    </a>
                                </td>
                            </tr>
                        </table>
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

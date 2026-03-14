<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página não encontrada — 24 Horas</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;900&display=swap" rel="stylesheet">
    <link rel="icon" href="{{ asset('img/logo.png') }}" sizes="any">
    @vite(['resources/css/app.css'])
    <style>
        body { margin:0; min-height:100vh; display:flex; flex-direction:column; align-items:center; justify-content:center; background:#021018; font-family:'Inter',system-ui,sans-serif; padding:1.5rem; }
        .err-box { text-align:center; max-width:460px; width:100%; }
        .err-icon { font-size:3.5rem; margin-bottom:1.25rem; }
        .err-code { font-size:5rem; font-weight:900; color:#00baff; line-height:1; margin:0 0 .5rem; }
        .err-title { font-size:1.85rem; font-weight:900; color:#f1f5f9; margin:0 0 .75rem; }
        .err-msg { color:#94a3b8; font-size:1rem; line-height:1.6; margin:0 0 2rem; }
        .err-btn-primary { display:inline-block; background:#00baff; color:#021018; padding:.75rem 2rem; border-radius:10px; font-weight:700; font-size:1rem; text-decoration:none; transition:background .2s; }
        .err-btn-primary:hover { background:#0099dd; }
        .err-btn-home { display:inline-block; margin-top:1rem; color:#64748b; font-size:.875rem; text-decoration:none; }
        .err-btn-home:hover { color:#94a3b8; }
    </style>
</head>
<body>
    <div class="err-box">
        <div class="err-icon">🔍</div>
        <div class="err-code">404</div>
        <h1 class="err-title">Página não encontrada</h1>
        <p class="err-msg">
            A página que procura não existe ou foi movida.<br>
            Verifique o endereço ou navegue a partir da página inicial.
        </p>
        <a href="/" class="err-btn-primary">Ir para o início</a>
        <br>
        <a href="javascript:history.back()" class="err-btn-home">Voltar à página anterior</a>
    </div>
</body>
</html>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sessão expirada — 24 Horas</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;900&display=swap" rel="stylesheet">
    <link rel="icon" href="{{ asset('img/logo.png') }}" sizes="any">
    @vite(['resources/css/app.css'])
    <style>
        body { margin:0; min-height:100vh; display:flex; flex-direction:column; align-items:center; justify-content:center; background:#021018; font-family:'Inter',system-ui,sans-serif; padding:1.5rem; }
        .err-box { text-align:center; max-width:460px; width:100%; }
        .err-icon { font-size:3.5rem; margin-bottom:1.25rem; }
        .err-title { font-size:1.85rem; font-weight:900; color:#f1f5f9; margin:0 0 .75rem; }
        .err-msg { color:#94a3b8; font-size:1rem; line-height:1.6; margin:0 0 2rem; }
        .err-btn-primary { display:inline-block; background:#00baff; color:#021018; padding:.75rem 2rem; border-radius:10px; font-weight:700; font-size:1rem; text-decoration:none; transition:background .2s; }
        .err-btn-primary:hover { background:#0099dd; }
        .err-btn-home { display:inline-block; margin-top:1rem; color:#64748b; font-size:.875rem; text-decoration:none; }
        .err-btn-home:hover { color:#94a3b8; }
        .err-countdown { color:#475569; font-size:.8rem; margin-top:1.5rem; }
    </style>
</head>
<body>
    <div class="err-box">
        <div class="err-icon">⏱️</div>
        <h1 class="err-title">Sessão expirada</h1>
        <p class="err-msg">
            A sua sessão expirou por inactividade. Por favor, recarregue a página para continuar.
        </p>
        <a href="javascript:void(0)"
           onclick="window.location.href = (document.referrer && document.referrer !== window.location.href) ? document.referrer : '/';"
           class="err-btn-primary">
            Recarregar página
        </a>
        <br>
        <a href="/" class="err-btn-home">Ir para o início</a>
        <p class="err-countdown" id="countdown">A redirecionar automaticamente em <span id="secs">8</span> segundos...</p>
    </div>
    <script>
        var secs = 8;
        var el = document.getElementById('secs');
        var timer = setInterval(function() {
            secs--;
            if (el) el.textContent = secs;
            if (secs <= 0) {
                clearInterval(timer);
                window.location.href = (document.referrer && document.referrer !== window.location.href) ? document.referrer : '/';
            }
        }, 1000);
    </script>
</body>
</html>

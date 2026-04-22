<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuração Inicial — Software Jurídico</title>

    <script>(function(){var t=localStorage.getItem('software-juridico-theme')||(window.matchMedia('(prefers-color-scheme:dark)').matches?'dark':'light');document.documentElement.setAttribute('data-theme',t);}());</script>

    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#1a3a5c">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body style="min-height:100vh; background: var(--bg-secondary, #f0f4f8); display:flex; flex-direction:column; align-items:center; justify-content:center; font-family: system-ui, sans-serif;">

    <div style="width:100%; max-width:640px; padding:1.5rem;">
        <div style="text-align:center; margin-bottom:2rem;">
            <div style="font-size:1.75rem; font-weight:700; color:var(--text-primary,#1a3a5c);">⚖️ Software Jurídico</div>
            <div style="font-size:.875rem; color:var(--text-muted,#6b7280); margin-top:.25rem;">Configuração inicial do escritório</div>
        </div>

        {{ $slot }}
    </div>

    @livewireScripts
</body>
</html>

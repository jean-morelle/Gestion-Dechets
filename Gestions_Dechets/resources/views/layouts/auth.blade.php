<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#0f3b2a">

    <title>@hasSection('title')@yield('title') · @endif{{ config('app.name') }}</title>

    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="{{ $vendorAsset('bootstrap_css') }}" rel="stylesheet">
    <link href="{{ $vendorAsset('fontawesome_css') }}" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}?v={{ filemtime(public_path('css/app.css')) }}" rel="stylesheet">
</head>
<body>
    <div class="auth-wrapper">
        <aside class="auth-aside">
            <div class="d-flex align-items-center gap-2 fw-bold fs-5">
                <span class="app-brand-mark"><img src="{{ asset('images/collectplus-embleme.png') }}" alt=""></span>
                CollectPlus Togo
            </div>

            <div>
                <h2>Une ville plus propre, ensemble.</h2>
                <ul>
                    <li><i class="fas fa-check" aria-hidden="true"></i> Signalez un dépôt sauvage en quelques secondes, photo à l'appui.</li>
                    <li><i class="fas fa-check" aria-hidden="true"></i> Consultez les jours de passage dans votre quartier.</li>
                    <li><i class="fas fa-check" aria-hidden="true"></i> Suivez le traitement de vos demandes jusqu'à leur résolution.</li>
                </ul>
            </div>

            <small class="text-white-50">
                Service de gestion des déchets — Lomé ·
                <a href="{{ route('legal.confidentialite') }}" class="text-white-50">Confidentialité</a> ·
                <a href="{{ route('legal.conditions') }}" class="text-white-50">Conditions d’utilisation</a>
            </small>
        </aside>

        <main class="auth-panel">
            @yield('content')
        </main>
    </div>

    <script src="{{ $vendorAsset('bootstrap_js') }}"></script>
    <script>
        document.querySelectorAll('.toggle-password').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var input = document.getElementById(btn.dataset.target);
                var visible = input.type === 'text';
                input.type = visible ? 'password' : 'text';
                btn.setAttribute('aria-label', visible ? 'Afficher le mot de passe' : 'Masquer le mot de passe');
                btn.querySelector('i').className = visible ? 'far fa-eye' : 'far fa-eye-slash';
            });
        });

        document.querySelectorAll('form').forEach(function (form) {
            form.addEventListener('submit', function () {
                var btn = form.querySelector('button[type="submit"]');
                if (btn) {
                    setTimeout(function () { btn.disabled = true; }, 0);
                }
            });
        });
    </script>
    @yield('scripts')
</body>
</html>

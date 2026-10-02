<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#0f3b2a">
    <title>@yield('title') · {{ config('app.name') }}</title>
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="{{ $vendorAsset('bootstrap_css') }}" rel="stylesheet">
    <link href="{{ $vendorAsset('fontawesome_css') }}" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}?v={{ filemtime(public_path('css/app.css')) }}" rel="stylesheet">
</head>
<body>
    <header class="border-bottom bg-body">
        <div class="container page-texte d-flex align-items-center justify-content-between py-3">
            <a href="{{ url('/') }}" class="d-flex align-items-center gap-2 fw-bold text-decoration-none text-body">
                <span class="app-brand-mark"><img src="{{ asset('images/collectplus-embleme.png') }}" alt=""></span>
                {{ config('app.name') }}
            </a>
            @auth
                <a href="{{ route('dashboard') }}" class="small">Retour à mon espace</a>
            @else
                <a href="{{ route('login') }}" class="small">Se connecter</a>
            @endauth
        </div>
    </header>

    <main class="container page-texte py-4 py-md-5">
        @yield('content')
    </main>

    <footer class="container page-texte pb-5 small text-body-secondary">
        <a href="{{ route('legal.confidentialite') }}">Confidentialité</a> ·
        <a href="{{ route('legal.conditions') }}">Conditions d’utilisation</a>
    </footer>
</body>
</html>

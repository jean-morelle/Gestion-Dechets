@php
    $theme = auth()->user()?->theme ?: 'light';
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="{{ $theme === 'dark' ? 'dark' : 'light' }}">
<head>
    <meta charset="utf-8">
    @if($theme === 'auto')
        {{-- Thème « Automatique » : appliqué avant l'affichage pour éviter un flash blanc --}}
        <script>
            (function () {
                var mq = window.matchMedia('(prefers-color-scheme: dark)');
                var appliquer = function () { document.documentElement.setAttribute('data-bs-theme', mq.matches ? 'dark' : 'light'); };
                appliquer();
                mq.addEventListener('change', appliquer);
            })();
        </script>
    @endif
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
    @stack('styles')
</head>
<body>
    <a href="#contenu" class="skip-link">Aller au contenu</a>

    @php
        $user = auth()->user();
        $role = $user->role;
    @endphp

    <aside class="offcanvas-lg offcanvas-start app-sidebar" tabindex="-1" id="appSidebar" aria-label="Menu principal">
        <div class="app-sidebar-inner">
            <div class="d-flex align-items-center">
                <a href="{{ route('dashboard') }}" class="app-brand flex-grow-1">
                    <span class="app-brand-mark"><img src="{{ asset('images/collectplus-embleme.png') }}" alt=""></span>
                    CollectPlus Togo
                </a>
                <button type="button" class="btn-close btn-close-white d-lg-none me-3" data-bs-dismiss="offcanvas" data-bs-target="#appSidebar" aria-label="Fermer le menu"></button>
            </div>

            <nav class="app-nav">
                @if($role === 'citoyen')
                    <a class="nav-link {{ request()->routeIs('citoyen.dashboard') ? 'active' : '' }}" href="{{ route('citoyen.dashboard') }}">
                        <i class="fas fa-house" aria-hidden="true"></i> Tableau de bord
                    </a>

                    <div class="app-nav-label">Mes démarches</div>
                    <a class="nav-link {{ request()->routeIs('citoyen.signalements.*') ? 'active' : '' }}" href="{{ route('citoyen.signalements.index') }}">
                        <i class="fas fa-triangle-exclamation" aria-hidden="true"></i> Signalements
                    </a>
                    <a class="nav-link {{ request()->routeIs('citoyen.demandes-collecte.*') ? 'active' : '' }}" href="{{ route('citoyen.demandes-collecte.index') }}">
                        <i class="fas fa-truck" aria-hidden="true"></i> Demandes de collecte
                    </a>
                    <a class="nav-link {{ request()->routeIs('citoyen.plaintes.*') ? 'active' : '' }}" href="{{ route('citoyen.plaintes.index') }}">
                        <i class="fas fa-comment-dots" aria-hidden="true"></i> Plaintes
                    </a>

                    <div class="app-nav-label">S'informer</div>
                    <a class="nav-link {{ request()->routeIs('citoyen.calendrier.*') ? 'active' : '' }}" href="{{ route('citoyen.calendrier.index') }}">
                        <i class="fas fa-calendar-days" aria-hidden="true"></i> Calendrier de collecte
                    </a>
                    <a class="nav-link {{ request()->routeIs('citoyen.campagnes.*') ? 'active' : '' }}" href="{{ route('citoyen.campagnes.index') }}">
                        <i class="fas fa-bullhorn" aria-hidden="true"></i> Campagnes
                    </a>

                    <div class="app-nav-label">Communication</div>
                    <a class="nav-link {{ request()->routeIs('citoyen.notifications.*') ? 'active' : '' }}" href="{{ route('citoyen.notifications.index') }}">
                        <i class="fas fa-bell" aria-hidden="true"></i> Notifications
                        @if($notificationsNonLues > 0)<span class="badge rounded-pill text-bg-warning">{{ $notificationsNonLues }}</span>@endif
                    </a>
                @elseif($role === 'collecteur')
                    <a class="nav-link {{ request()->routeIs('collecteur.dashboard') ? 'active' : '' }}" href="{{ route('collecteur.dashboard') }}">
                        <i class="fas fa-house" aria-hidden="true"></i> Tableau de bord
                    </a>

                    <div class="app-nav-label">Terrain</div>
                    <a class="nav-link {{ request()->routeIs('collecteur.itineraires.*') ? 'active' : '' }}" href="{{ route('collecteur.itineraires.index') }}">
                        <i class="fas fa-route" aria-hidden="true"></i> Mes tournées
                    </a>
                    <a class="nav-link {{ request()->routeIs('collecteur.collectes.*') ? 'active' : '' }}" href="{{ route('collecteur.collectes.index') }}">
                        <i class="fas fa-dumpster" aria-hidden="true"></i> Collectes
                    </a>
                    <a class="nav-link {{ request()->routeIs('collecteur.incidents.*') ? 'active' : '' }}" href="{{ route('collecteur.incidents.index') }}">
                        <i class="fas fa-triangle-exclamation" aria-hidden="true"></i> Incidents
                    </a>

                    <div class="app-nav-label">Communication</div>
                    <a class="nav-link {{ request()->routeIs('collecteur.campagnes.*') ? 'active' : '' }}" href="{{ route('collecteur.campagnes.index') }}">
                        <i class="fas fa-bullhorn" aria-hidden="true"></i> Campagnes
                    </a>
                    <a class="nav-link {{ request()->routeIs('collecteur.notifications.*') ? 'active' : '' }}" href="{{ route('collecteur.notifications.index') }}">
                        <i class="fas fa-bell" aria-hidden="true"></i> Notifications
                        @if($notificationsNonLues > 0)<span class="badge rounded-pill text-bg-warning">{{ $notificationsNonLues }}</span>@endif
                    </a>
                @elseif($role === 'admin')
                    <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                        <i class="fas fa-gauge-high" aria-hidden="true"></i> Tableau de bord
                    </a>
                    <a class="nav-link {{ request()->routeIs('admin.carte') ? 'active' : '' }}" href="{{ route('admin.carte') }}">
                        <i class="fas fa-map" aria-hidden="true"></i> Carte de la commune
                    </a>
                    <a class="nav-link {{ request()->routeIs('admin.rapports*') ? 'active' : '' }}" href="{{ route('admin.rapports') }}">
                        <i class="fas fa-chart-line" aria-hidden="true"></i> Rapports
                    </a>

                    <div class="app-nav-label">Demandes citoyennes</div>
                    <a class="nav-link {{ request()->routeIs('admin.signalements.*') ? 'active' : '' }}" href="{{ route('admin.signalements.index') }}">
                        <i class="fas fa-triangle-exclamation" aria-hidden="true"></i> Signalements
                        @if($signalementsEnAttente > 0)<span class="badge rounded-pill text-bg-warning">{{ $signalementsEnAttente }}</span>@endif
                    </a>
                    <a class="nav-link {{ request()->routeIs('admin.demandes.*') ? 'active' : '' }}" href="{{ route('admin.demandes.index') }}">
                        <i class="fas fa-truck-ramp-box" aria-hidden="true"></i> Demandes de collecte
                        @if($demandesEnAttente > 0)<span class="badge rounded-pill text-bg-warning">{{ $demandesEnAttente }}</span>@endif
                    </a>
                    <a class="nav-link {{ request()->routeIs('admin.plaintes.*') ? 'active' : '' }}" href="{{ route('admin.plaintes.index') }}">
                        <i class="fas fa-comment-dots" aria-hidden="true"></i> Plaintes
                    </a>

                    <div class="app-nav-label">Organisation</div>
                    <a class="nav-link {{ request()->routeIs('admin.itineraires.*') ? 'active' : '' }}" href="{{ route('admin.itineraires.index') }}">
                        <i class="fas fa-route" aria-hidden="true"></i> Tournées
                    </a>
                    <a class="nav-link {{ request()->routeIs('admin.points.*') ? 'active' : '' }}" href="{{ route('admin.points.index') }}">
                        <i class="fas fa-map-location-dot" aria-hidden="true"></i> Points de collecte
                    </a>
                    <a class="nav-link {{ request()->routeIs('admin.incidents.*') ? 'active' : '' }}" href="{{ route('admin.incidents.index') }}">
                        <i class="fas fa-truck-medical" aria-hidden="true"></i> Incidents
                        @if($incidentsOuverts > 0)<span class="badge rounded-pill text-bg-danger">{{ $incidentsOuverts }}</span>@endif
                    </a>
                    <a class="nav-link {{ request()->routeIs('admin.calendrier.*') ? 'active' : '' }}" href="{{ route('admin.calendrier.index') }}">
                        <i class="fas fa-calendar-days" aria-hidden="true"></i> Calendrier
                    </a>
                    <a class="nav-link {{ request()->routeIs('admin.utilisateurs.*') ? 'active' : '' }}" href="{{ route('admin.utilisateurs.index') }}">
                        <i class="fas fa-users" aria-hidden="true"></i> Utilisateurs
                    </a>

                    <div class="app-nav-label">Communication</div>
                    <a class="nav-link {{ request()->routeIs('admin.campagnes.*') ? 'active' : '' }}" href="{{ route('admin.campagnes.index') }}">
                        <i class="fas fa-bullhorn" aria-hidden="true"></i> Campagnes
                    </a>
                    <a class="nav-link {{ request()->routeIs('admin.notifications.*') ? 'active' : '' }}" href="{{ route('admin.notifications.index') }}">
                        <i class="fas fa-paper-plane" aria-hidden="true"></i> Notifications
                    </a>
                @endif

                <a class="nav-link {{ request()->routeIs('messages.*') ? 'active' : '' }}" href="{{ route('messages.index') }}">
                    <i class="fas fa-envelope" aria-hidden="true"></i> Messagerie
                    @if($messagesNonLus > 0)<span class="badge rounded-pill text-bg-warning">{{ $messagesNonLus }}</span>@endif
                </a>
            </nav>

            <div class="app-sidebar-footer">
                Service de gestion des déchets — Lomé
                <div class="mt-1">
                    <a href="{{ route('legal.confidentialite') }}" class="text-reset">Confidentialité</a> ·
                    <a href="{{ route('legal.conditions') }}" class="text-reset">Conditions</a>
                </div>
            </div>
        </div>
    </aside>

    <div class="app-main">
        <header class="app-topbar">
            <button class="btn-icon d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#appSidebar" aria-controls="appSidebar" aria-label="Ouvrir le menu">
                <i class="fas fa-bars" aria-hidden="true"></i>
            </button>

            <div class="ms-auto d-flex align-items-center gap-1">
                @yield('actions')

                @if(in_array($role, ['citoyen', 'collecteur', 'admin']))
                    <a href="{{ route($role . '.notifications.index') }}" class="btn-icon" aria-label="Notifications{{ $notificationsNonLues ? ' (' . $notificationsNonLues . ' non lues)' : '' }}">
                        <i class="far fa-bell" aria-hidden="true"></i>
                        @if($notificationsNonLues > 0)<span class="badge rounded-pill text-bg-danger">{{ $notificationsNonLues > 9 ? '9+' : $notificationsNonLues }}</span>@endif
                    </a>
                @endif

                <div class="dropdown">
                    <button class="app-user-btn" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <x-avatar :user="$user" />
                        <span class="d-none d-sm-block text-start lh-sm">
                            <span class="d-block fw-semibold small">{{ $user->name }}</span>
                            <span class="d-block text-body-secondary" style="font-size: .75rem">{{ ['citoyen' => 'Citoyen', 'collecteur' => 'Collecteur', 'admin' => 'Administrateur'][$role] ?? ucfirst($role) }}</span>
                        </span>
                        <i class="fas fa-chevron-down small text-body-secondary d-none d-sm-inline" aria-hidden="true"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                        <li class="px-3 py-2 d-sm-none">
                            <div class="fw-semibold">{{ $user->name }}</div>
                            <div class="small text-body-secondary">{{ $user->email }}</div>
                        </li>
                        <li class="d-sm-none"><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="{{ route('profile.edit') }}"><i class="far fa-user me-2" aria-hidden="true"></i>Mon profil</a></li>
                        <li><a class="dropdown-item" href="{{ route('settings.index') }}"><i class="fas fa-gear me-2" aria-hidden="true"></i>Paramètres</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger"><i class="fas fa-arrow-right-from-bracket me-2" aria-hidden="true"></i>Se déconnecter</button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </header>

        <main class="app-content" id="contenu">
            <div class="flash-stack">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="status" data-autoclose>
                        <i class="fas fa-circle-check mt-1" aria-hidden="true"></i>
                        <div>{{ session('success') }}</div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
                    </div>
                @endif

                @if (session('info'))
                    <div class="alert alert-info alert-dismissible fade show" role="status">
                        <i class="fas fa-circle-info mt-1" aria-hidden="true"></i>
                        <div>{{ session('info') }}</div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
                    </div>
                @endif

                @if (session('warning'))
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <i class="fas fa-triangle-exclamation mt-1" aria-hidden="true"></i>
                        <div>{{ session('warning') }}</div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-circle-exclamation mt-1" aria-hidden="true"></i>
                        <div>{{ session('error') }}</div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-circle-exclamation mt-1" aria-hidden="true"></i>
                        <div>
                            <strong>Veuillez corriger les points suivants :</strong>
                            <ul class="mb-0 mt-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
                    </div>
                @endif
            </div>

            @yield('content')
        </main>
    </div>

    <script src="{{ $vendorAsset('bootstrap_js') }}"></script>
    <script src="{{ asset('js/photos.js') }}?v={{ filemtime(public_path('js/photos.js')) }}"></script>
    <script>
        // Bouton œil des champs mot de passe (même comportement que sur la connexion)
        document.querySelectorAll('.toggle-password').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var input = document.getElementById(btn.dataset.target);
                var visible = input.type === 'text';
                input.type = visible ? 'password' : 'text';
                btn.setAttribute('aria-label', visible ? 'Afficher le mot de passe' : 'Masquer le mot de passe');
                btn.querySelector('i').className = visible ? 'far fa-eye' : 'far fa-eye-slash';
            });
        });

        // Seuls les messages de confirmation disparaissent seuls ; les erreurs restent affichées
        document.querySelectorAll('.alert[data-autoclose]').forEach(function (el) {
            setTimeout(function () { bootstrap.Alert.getOrCreateInstance(el).close(); }, 6000);
        });

        // Évite les doubles envois de formulaire
        document.addEventListener('submit', function (e) {
            var btn = e.target.querySelector('button[type="submit"]:not([data-no-lock])');
            if (btn && !e.defaultPrevented) {
                setTimeout(function () { btn.disabled = true; }, 0);
            }
        });
        window.addEventListener('pageshow', function () {
            document.querySelectorAll('form button[type="submit"]:disabled').forEach(function (b) { b.disabled = false; });
        });
    </script>
    @yield('scripts')
    @stack('scripts')
</body>
</html>

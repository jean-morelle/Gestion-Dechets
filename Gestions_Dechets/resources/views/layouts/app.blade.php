<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <style>
        /* Thèmes */
        .theme-light {
            background-color: #ffffff;
            color: #333333;
        }
        
        .theme-dark {
            background-color: #1a1a1a;
            color: #ffffff;
        }
        
        .theme-dark .card {
            background-color: #2d2d2d;
            border-color: #444444;
        }
        
        .theme-dark .card-header {
            background-color: #3d3d3d;
            border-bottom-color: #444444;
        }
        
        .theme-dark .form-control,
        .theme-dark .form-select {
            background-color: #3d3d3d;
            border-color: #555555;
            color: #ffffff;
        }
        
        .theme-dark .form-control:focus,
        .theme-dark .form-select:focus {
            background-color: #3d3d3d;
            border-color: #007bff;
            color: #ffffff;
        }
        
        .theme-dark .btn-outline-primary {
            color: #007bff;
            border-color: #007bff;
        }
        
        .theme-dark .btn-outline-primary:hover {
            background-color: #007bff;
            color: #ffffff;
        }
        
        .theme-dark .text-muted {
            color: #aaaaaa !important;
        }
        
        .theme-dark .alert-info {
            background-color: #1e3a5f;
            border-color: #2c5aa0;
            color: #ffffff;
        }
        
        .theme-dark .alert-success {
            background-color: #1e4d2b;
            border-color: #2d5a3d;
            color: #ffffff;
        }
        
        .theme-dark .alert-danger {
            background-color: #5c1a1a;
            border-color: #7d2a2a;
            color: #ffffff;
        }
        
        .theme-dark .modal-content {
            background-color: #2d2d2d;
            color: #ffffff;
        }
        
        .theme-dark .modal-header {
            border-bottom-color: #444444;
        }
        
        .theme-dark .modal-footer {
            border-top-color: #444444;
        }
        
        .theme-dark .btn-close {
            filter: invert(1);
        }
        
        /* Styles supplémentaires pour le thème sombre */
        .theme-dark .card-body {
            color: #e9ecef;
        }
        
        .theme-dark h1, .theme-dark h2, .theme-dark h3, 
        .theme-dark h4, .theme-dark h5, .theme-dark h6 {
            color: #ffffff;
        }
        
        .theme-dark p {
            color: #e9ecef;
        }
        
        .theme-dark .form-label {
            color: #e9ecef;
        }
        
        .theme-dark .form-text {
            color: #adb5bd;
        }
        
        .theme-dark .table {
            color: #e9ecef;
            border-color: #444444;
        }
        
        .theme-dark .table thead {
            background-color: #3d3d3d;
            color: #ffffff;
        }
        
        .theme-dark .table tbody tr {
            border-color: #444444;
        }
        
        .theme-dark .table tbody tr:hover {
            background-color: #3d3d3d;
        }
        
        .theme-dark .border,
        .theme-dark .border-bottom,
        .theme-dark .border-top {
            border-color: #444444 !important;
        }
        
        .theme-dark .list-group-item {
            background-color: #2d2d2d;
            border-color: #444444;
            color: #e9ecef;
        }
        
        .theme-dark .badge {
            background-color: #3d3d3d;
            color: #ffffff;
        }
        
        .theme-dark .badge.bg-success {
            background-color: #198754 !important;
        }
        
        .theme-dark .badge.bg-warning {
            background-color: #ffc107 !important;
            color: #000000 !important;
        }
        
        .theme-dark .badge.bg-danger {
            background-color: #dc3545 !important;
        }
        
        .theme-dark .badge.bg-info {
            background-color: #0dcaf0 !important;
            color: #000000 !important;
        }
        
        .theme-dark .dropdown-menu {
            background-color: #2d2d2d;
            border-color: #444444;
        }
        
        .theme-dark .dropdown-item {
            color: #e9ecef;
        }
        
        .theme-dark .dropdown-item:hover {
            background-color: #3d3d3d;
            color: #ffffff;
        }
        
        .theme-dark .nav-link {
            color: #e9ecef;
        }
        
        .theme-dark .nav-tabs .nav-link {
            color: #adb5bd;
            border-color: #444444;
        }
        
        .theme-dark .nav-tabs .nav-link.active {
            background-color: #2d2d2d;
            border-color: #444444 #444444 #2d2d2d;
            color: #ffffff;
        }
        
        .theme-dark input::placeholder,
        .theme-dark textarea::placeholder {
            color: #6c757d;
        }
        
        .theme-dark a {
            color: #66b3ff;
        }
        
        .theme-dark a:hover {
            color: #99ccff;
        }
        
        .theme-dark .page-title-box {
            color: #ffffff;
        }
        
        .theme-dark small {
            color: #adb5bd;
        }
        
        .theme-dark .alert-warning {
            background-color: #664d03;
            border-color: #997404;
            color: #ffecb5;
        }
        
        /* Sidebar améliorée */
        .sidebar {
            overflow-y: auto;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
        }
        
        .sidebar .position-sticky {
            height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        .sidebar ul {
            flex: 1;
            overflow-y: auto;
            padding-bottom: 20px;
        }
        
        .nav-link {
            transition: all 0.3s ease;
            border-radius: 8px;
            margin: 2px 8px;
        }
        
        .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.1);
            transform: translateX(5px);
        }
        
        .nav-link.active {
            background-color: rgba(255, 255, 255, 0.2);
            font-weight: 600;
        }
        
        .nav-link i {
            width: 20px;
            text-align: center;
        }
        
        
        /* Scrollbar personnalisée */
        .sidebar::-webkit-scrollbar {
            width: 6px;
        }
        
        .sidebar::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
        }
        
        .sidebar::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.3);
            border-radius: 3px;
        }
        
        .sidebar::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.5);
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-3 col-lg-2 d-md-block sidebar collapse" style="background-color: #1e3a8a; min-height: 100vh;">
                <div class="position-sticky pt-3">
                    <div class="text-center mb-4">
                        <h6 style="color: white; font-weight: bold;">
                            <i class="fas fa-recycle me-2"></i>
                            CollectPlusTogo
                        </h6>
                    </div>
                    
                    <ul class="nav flex-column">
                        @if(auth()->user()->role === 'citoyen')
                            <!-- Menu Citoyen -->
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('citoyen.dashboard') ? 'active' : '' }}" 
                                   href="{{ route('citoyen.dashboard') }}" 
                                   style="color: white; padding: 12px 16px;">
                                    <i class="fas fa-home me-2"></i> Tableau de bord
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('citoyen.signalements.*') ? 'active' : '' }}" 
                                   href="{{ route('citoyen.signalements.index') }}" 
                                   style="color: white; padding: 12px 16px;">
                                    <i class="fas fa-exclamation-triangle me-2"></i> Mes Signalements
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('citoyen.demandes-collecte.*') ? 'active' : '' }}" 
                                   href="{{ route('citoyen.demandes-collecte.index') }}" 
                                   style="color: white; padding: 12px 16px;">
                                    <i class="fas fa-truck me-2"></i> Demandes de Collecte
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('citoyen.plaintes.*') ? 'active' : '' }}" 
                                   href="{{ route('citoyen.plaintes.index') }}" 
                                   style="color: white; padding: 12px 16px;">
                                    <i class="fas fa-comment me-2"></i> Mes Plaintes
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('citoyen.calendrier.*') ? 'active' : '' }}" 
                                   href="{{ route('citoyen.calendrier.index') }}" 
                                   style="color: white; padding: 12px 16px;">
                                    <i class="fas fa-calendar me-2"></i> Calendrier
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('citoyen.campagnes.*') ? 'active' : '' }}" 
                                   href="{{ route('citoyen.campagnes.index') }}" 
                                   style="color: white; padding: 12px 16px;">
                                    <i class="fas fa-bullhorn me-2"></i> Campagnes de Sensibilisation
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('citoyen.notifications.*') ? 'active' : '' }}" 
                                   href="{{ route('citoyen.notifications.index') }}" 
                                   style="color: white; padding: 12px 16px;">
                                    <i class="fas fa-bell me-2"></i> Notifications
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('messages.*') ? 'active' : '' }}" 
                                   href="{{ route('messages.index') }}" 
                                   style="color: white; padding: 12px 16px;">
                                    <i class="fas fa-comments me-2"></i> Messages
                                </a>
                            </li>
                        @elseif(auth()->user()->role === 'collecteur')
                            <!-- Menu Collecteur -->
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('collecteur.dashboard') ? 'active' : '' }}" 
                                   href="{{ route('collecteur.dashboard') }}" 
                                   style="color: white; padding: 12px 16px;">
                                    <i class="fas fa-home me-2"></i> Tableau de bord
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('collecteur.itineraires.*') ? 'active' : '' }}" 
                                   href="{{ route('collecteur.itineraires.index') }}" 
                                   style="color: white; padding: 12px 16px;">
                                    <i class="fas fa-route me-2"></i> Mes Itinéraires
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('collecteur.collectes.*') ? 'active' : '' }}" 
                                   href="{{ route('collecteur.collectes.index') }}" 
                                   style="color: white; padding: 12px 16px;">
                                    <i class="fas fa-recycle me-2"></i> Mes Collectes
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('collecteur.incidents.*') ? 'active' : '' }}" 
                                   href="{{ route('collecteur.incidents.create') }}" 
                                   style="color: white; padding: 12px 16px;">
                                    <i class="fas fa-exclamation-triangle me-2"></i> Incidents
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('collecteur.notifications.*') ? 'active' : '' }}" 
                                   href="{{ route('collecteur.notifications.index') }}" 
                                   style="color: white; padding: 12px 16px;">
                                    <i class="fas fa-bell me-2"></i> Notifications
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('collecteur.campagnes.*') ? 'active' : '' }}" 
                                   href="{{ route('collecteur.campagnes.index') }}" 
                                   style="color: white; padding: 12px 16px;">
                                    <i class="fas fa-bullhorn me-2"></i> Campagnes de Sensibilisation
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('messages.*') ? 'active' : '' }}" 
                                   href="{{ route('messages.index') }}" 
                                   style="color: white; padding: 12px 16px;">
                                    <i class="fas fa-comments me-2"></i> Messages
                                </a>
                            </li>
                        @elseif(auth()->user()->role === 'admin')
                            <!-- Menu Admin -->
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" 
                                   href="{{ route('admin.dashboard') }}" 
                                   style="color: white; padding: 12px 16px;">
                                    <i class="fas fa-tachometer-alt me-2"></i> Tableau de bord
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.signalements.*') ? 'active' : '' }}" 
                                   href="{{ route('admin.signalements.index') }}" 
                                   style="color: white; padding: 12px 16px;">
                                    <i class="fas fa-exclamation-triangle me-2"></i> Signalements
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.utilisateurs.*') ? 'active' : '' }}" 
                                   href="{{ route('admin.utilisateurs.index') }}" 
                                   style="color: white; padding: 12px 16px;">
                                    <i class="fas fa-users me-2"></i> Utilisateurs
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.plaintes.*') ? 'active' : '' }}" 
                                   href="{{ route('admin.plaintes.index') }}" 
                                   style="color: white; padding: 12px 16px;">
                                    <i class="fas fa-comments me-2"></i> Plaintes
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.itineraires.*') ? 'active' : '' }}" 
                                   href="{{ route('admin.itineraires.index') }}" 
                                   style="color: white; padding: 12px 16px;">
                                    <i class="fas fa-route me-2"></i> Itinéraires
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.calendrier.*') ? 'active' : '' }}" 
                                   href="{{ route('admin.calendrier.index') }}" 
                                   style="color: white; padding: 12px 16px;">
                                    <i class="fas fa-calendar-alt me-2"></i> Calendrier
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.supervision.*') ? 'active' : '' }}" 
                                   href="{{ route('admin.supervision.index') }}" 
                                   style="color: white; padding: 12px 16px;">
                                    <i class="fas fa-chart-line me-2"></i> Supervision
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.campagnes.*') ? 'active' : '' }}" 
                                   href="{{ route('admin.campagnes.index') }}" 
                                   style="color: white; padding: 12px 16px;">
                                    <i class="fas fa-bullhorn me-2"></i> Campagnes
                                </a>
                            </li>
                        @endif

                    </ul>

                    <!-- Footer de la sidebar - Visible pour tous les rôles -->
                    <div class="sidebar-footer" style="position: fixed; bottom: 0; left: 0; width: 16.666667%; padding: 8px; background-color: #1e3a8a; border-top: 1px solid rgba(255, 255, 255, 0.1);">
                        <div class="text-center">
                            <div style="color: rgba(255, 255, 255, 0.6); font-size: 0.7rem; margin-bottom: 3px;">
                                CollectPlusTogo
                            </div>
                            <div style="color: rgba(255, 255, 255, 0.5); font-size: 0.65rem;">
                                © 2025
                            </div>
                        </div>
                    </div>

                </div>
            </nav>

            <!-- Contenu principal -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4" style="margin-left: 16.666667%;">
                <div class="d-flex justify-content-end flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <!-- Profil utilisateur -->
                        <div class="dropdown">
                            <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown" data-bs-auto-close="true" aria-expanded="false">
                                <i class="fas fa-user me-2"></i>{{ auth()->user()->name }}
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                @if(auth()->user()->role === 'citoyen')
                                    <li><a class="dropdown-item" href="{{ route('citoyen.profil') }}">
                                        <i class="fas fa-user me-2"></i>Mon Profil
                                    </a></li>
                                    <li><a class="dropdown-item" href="{{ route('settings.index') }}">
                                        <i class="fas fa-cog me-2"></i>Paramètres
                                    </a></li>
                                @elseif(auth()->user()->role === 'collecteur')
                                    <li><a class="dropdown-item" href="#">
                                        <i class="fas fa-user me-2"></i>Mon Profil
                                    </a></li>
                                    <li><a class="dropdown-item" href="{{ route('settings.index') }}">
                                        <i class="fas fa-cog me-2"></i>Paramètres
                                    </a></li>
                                @elseif(auth()->user()->role === 'admin')
                                    <li><a class="dropdown-item" href="#">
                                        <i class="fas fa-user me-2"></i>Mon Profil
                                    </a></li>
                                    <li><a class="dropdown-item" href="{{ route('settings.index') }}">
                                        <i class="fas fa-cog me-2"></i>Paramètres
                                    </a></li>
                                @endif
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="fas fa-sign-out-alt me-2"></i>Déconnexion
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                        
                        <!-- Boutons d'action globaux ou spécifiques à la page -->
                        @yield('actions')
                    </div>
                </div>

                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-hide alerts
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                let alerts = document.querySelectorAll('.alert');
                alerts.forEach(function(alert) {
                    new bootstrap.Alert(alert).close();
                });
            }, 5000);
        });

        // Appliquer le thème automatiquement depuis localStorage ou session
        document.addEventListener('DOMContentLoaded', function() {
            const savedTheme = localStorage.getItem('theme') || '{{ session('theme', 'light') }}';
            document.body.className = document.body.className.replace(/theme-\w+/g, '');
            document.body.classList.add(`theme-${savedTheme}`);
        });
    </script>
    @yield('scripts')
</body>
</html>




    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <style>
        /* Thème clair uniquement */
        :root {
            --bg-primary: #ffffff;
            --bg-secondary: #f8f9fa;
            --text-primary: #2c3e50;
            --text-secondary: #6c757d;
            --border-color: #dee2e6;
            --shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        body {
            background-color: var(--bg-primary);
            color: var(--text-primary);
            transition: all 0.3s ease;
        }

        .card {
            background-color: var(--bg-primary);
            border: 1px solid var(--border-color);
        }

        .text-muted {
            color: var(--text-secondary) !important;
        }

        /* Styles pour les paramètres */
        .theme-option, .language-option {
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .theme-option:hover, .language-option:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        .theme-option.active .theme-preview,
        .language-option.active .language-preview {
            border-color: #007bff !important;
            box-shadow: 0 0 0 2px rgba(0,123,255,0.25);
        }

        .flag-icon {
            font-size: 2rem;
        }

        /* Styles pour le dropdown */
        .dropdown-menu {
            display: none;
            position: absolute;
            top: 100%;
            right: 0;
            z-index: 1000;
            min-width: 200px;
            padding: 0.5rem 0;
            margin: 0.125rem 0 0;
            background-color: #fff;
            border: 1px solid rgba(0,0,0,.15);
            border-radius: 0.375rem;
            box-shadow: 0 0.5rem 1rem rgba(0,0,0,.175);
        }

        .dropdown-menu.show {
            display: block;
        }

        .dropdown-item {
            display: block;
            width: 100%;
            padding: 0.25rem 1rem;
            clear: both;
            font-weight: 400;
            color: #212529;
            text-align: inherit;
            text-decoration: none;
            white-space: nowrap;
            background-color: transparent;
            border: 0;
        }

        .dropdown-item:hover {
            color: #1e2125;
            background-color: #e9ecef;
        }

        .dropdown-divider {
            height: 0;
            margin: 0.5rem 0;
            overflow: hidden;
            border-top: 1px solid #e9ecef;
        }
    </style>

    <script>
        // Thème clair fixé
        document.addEventListener('DOMContentLoaded', function() {
            document.body.classList.add('theme-light');
            
            // Gestion du dropdown manuel
            const dropdownButton = document.getElementById('userDropdown');
            const dropdownMenu = dropdownButton.nextElementSibling;
            
            if (dropdownButton && dropdownMenu) {
                dropdownButton.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    // Fermer tous les autres dropdowns
                    document.querySelectorAll('.dropdown-menu.show').forEach(menu => {
                        if (menu !== dropdownMenu) {
                            menu.classList.remove('show');
                        }
                    });
                    
                    // Toggle le dropdown actuel
                    dropdownMenu.classList.toggle('show');
                });
                
                // Fermer le dropdown quand on clique ailleurs
                document.addEventListener('click', function(e) {
                    if (!dropdownButton.contains(e.target) && !dropdownMenu.contains(e.target)) {
                        dropdownMenu.classList.remove('show');
                    }
                });
            }
        });
        
        // Charger le thème au démarrage
        document.addEventListener('DOMContentLoaded', function() {
            const savedTheme = localStorage.getItem('theme') || 'light';
            document.body.className = document.body.className.replace(/theme-\w+/g, '');
            document.body.classList.add(`theme-${savedTheme}`);
        });
    </script>
</body>
</html>




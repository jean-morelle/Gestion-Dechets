@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Paramètres</h2>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="row">
                <div class="col-md-6">
                    <!-- Notifications -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Notifications</h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('settings.notifications.update') }}">
                                @csrf
                                @method('PUT')
                                
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="notifications_email" name="notifications_email" 
                                           {{ session('notifications_email', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="notifications_email">
                                        Recevoir des notifications par email
                                    </label>
                                </div>

                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="notifications_sms" name="notifications_sms"
                                           {{ session('notifications_sms', false) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="notifications_sms">
                                        Recevoir des notifications par SMS
                                    </label>
                                </div>

                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="notifications_push" name="notifications_push"
                                           {{ session('notifications_push', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="notifications_push">
                                        Notifications push dans l'application
                                    </label>
                                </div>

                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Sauvegarder les notifications
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Apparence -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Apparence</h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('settings.appearance.update') }}" id="appearanceForm">
                                @csrf
                                @method('PUT')
                                
                                <div class="mb-3">
                                    <label for="theme" class="form-label">Thème</label>
                                    <select class="form-select" id="theme" name="theme">
                                        <option value="light" {{ session('theme', 'light') == 'light' ? 'selected' : '' }}>Mode clair</option>
                                        <option value="dark" {{ session('theme', 'light') == 'dark' ? 'selected' : '' }}>Mode sombre</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="language" class="form-label">Langue</label>
                                    <select class="form-select" id="language" name="language">
                                        <option value="fr" {{ session('language', 'fr') == 'fr' ? 'selected' : '' }}>Français</option>
                                    </select>
                                    <div class="form-text">
                                        La page sera rechargée après le changement de langue.
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end">
                                    <button type="button" class="btn btn-primary" onclick="applySettings()">
                                        <i class="fas fa-save"></i> Sauvegarder les paramètres
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <!-- Sécurité -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Sécurité</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <h6>Authentification à deux facteurs</h6>
                                <p class="text-muted small">Ajoutez une couche de sécurité supplémentaire à votre compte.</p>
                                <button class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-shield-alt"></i> Configurer la 2FA
                                </button>
                            </div>

                            <div class="mb-3">
                                <h6>Sessions actives</h6>
                                <p class="text-muted small">Gérez vos sessions de connexion.</p>
                                <button class="btn btn-outline-warning btn-sm">
                                    <i class="fas fa-sign-out-alt"></i> Se déconnecter de tous les appareils
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Données et confidentialité -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Données et confidentialité</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <h6>Export des données</h6>
                                <p class="text-muted small">Téléchargez une copie de vos données personnelles.</p>
                                <button class="btn btn-outline-info btn-sm">
                                    <i class="fas fa-download"></i> Exporter mes données
                                </button>
                            </div>

                            <div class="mb-3">
                                <h6>Suppression du compte</h6>
                                <p class="text-muted small">Supprimer définitivement votre compte et toutes vos données.</p>
                                <button class="btn btn-outline-danger btn-sm">
                                    <i class="fas fa-trash"></i> Supprimer mon compte
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function applySettings() {
    // Récupérer les valeurs sélectionnées
    const theme = document.getElementById('theme').value;
    const language = document.getElementById('language').value;
    
    // Appliquer le thème immédiatement
    document.body.className = document.body.className.replace(/theme-\w+/g, '');
    document.body.classList.add(`theme-${theme}`);
    localStorage.setItem('theme', theme);
    
    // Sauvegarder la langue
    localStorage.setItem('language', language);
    
    // Soumettre le formulaire pour sauvegarder côté serveur
    document.getElementById('appearanceForm').submit();
}

// Charger les paramètres au démarrage
document.addEventListener('DOMContentLoaded', function() {
    // Appliquer le thème sauvegardé
    const savedTheme = localStorage.getItem('theme') || '{{ session('theme', 'light') }}';
    document.body.className = document.body.className.replace(/theme-\w+/g, '');
    document.body.classList.add(`theme-${savedTheme}`);
    document.getElementById('theme').value = savedTheme;
    
    // Appliquer la langue sauvegardée
    const savedLanguage = localStorage.getItem('language') || '{{ session('language', 'fr') }}';
    document.getElementById('language').value = savedLanguage;
});
</script>
@endsection
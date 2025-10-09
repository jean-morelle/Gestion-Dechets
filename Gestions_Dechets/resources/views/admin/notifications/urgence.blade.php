@extends('layouts.app')

@section('title', 'Notification d\'Urgence')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-exclamation-triangle me-2"></i>{{ __('app.emergency_notification') }}
                    </h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info mb-3">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>{{ __('app.attention') }} :</strong> {{ __('app.this_notification_will_be_sent_to_all') }} ({{ $users->flatten()->count() }} {{ __('app.users') }}).
                    </div>

                    <form method="POST" action="{{ route('admin.notifications.store-urgence') }}">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <!-- Titre -->
                                <div class="mb-3">
                                    <label for="titre" class="form-label">{{ __('app.emergency_title') }} *</label>
                                    <input type="text" class="form-control @error('titre') is-invalid @enderror" 
                                           id="titre" name="titre" value="{{ old('titre') }}" 
                                           placeholder="Ex: Maintenance programmée, Alerte météo..." required>
                                    @error('titre')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Message -->
                                <div class="mb-3">
                                    <label for="message" class="form-label">{{ __('app.emergency_message') }} *</label>
                                    <textarea class="form-control @error('message') is-invalid @enderror" 
                                              id="message" name="message" rows="4" 
                                              placeholder="Décrivez l'urgence et les actions à prendre..." required>{{ old('message') }}</textarea>
                                    <div class="form-text">{{ __('app.minimum_characters', ['min' => 10]) }} {{ __('app.be_clear_and_concise') }}</div>
                                    @error('message')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Lien d'action -->
                                <div class="mb-3">
                                    <label for="lien_action" class="form-label">{{ __('app.action_link') }} (optionnel)</label>
                                    <input type="url" class="form-control @error('lien_action') is-invalid @enderror" 
                                           id="lien_action" name="lien_action" value="{{ old('lien_action') }}" 
                                           placeholder="https://exemple.com/action-urgence">
                                    <div class="form-text">URL vers une page d'information ou d'action spécifique</div>
                                    @error('lien_action')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <!-- Aperçu des destinataires -->
                                <div class="mb-3">
                                    <label class="form-label">{{ __('app.recipients') }} ({{ $users->flatten()->count() }} {{ __('app.users') }})</label>
                                    <div class="card">
                                        <div class="card-body" style="max-height: 300px; overflow-y: auto;">
                                            @foreach($users as $role => $roleUsers)
                                                <div class="mb-2">
                                                    <strong>{{ ucfirst($role) }}s ({{ count($roleUsers) }})</strong>
                                                    <div class="ms-3">
                                                        @foreach($roleUsers as $user)
                                                            <div class="small">{{ $user->name }}</div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Confirmation -->
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="confirmation" required>
                                <label class="form-check-label" for="confirmation">
                                    {{ __('app.i_confirm') }}
                                </label>
                            </div>
                        </div>

                        <!-- Boutons d'action -->
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary" id="sendBtn" disabled>
                                <i class="fas fa-paper-plane me-1"></i>Envoyer
                            </button>
                            <a href="{{ route('admin.notifications.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-1"></i>Annuler
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Activer le bouton d'envoi seulement si la confirmation est cochée
document.getElementById('confirmation').addEventListener('change', function() {
    document.getElementById('sendBtn').disabled = !this.checked;
});

// Confirmation supplémentaire avant envoi
document.querySelector('form').addEventListener('submit', function(e) {
    if (!confirm('Êtes-vous sûr de vouloir envoyer cette notification d\'urgence à TOUS les utilisateurs ?')) {
        e.preventDefault();
    }
});
</script>
@endsection
















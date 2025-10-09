@extends('layouts.app')

@section('title', 'Nouvelle Notification')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Nouvelle Notification</h5>
                </div>
                <div class="card-body py-2">
                    <form method="POST" action="{{ route('admin.notifications.store') }}">
                        @csrf
                        
                        <div class="row">
                            <!-- Colonne gauche: Formulaire -->
                            <div class="col-md-5">
                                <!-- Type et Priorité sur la même ligne -->
                                <div class="row mb-2">
                                    <div class="col-md-6">
                                        <label for="type" class="form-label small">Type *</label>
                                        <select class="form-select form-select-sm @error('type') is-invalid @enderror" id="type" name="type" required>
                                            <option value="">Type</option>
                                            <option value="signalement" {{ old('type') == 'signalement' ? 'selected' : '' }}>Signalement</option>
                                            <option value="plainte" {{ old('type') == 'plainte' ? 'selected' : '' }}>Plainte</option>
                                            <option value="collecte" {{ old('type') == 'collecte' ? 'selected' : '' }}>Collecte</option>
                                            <option value="systeme" {{ old('type') == 'systeme' ? 'selected' : '' }}>Système</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="priorite" class="form-label small">Priorité *</label>
                                        <select class="form-select form-select-sm @error('priorite') is-invalid @enderror" id="priorite" name="priorite" required>
                                            <option value="faible">Faible</option>
                                            <option value="moyenne" selected>Moyenne</option>
                                            <option value="elevee">Élevée</option>
                                            <option value="urgente">Urgente</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Titre -->
                                <div class="mb-2">
                                    <label for="titre" class="form-label small">Titre *</label>
                                    <input type="text" class="form-control form-control-sm @error('titre') is-invalid @enderror" 
                                           id="titre" name="titre" value="{{ old('titre') }}" 
                                           placeholder="Titre" required>
                                </div>

                                <!-- Message -->
                                <div class="mb-2">
                                    <label for="message" class="form-label small">Message *</label>
                                    <textarea class="form-control form-control-sm @error('message') is-invalid @enderror" 
                                              id="message" name="message" rows="2" 
                                              placeholder="Contenu" required>{{ old('message') }}</textarea>
                                </div>

                                <!-- Lien et Icône sur la même ligne -->
                                <div class="row mb-2">
                                    <div class="col-md-8">
                                        <label for="lien_action" class="form-label small">Lien (optionnel)</label>
                                        <input type="url" class="form-control form-control-sm @error('lien_action') is-invalid @enderror" 
                                               id="lien_action" name="lien_action" value="{{ old('lien_action') }}" 
                                               placeholder="https://">
                                    </div>
                                    <div class="col-md-4">
                                        <label for="icone" class="form-label small">Icône</label>
                                        <input type="text" class="form-control form-control-sm @error('icone') is-invalid @enderror" 
                                               id="icone" name="icone" value="{{ old('icone') }}" 
                                               placeholder="fa-bell">
                                    </div>
                                </div>
                            </div>

                            <!-- Colonne droite: Destinataires -->
                            <div class="col-md-7">
                                <label class="form-label small">Destinataires *</label>
                            <div class="row">
                                @foreach($users as $role => $roleUsers)
                                        <div class="col-md-4 mb-1">
                                        <div class="card">
                                                <div class="card-header py-1 px-2 bg-light">
                                                    <div class="form-check mb-0">
                                                    <input class="form-check-input" type="checkbox" id="select-all-{{ $role }}" 
                                                           onchange="toggleRoleUsers('{{ $role }}', this.checked)">
                                                        <label class="form-check-label small" for="select-all-{{ $role }}">
                                                        {{ ucfirst($role) }}s ({{ count($roleUsers) }})
                                                    </label>
                                                </div>
                                            </div>
                                                <div class="card-body p-2" style="max-height: 180px; overflow-y: auto;">
                                                @foreach($roleUsers as $user)
                                                        <div class="form-check mb-0">
                                                        <input class="form-check-input user-checkbox" type="checkbox" 
                                                               name="destinataires[]" value="{{ $user->id }}" 
                                                               id="user-{{ $user->id }}" data-role="{{ $role }}">
                                                            <label class="form-check-label small" for="user-{{ $user->id }}" style="font-size: 0.8rem;">
                                                            {{ $user->name }}
                                                        </label>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            @error('destinataires')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                        </div>

                        <!-- Boutons d'action -->
                        <div class="d-flex gap-2 mt-2">
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="fas fa-paper-plane me-1"></i>Envoyer
                            </button>
                            <a href="{{ route('admin.notifications.index') }}" class="btn btn-secondary btn-sm">
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
function toggleRoleUsers(role, checked) {
    const checkboxes = document.querySelectorAll(`input[data-role="${role}"]`);
    checkboxes.forEach(checkbox => {
        checkbox.checked = checked;
    });
}

// Vérifier si au moins un destinataire est sélectionné
document.querySelector('form').addEventListener('submit', function(e) {
    const selectedUsers = document.querySelectorAll('input[name="destinataires[]"]:checked');
    if (selectedUsers.length === 0) {
        e.preventDefault();
        alert('Veuillez sélectionner au moins un destinataire.');
    }
});
</script>
@endsection
















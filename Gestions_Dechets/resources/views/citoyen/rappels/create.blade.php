@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header py-2 d-flex justify-content-between align-items-center">
                    <h6 class="card-title mb-0">Nouveau rappel automatique</h6>
                    <a href="{{ route('citoyen.rappels.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-arrow-left me-1"></i>Retour
                    </a>
                </div>
                <div class="card-body py-3">
                    <form method="POST" action="{{ route('citoyen.rappels.store') }}">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="calendrier_id" class="form-label">Calendrier de collecte <span class="text-danger">*</span></label>
                            <select class="form-select @error('calendrier_id') is-invalid @enderror" 
                                    id="calendrier_id" name="calendrier_id" required>
                                <option value="">Sélectionner un calendrier</option>
                                @foreach($calendriers as $calendrier)
                                    <option value="{{ $calendrier->id }}" {{ old('calendrier_id') == $calendrier->id ? 'selected' : '' }}>
                                        {{ $calendrier->nom }} - {{ $calendrier->quartier }} ({{ $calendrier->type_collecte_label }})
                                    </option>
                                @endforeach
                            </select>
                            @error('calendrier_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Sélectionnez le calendrier pour lequel vous souhaitez recevoir des rappels</div>
                        </div>

                        <div class="mb-3">
                            <label for="type_rappel" class="form-label">Type de rappel <span class="text-danger">*</span></label>
                            <select class="form-select @error('type_rappel') is-invalid @enderror" 
                                    id="type_rappel" name="type_rappel" required>
                                <option value="">Sélectionner un type</option>
                                <option value="email" {{ old('type_rappel') == 'email' ? 'selected' : '' }}>Email</option>
                                <option value="sms" {{ old('type_rappel') == 'sms' ? 'selected' : '' }}>SMS</option>
                                <option value="push" {{ old('type_rappel') == 'push' ? 'selected' : '' }}>Notification push</option>
                            </select>
                            @error('type_rappel')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="delai_heures" class="form-label">Délai d'envoi <span class="text-danger">*</span></label>
                            <select class="form-select @error('delai_heures') is-invalid @enderror" 
                                    id="delai_heures" name="delai_heures" required>
                                <option value="">Sélectionner un délai</option>
                                <option value="1" {{ old('delai_heures') == '1' ? 'selected' : '' }}>1 heure avant</option>
                                <option value="2" {{ old('delai_heures') == '2' ? 'selected' : '' }}>2 heures avant</option>
                                <option value="6" {{ old('delai_heures') == '6' ? 'selected' : '' }}>6 heures avant</option>
                                <option value="12" {{ old('delai_heures') == '12' ? 'selected' : '' }}>12 heures avant</option>
                                <option value="24" {{ old('delai_heures') == '24' ? 'selected' : '' }}>24 heures avant (1 jour)</option>
                                <option value="48" {{ old('delai_heures') == '48' ? 'selected' : '' }}>48 heures avant (2 jours)</option>
                                <option value="72" {{ old('delai_heures') == '72' ? 'selected' : '' }}>72 heures avant (3 jours)</option>
                            </select>
                            @error('delai_heures')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Choisissez quand vous souhaitez recevoir le rappel avant la collecte</div>
                        </div>

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Information :</strong> Les rappels automatiques vous permettront de ne pas oublier de sortir vos déchets avant le passage du collecteur.
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>Créer le rappel
                            </button>
                            <a href="{{ route('citoyen.rappels.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-1"></i>Annuler
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


















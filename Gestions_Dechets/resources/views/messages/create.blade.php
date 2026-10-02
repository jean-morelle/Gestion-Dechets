@extends('layouts.app')

@section('title', 'Nouveau message')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header py-2 d-flex justify-content-between align-items-center">
                    <h6 class="card-title mb-0">Nouveau message</h6>
                    <a href="{{ route('messages.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-arrow-left me-1"></i>Retour
                    </a>
                </div>
                <div class="card-body py-3">
                    <form method="POST" action="{{ route('messages.store') }}">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="receiver_id" class="form-label">Destinataire <span class="text-danger">*</span></label>
                            <select class="form-select @error('receiver_id') is-invalid @enderror" 
                                    id="receiver_id" name="receiver_id" required>
                                <option value="">Sélectionner un destinataire</option>
                                
                                @if(isset($users['admin']) && $users['admin']->count() > 0)
                                    <optgroup label="Administration">
                                        @foreach($users['admin'] as $admin)
                                            <option value="{{ $admin->id }}" {{ old('receiver_id') == $admin->id ? 'selected' : '' }}>
                                                {{ $admin->name }} ({{ $admin->email }})
                                            </option>
                                        @endforeach
                                    </optgroup>
                                @endif
                                
                                @if(isset($users['citoyen']) && $users['citoyen']->count() > 0)
                                    <optgroup label="Citoyens">
                                        @foreach($users['citoyen'] as $citoyen)
                                            <option value="{{ $citoyen->id }}" {{ old('receiver_id') == $citoyen->id ? 'selected' : '' }}>
                                                {{ $citoyen->name }} ({{ $citoyen->email }})
                                            </option>
                                        @endforeach
                                    </optgroup>
                                @endif
                                
                                @if(isset($users['collecteur']) && $users['collecteur']->count() > 0)
                                    <optgroup label="Collecteurs">
                                        @foreach($users['collecteur'] as $collecteur)
                                            <option value="{{ $collecteur->id }}" {{ old('receiver_id') == $collecteur->id ? 'selected' : '' }}>
                                                {{ $collecteur->name }} ({{ $collecteur->email }})
                                            </option>
                                        @endforeach
                                    </optgroup>
                                @endif
                            </select>
                            @error('receiver_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="subject" class="form-label">Sujet</label>
                            <input type="text" class="form-control @error('subject') is-invalid @enderror" 
                                   id="subject" name="subject" value="{{ old('subject') }}" 
                                   placeholder="Sujet du message (optionnel)">
                            @error('subject')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="message" class="form-label">Message <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('message') is-invalid @enderror" 
                                      id="message" name="message" rows="6" 
                                      placeholder="Tapez votre message ici..." required>{{ old('message') }}</textarea>
                            @error('message')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Minimum 10 caractères</div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane me-1"></i>Envoyer
                            </button>
                            <a href="{{ route('messages.index') }}" class="btn btn-secondary">
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



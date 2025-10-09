@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header py-2 d-flex justify-content-between align-items-center">
                    <h6 class="card-title mb-0">Répondre à {{ $originalMessage->sender->name }}</h6>
                    <a href="{{ route('messages.show', $originalMessage) }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-arrow-left me-1"></i>Retour
                    </a>
                </div>
                <div class="card-body py-3">
                    <!-- Message original -->
                    <div class="mb-4">
                        <h6 class="text-muted">Message original :</h6>
                        <div class="bg-light p-3 rounded border-start border-3 border-primary">
                            @if($originalMessage->subject)
                                <div class="mb-2">
                                    <strong>Sujet :</strong> {{ $originalMessage->subject }}
                                </div>
                            @endif
                            <div class="mb-2">
                                <strong>Message :</strong>
                            </div>
                            <div class="text-muted">
                                {!! nl2br(e($originalMessage->message)) !!}
                            </div>
                            <div class="mt-2">
                                <small class="text-muted">
                                    <i class="fas fa-clock me-1"></i>
                                    Envoyé le {{ $originalMessage->created_at->format('d/m/Y à H:i') }}
                                </small>
                            </div>
                        </div>
                    </div>

                    <!-- Formulaire de réponse -->
                    <form method="POST" action="{{ route('messages.reply.store', $originalMessage) }}">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="subject" class="form-label">Sujet de la réponse</label>
                            <input type="text" class="form-control @error('subject') is-invalid @enderror" 
                                   id="subject" name="subject" value="{{ old('subject', 'Re: ' . $originalMessage->subject) }}" 
                                   placeholder="Sujet de votre réponse">
                            @error('subject')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="message" class="form-label">Votre réponse <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('message') is-invalid @enderror" 
                                      id="message" name="message" rows="6" 
                                      placeholder="Tapez votre réponse ici..." required>{{ old('message') }}</textarea>
                            @error('message')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Minimum 10 caractères</div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane me-1"></i>Envoyer la réponse
                            </button>
                            <a href="{{ route('messages.show', $originalMessage) }}" class="btn btn-secondary">
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
















@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header py-2 d-flex justify-content-between align-items-center">
                    <h6 class="card-title mb-0">
                        @if($message->sender_id === auth()->id())
                            Message envoyé à {{ $message->receiver->name }}
                        @else
                            Message de {{ $message->sender->name }}
                        @endif
                    </h6>
                    <a href="{{ route('messages.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-arrow-left me-1"></i>Retour
                    </a>
                </div>
                <div class="card-body py-3">
                    @if($message->subject)
                        <div class="mb-3">
                            <h6 class="text-muted">Sujet :</h6>
                            <p class="mb-0">{{ $message->subject }}</p>
                        </div>
                    @endif
                    
                    <div class="mb-3">
                        <h6 class="text-muted">Message :</h6>
                        <div class="bg-light p-3 rounded">
                            {!! nl2br(e($message->message)) !!}
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <small class="text-muted">
                                <i class="fas fa-clock me-1"></i>
                                Envoyé le {{ $message->created_at->format('d/m/Y à H:i') }}
                            </small>
                        </div>
                        @if($message->is_read && $message->read_at)
                            <div class="col-md-6 text-end">
                                <small class="text-muted">
                                    <i class="fas fa-check me-1"></i>
                                    Lu le {{ $message->read_at->format('d/m/Y à H:i') }}
                                </small>
                            </div>
                        @endif
                    </div>
                    
                    <!-- Bouton de réponse -->
                    @if($message->sender_id !== auth()->id())
                        <div class="mt-3 pt-3 border-top">
                            <a href="{{ route('messages.reply', $message) }}" class="btn btn-primary">
                                <i class="fas fa-reply me-1"></i>Répondre
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection



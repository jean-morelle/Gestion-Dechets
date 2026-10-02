@extends('layouts.app')

@section('title', 'Messagerie')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header py-2 d-flex justify-content-between align-items-center">
                    <h6 class="card-title mb-0">Messages - Communication</h6>
                    <a href="{{ route('messages.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus me-1"></i>Nouveau Message
                    </a>
                </div>
                <div class="card-body py-2">
                    <!-- Onglets -->
                    <ul class="nav nav-tabs mb-3" id="messageTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="received-tab" data-bs-toggle="tab" data-bs-target="#received" type="button" role="tab">
                                Messages reçus @if($unreadCount > 0)<span class="badge bg-danger ms-1">{{ $unreadCount }}</span>@endif
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="sent-tab" data-bs-toggle="tab" data-bs-target="#sent" type="button" role="tab">
                                Messages envoyés
                            </button>
                        </li>
                    </ul>

                    <!-- Contenu des onglets -->
                    <div class="tab-content" id="messageTabsContent">
                        <!-- Messages reçus -->
                        <div class="tab-pane fade show active" id="received" role="tabpanel">
                            @if($messagesReceived->count() > 0)
                                <div class="list-group">
                                    @foreach($messagesReceived as $message)
                                    <div class="list-group-item {{ !$message->is_read ? 'bg-light' : '' }}">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div class="flex-grow-1">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <h6 class="mb-1">
                                                        @if(!$message->is_read)<i class="fas fa-circle text-primary me-1" style="font-size: 8px;"></i>@endif
                                                        {{ $message->sender->name }}
                                                        @if($message->subject)
                                                            <small class="text-muted">- {{ $message->subject }}</small>
                                                        @endif
                                                    </h6>
                                                    <small class="text-muted">{{ $message->created_at->format('d/m/Y H:i') }}</small>
                                                </div>
                                                <p class="mb-1">{{ Str::limit($message->message, 100) }}</p>
                                            </div>
                                            <div class="ms-3">
                                                <a href="{{ route('messages.show', $message) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                                
                                <!-- Pagination -->
                                <div class="d-flex justify-content-center mt-3">
                                    {{ $messagesReceived->links() }}
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">Aucun message reçu</p>
                                </div>
                            @endif
                        </div>

                        <!-- Messages envoyés -->
                        <div class="tab-pane fade" id="sent" role="tabpanel">
                            @if($messagesSent->count() > 0)
                                <div class="list-group">
                                    @foreach($messagesSent as $message)
                                    <div class="list-group-item">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div class="flex-grow-1">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <h6 class="mb-1">
                                                        À {{ $message->receiver->name }}
                                                        @if($message->subject)
                                                            <small class="text-muted">- {{ $message->subject }}</small>
                                                        @endif
                                                    </h6>
                                                    <small class="text-muted">{{ $message->created_at->format('d/m/Y H:i') }}</small>
                                                </div>
                                                <p class="mb-1">{{ Str::limit($message->message, 100) }}</p>
                                            </div>
                                            <div class="ms-3">
                                                <a href="{{ route('messages.show', $message) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                                
                                <!-- Pagination -->
                                <div class="d-flex justify-content-center mt-3">
                                    {{ $messagesSent->links() }}
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <i class="fas fa-paper-plane fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">Aucun message envoyé</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

















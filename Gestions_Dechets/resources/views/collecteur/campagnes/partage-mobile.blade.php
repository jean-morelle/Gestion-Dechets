@extends('layouts.app')

@section('title', 'Partage Mobile')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Partage Mobile - {{ $campagne->titre }}</h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <i class="fas fa-mobile-alt fa-3x text-primary mb-3"></i>
                        <h4>Partagez cette campagne via mobile</h4>
                        <p class="text-muted">Utilisez ces liens pour partager la campagne via WhatsApp, SMS ou email</p>
                    </div>

                    <!-- Lien de partage -->
                    <div class="mb-4">
                        <label for="partage-link" class="form-label fw-bold">Lien de partage</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="partage-link" 
                                   value="{{ route('citoyen.campagnes.show', $campagne) }}" readonly>
                            <button class="btn btn-outline-secondary" type="button" onclick="copierLien()">
                                <i class="fas fa-copy me-1"></i>Copier
                            </button>
                        </div>
                    </div>

                    <!-- Message de partage -->
                    <div class="mb-4">
                        <label for="message-partage" class="form-label fw-bold">Message de partage</label>
                        <textarea class="form-control" id="message-partage" rows="4" readonly>🌍 *Campagne de Sensibilisation*

*{{ $campagne->titre }}*

{{ $campagne->description }}

Découvrez cette campagne importante pour notre environnement !

Lien: {{ route('citoyen.campagnes.show', $campagne) }}

#Sensibilisation #Environnement #Lomé</textarea>
                        <button class="btn btn-outline-secondary mt-2" onclick="copierMessage()">
                            <i class="fas fa-copy me-1"></i>Copier le message
                        </button>
                    </div>

                    <!-- Boutons de partage -->
                    <div class="row">
                        <div class="col-md-4 mb-2">
                            <a href="https://wa.me/?text={{ urlencode('🌍 *Campagne de Sensibilisation*%0A%0A*' . $campagne->titre . '*%0A%0A' . $campagne->description . '%0A%0ADécouvrez cette campagne importante pour notre environnement !%0A%0ALien: ' . route('citoyen.campagnes.show', $campagne) . '%0A%0A#Sensibilisation #Environnement #Lomé') }}" 
                               class="btn btn-success w-100" target="_blank">
                                <i class="fab fa-whatsapp me-1"></i>WhatsApp
                            </a>
                        </div>
                        <div class="col-md-4 mb-2">
                            <a href="sms:?body={{ urlencode('🌍 Campagne de Sensibilisation: ' . $campagne->titre . ' - ' . route('citoyen.campagnes.show', $campagne)) }}" 
                               class="btn btn-info w-100">
                                <i class="fas fa-sms me-1"></i>SMS
                            </a>
                        </div>
                        <div class="col-md-4 mb-2">
                            <a href="mailto:?subject={{ urlencode('Campagne de Sensibilisation: ' . $campagne->titre) }}&body={{ urlencode('Bonjour,%0A%0AVoici une campagne de sensibilisation importante :%0A%0A' . $campagne->titre . '%0A' . $campagne->description . '%0A%0ALien: ' . route('citoyen.campagnes.show', $campagne) . '%0A%0ACordialement') }}" 
                               class="btn btn-warning w-100">
                                <i class="fas fa-envelope me-1"></i>Email
                            </a>
                        </div>
                    </div>

                    <!-- QR Code -->
                    <div class="text-center mt-4">
                        <h6>QR Code pour partage rapide</h6>
                        <div id="qrcode" class="d-flex justify-content-center"></div>
                        <small class="text-muted">Scannez ce code pour accéder à la campagne</small>
                    </div>

                    <!-- Actions -->
                    <div class="d-flex gap-2 mt-4">
                        <a href="{{ route('collecteur.campagnes.show', $campagne) }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-1"></i>Retour
                        </a>
                        <a href="{{ route('collecteur.campagnes.index') }}" class="btn btn-primary">
                            <i class="fas fa-list me-1"></i>Toutes les campagnes
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- QR Code Library -->
<script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.3/build/qrcode.min.js"></script>

<script>
// Copier le lien
function copierLien() {
    const lien = document.getElementById('partage-link');
    lien.select();
    document.execCommand('copy');
    
    // Afficher une notification
    const btn = event.target;
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-check me-1"></i>Copié !';
    btn.classList.remove('btn-outline-secondary');
    btn.classList.add('btn-success');
    
    setTimeout(() => {
        btn.innerHTML = originalText;
        btn.classList.remove('btn-success');
        btn.classList.add('btn-outline-secondary');
    }, 2000);
}

// Copier le message
function copierMessage() {
    const message = document.getElementById('message-partage');
    message.select();
    document.execCommand('copy');
    
    // Afficher une notification
    const btn = event.target;
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-check me-1"></i>Copié !';
    btn.classList.remove('btn-outline-secondary');
    btn.classList.add('btn-success');
    
    setTimeout(() => {
        btn.innerHTML = originalText;
        btn.classList.remove('btn-success');
        btn.classList.add('btn-outline-secondary');
    }, 2000);
}

// Générer le QR Code
document.addEventListener('DOMContentLoaded', function() {
    const qrContainer = document.getElementById('qrcode');
    const lien = '{{ route("citoyen.campagnes.show", $campagne) }}';
    
    QRCode.toCanvas(qrContainer, lien, {
        width: 200,
        height: 200,
        color: {
            dark: '#000000',
            light: '#FFFFFF'
        }
    }, function (error) {
        if (error) console.error(error);
    });
});
</script>
@endsection
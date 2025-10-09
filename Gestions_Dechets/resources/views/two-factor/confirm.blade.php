@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">
                        <i class="fas fa-qrcode me-2"></i>
                        Confirmer l'activation de l'authentification à deux facteurs
                    </h4>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Étape 1 : Scanner le QR Code</h5>
                            <p class="text-muted">
                                Scannez ce QR Code avec votre application d'authentification (Google Authenticator, Microsoft Authenticator, etc.)
                            </p>
                            
                            <div class="text-center mb-4">
                                <div class="border rounded p-3 bg-white d-inline-block">
                                    <img id="qrcodeImg" src="" alt="QR Code" style="display: none; max-width: 200px;">
                                    <div id="qrcodeFallback" class="small text-muted"></div>
                                </div>
                            </div>

                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Pas d'application ?</strong> Téléchargez Google Authenticator ou Microsoft Authenticator sur votre téléphone.
                            </div>

                            <div class="alert alert-warning">
                                <h6><i class="fas fa-exclamation-triangle me-2"></i>Si le scan ne fonctionne pas :</h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <strong>Clé secrète :</strong><br>
                                        <code class="small">{{ $user->two_factor_secret }}</code>
                                        <button type="button" class="btn btn-sm btn-outline-secondary ms-2" onclick="copyToClipboard('{{ $user->two_factor_secret }}')">
                                            <i class="fas fa-copy"></i>
                                        </button>
                                    </div>
                                    <div class="col-md-6">
                                        <strong>URL complète :</strong><br>
                                        <code class="small">{{ $qrCodeUrl }}</code>
                                        <button type="button" class="btn btn-sm btn-outline-secondary ms-2" onclick="copyToClipboard('{{ $qrCodeUrl }}')">
                                            <i class="fas fa-copy"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <h5>Étape 2 : Entrer le code de vérification</h5>
                            <p class="text-muted">
                                Entrez le code à 6 chiffres affiché dans votre application d'authentification.
                            </p>

                            <form method="POST" action="{{ route('two-factor.confirm') }}">
                                @csrf
                                
                                <div class="mb-3">
                                    <label for="code" class="form-label">Code de vérification</label>
                                    <input type="text" 
                                           class="form-control form-control-lg text-center @error('code') is-invalid @enderror" 
                                           id="code" 
                                           name="code" 
                                           placeholder="123456"
                                           maxlength="6"
                                           pattern="[0-9]{6}"
                                           required
                                           autofocus>
                                    @error('code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="fas fa-check me-2"></i>Confirmer l'activation
                                    </button>
                                </div>
                            </form>

                            <div class="mt-4">
                                <h6>Codes de récupération</h6>
                                <p class="text-muted small">
                                    Conservez ces codes de récupération dans un endroit sûr. 
                                    Ils vous permettront d'accéder à votre compte si vous perdez votre téléphone.
                                </p>
                                
                                @if($user->two_factor_recovery_codes)
                                    <div class="alert alert-warning">
                                        <strong>Codes de récupération :</strong>
                                        <div class="row mt-2">
                                            @foreach(json_decode($user->two_factor_recovery_codes, true) as $code)
                                                <div class="col-6 col-md-4 mb-1">
                                                    <code class="small">{{ $code }}</code>
                                                </div>
                                            @endforeach
                                        </div>
                                        <small class="text-muted">
                                            <i class="fas fa-exclamation-triangle me-1"></i>
                                            Copiez et sauvegardez ces codes maintenant. Ils ne seront plus affichés après cette étape.
                                        </small>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Générer le QR Code
    const qrUrl = @json($qrCodeUrl);
    const qrImg = document.getElementById('qrcodeImg');
    const fallback = document.getElementById('qrcodeFallback');

    // Utiliser l'API QR Server pour générer le QR Code
    const encodedUrl = encodeURIComponent(qrUrl);
    const qrApiUrl = `https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=${encodedUrl}`;
    
    qrImg.src = qrApiUrl;
    qrImg.style.display = 'block';
    
    qrImg.onload = function() {
        fallback.innerHTML = '';
    };
    
    qrImg.onerror = function() {
        qrImg.style.display = 'none';
        fallback.innerHTML = `
            <div class="alert alert-warning">
                <strong>QR Code non disponible</strong><br>
                <small>Utilisez ce lien dans votre application d'authentification :</small><br>
                <code class="small">${qrUrl}</code><br>
                <small class="text-muted">Ou entrez manuellement :</small><br>
                <strong>Clé secrète :</strong> <code>@json($user->two_factor_secret)</code>
            </div>
        `;
    };

    // Auto-formatage du code
    const codeInput = document.getElementById('code');
    codeInput.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, ''); // Garder seulement les chiffres
        if (value.length > 6) {
            value = value.substring(0, 6);
        }
        e.target.value = value;
    });

    // Auto-submit quand 6 chiffres sont entrés
    codeInput.addEventListener('input', function(e) {
        if (e.target.value.length === 6) {
            setTimeout(() => {
                e.target.form.submit();
            }, 500);
        }
    });

    // Fonction pour copier dans le presse-papiers
    window.copyToClipboard = function(text) {
        navigator.clipboard.writeText(text).then(function() {
            // Afficher un toast de confirmation
            const toast = document.createElement('div');
            toast.className = 'toast align-items-center text-white bg-success border-0 position-fixed top-0 end-0 m-3';
            toast.style.zIndex = '9999';
            toast.innerHTML = `
                <div class="d-flex">
                    <div class="toast-body">
                        Copié dans le presse-papiers !
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            `;
            document.body.appendChild(toast);
            
            const bsToast = new bootstrap.Toast(toast);
            bsToast.show();
            
            // Supprimer le toast après 3 secondes
            setTimeout(() => {
                toast.remove();
            }, 3000);
        });
    };
});
</script>
@endsection



@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">
                        <i class="fas fa-qrcode me-2"></i>
                        Confirmer l'activation de l'authentification à deux facteurs
                    </h4>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Étape 1 : Scanner le QR Code</h5>
                            <p class="text-muted">
                                Scannez ce QR Code avec votre application d'authentification (Google Authenticator, Microsoft Authenticator, etc.)
                            </p>
                            
                            <div class="text-center mb-4">
                                <div class="border rounded p-3 bg-white d-inline-block">
                                    <img id="qrcodeImg" src="" alt="QR Code" style="display: none; max-width: 200px;">
                                    <div id="qrcodeFallback" class="small text-muted"></div>
                                </div>
                            </div>

                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Pas d'application ?</strong> Téléchargez Google Authenticator ou Microsoft Authenticator sur votre téléphone.
                            </div>

                            <div class="alert alert-warning">
                                <h6><i class="fas fa-exclamation-triangle me-2"></i>Si le scan ne fonctionne pas :</h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <strong>Clé secrète :</strong><br>
                                        <code class="small">{{ $user->two_factor_secret }}</code>
                                        <button type="button" class="btn btn-sm btn-outline-secondary ms-2" onclick="copyToClipboard('{{ $user->two_factor_secret }}')">
                                            <i class="fas fa-copy"></i>
                                        </button>
                                    </div>
                                    <div class="col-md-6">
                                        <strong>URL complète :</strong><br>
                                        <code class="small">{{ $qrCodeUrl }}</code>
                                        <button type="button" class="btn btn-sm btn-outline-secondary ms-2" onclick="copyToClipboard('{{ $qrCodeUrl }}')">
                                            <i class="fas fa-copy"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <h5>Étape 2 : Entrer le code de vérification</h5>
                            <p class="text-muted">
                                Entrez le code à 6 chiffres affiché dans votre application d'authentification.
                            </p>

                            <form method="POST" action="{{ route('two-factor.confirm') }}">
                                @csrf
                                
                                <div class="mb-3">
                                    <label for="code" class="form-label">Code de vérification</label>
                                    <input type="text" 
                                           class="form-control form-control-lg text-center @error('code') is-invalid @enderror" 
                                           id="code" 
                                           name="code" 
                                           placeholder="123456"
                                           maxlength="6"
                                           pattern="[0-9]{6}"
                                           required
                                           autofocus>
                                    @error('code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="fas fa-check me-2"></i>Confirmer l'activation
                                    </button>
                                </div>
                            </form>

                            <div class="mt-4">
                                <h6>Codes de récupération</h6>
                                <p class="text-muted small">
                                    Conservez ces codes de récupération dans un endroit sûr. 
                                    Ils vous permettront d'accéder à votre compte si vous perdez votre téléphone.
                                </p>
                                
                                @if($user->two_factor_recovery_codes)
                                    <div class="alert alert-warning">
                                        <strong>Codes de récupération :</strong>
                                        <div class="row mt-2">
                                            @foreach(json_decode($user->two_factor_recovery_codes, true) as $code)
                                                <div class="col-6 col-md-4 mb-1">
                                                    <code class="small">{{ $code }}</code>
                                                </div>
                                            @endforeach
                                        </div>
                                        <small class="text-muted">
                                            <i class="fas fa-exclamation-triangle me-1"></i>
                                            Copiez et sauvegardez ces codes maintenant. Ils ne seront plus affichés après cette étape.
                                        </small>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Générer le QR Code
    const qrUrl = @json($qrCodeUrl);
    const qrImg = document.getElementById('qrcodeImg');
    const fallback = document.getElementById('qrcodeFallback');

    // Utiliser l'API QR Server pour générer le QR Code
    const encodedUrl = encodeURIComponent(qrUrl);
    const qrApiUrl = `https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=${encodedUrl}`;
    
    qrImg.src = qrApiUrl;
    qrImg.style.display = 'block';
    
    qrImg.onload = function() {
        fallback.innerHTML = '';
    };
    
    qrImg.onerror = function() {
        qrImg.style.display = 'none';
        fallback.innerHTML = `
            <div class="alert alert-warning">
                <strong>QR Code non disponible</strong><br>
                <small>Utilisez ce lien dans votre application d'authentification :</small><br>
                <code class="small">${qrUrl}</code><br>
                <small class="text-muted">Ou entrez manuellement :</small><br>
                <strong>Clé secrète :</strong> <code>@json($user->two_factor_secret)</code>
            </div>
        `;
    };

    // Auto-formatage du code
    const codeInput = document.getElementById('code');
    codeInput.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, ''); // Garder seulement les chiffres
        if (value.length > 6) {
            value = value.substring(0, 6);
        }
        e.target.value = value;
    });

    // Auto-submit quand 6 chiffres sont entrés
    codeInput.addEventListener('input', function(e) {
        if (e.target.value.length === 6) {
            setTimeout(() => {
                e.target.form.submit();
            }, 500);
        }
    });

    // Fonction pour copier dans le presse-papiers
    window.copyToClipboard = function(text) {
        navigator.clipboard.writeText(text).then(function() {
            // Afficher un toast de confirmation
            const toast = document.createElement('div');
            toast.className = 'toast align-items-center text-white bg-success border-0 position-fixed top-0 end-0 m-3';
            toast.style.zIndex = '9999';
            toast.innerHTML = `
                <div class="d-flex">
                    <div class="toast-body">
                        Copié dans le presse-papiers !
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            `;
            document.body.appendChild(toast);
            
            const bsToast = new bootstrap.Toast(toast);
            bsToast.show();
            
            // Supprimer le toast après 3 secondes
            setTimeout(() => {
                toast.remove();
            }, 3000);
        });
    };
});
</script>
@endsection









































































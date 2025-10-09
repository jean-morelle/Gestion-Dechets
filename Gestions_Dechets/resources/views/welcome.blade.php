<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion Déchets - {{ ucfirst($role ?? 'Utilisateur') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center min-vh-100 align-items-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-body text-center p-5">
                        <div class="mb-4">
                            <i class="fas fa-tools fa-3x text-primary"></i>
                        </div>
                        
                        <h2 class="card-title mb-3">
                            Interface {{ ucfirst($role ?? 'Utilisateur') }}
                        </h2>
                        
                        <p class="card-text text-muted mb-4">
                            {{ $message ?? 'Page en cours de développement' }}
                        </p>
                        
                        <div class="mt-3">
                            <small class="text-muted">
                                Rôle actuel: <strong>{{ ucfirst($role ?? 'Non défini') }}</strong>
                            </small>
                        </div>
                        
                        <div class="mt-4">
                            <a href="{{ route('logout') }}" class="btn btn-outline-primary">
                                <i class="fas fa-sign-out-alt"></i> Se déconnecter
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>





















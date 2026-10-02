@extends('layouts.app')

@section('title', 'Paramètres')

@php
    $aUnMotDePasse = \App\Http\Controllers\SettingsController::aUnMotDePasse($user);
    $themes = [
        'light' => ['Clair', 'Fond blanc, idéal en journée'],
        'dark' => ['Sombre', 'Moins éblouissant le soir'],
        'auto' => ['Automatique', 'Suit le réglage de votre appareil'],
    ];
    $autresAppareils = $appareils->where('actuel', false)->count();
@endphp

@section('content')
<div class="page-header">
    <div>
        <h1>Paramètres</h1>
        <p>Apparence de l’application et sécurité de votre compte.</p>
    </div>
</div>

<div class="settings-col">
    <section class="card mb-4" aria-labelledby="titre-apparence">
        <div class="card-header">
            <h5 id="titre-apparence">Apparence</h5>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('settings.appearance.update') }}" id="form-theme">
                @csrf
                @method('PUT')
                <fieldset>
                    <legend class="form-label fs-6">Thème</legend>
                    <div class="row g-3">
                        @foreach($themes as $valeur => [$libelle, $description])
                            <div class="col-sm-4">
                                <input type="radio" class="btn-check" name="theme" id="theme-{{ $valeur }}" value="{{ $valeur }}" @checked(($user->theme ?: 'light') === $valeur)>
                                <label class="theme-choix" for="theme-{{ $valeur }}">
                                    <span class="theme-apercu theme-apercu-{{ $valeur }}" aria-hidden="true"><span></span><span></span></span>
                                    <strong>{{ $libelle }}</strong>
                                    <small>{{ $description }}</small>
                                </label>
                            </div>
                        @endforeach
                    </div>
                </fieldset>

                <fieldset class="mt-4">
                    <legend class="form-label fs-6">Couleur principale</legend>
                    <div class="d-flex flex-wrap gap-2">
                        @foreach(\App\Http\Controllers\SettingsController::COULEURS as $valeur => $libelle)
                            <input type="radio" class="btn-check" name="couleur_accent" id="couleur-{{ $valeur }}" value="{{ $valeur }}" @checked(($user->couleur_accent ?: 'vert') === $valeur)>
                            <label class="couleur-choix" for="couleur-{{ $valeur }}" data-accent="{{ $valeur }}">
                                <span class="couleur-pastille" aria-hidden="true"></span>{{ $libelle }}
                            </label>
                        @endforeach
                    </div>
                    <div class="form-text">Menu, boutons et liens. Les couleurs qui ont un sens (collecté, urgent…) ne changent pas.</div>
                </fieldset>

                <fieldset class="mt-4">
                    <legend class="form-label fs-6">Taille du texte</legend>
                    <div class="btn-group" role="group">
                        @foreach(\App\Http\Controllers\SettingsController::TAILLES as $valeur => $libelle)
                            <input type="radio" class="btn-check" name="taille_texte" id="taille-{{ $valeur }}" value="{{ $valeur }}" @checked(($user->taille_texte ?: 'normale') === $valeur)>
                            <label class="btn btn-outline-primary taille-{{ $valeur }}" for="taille-{{ $valeur }}">{{ $libelle }}</label>
                        @endforeach
                    </div>
                </fieldset>
                <noscript><button type="submit" class="btn btn-primary mt-3">Enregistrer</button></noscript>
            </form>
        </div>
    </section>

    @if($user->role === 'citoyen')
        <section class="card mb-4" aria-labelledby="titre-notifications">
            <div class="card-header">
                <h5 id="titre-notifications">Notifications</h5>
            </div>
            <form method="POST" action="{{ route('settings.notifications') }}" class="card-body" id="form-notifications">
                @csrf
                @method('PUT')
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" role="switch" id="rappel_collecte" name="rappel_collecte" value="1"
                           @checked(($user->notification_preferences['rappel_collecte'] ?? true) !== false)
                           onchange="this.form.submit()">
                    <label class="form-check-label" for="rappel_collecte">
                        Me prévenir la veille des collectes dans mon quartier
                        <span class="d-block small text-body-secondary">
                            @if($user->quartier)
                                Quartier : {{ $user->quartier }}. Vous pouvez le modifier dans <a href="{{ route('profile.edit') }}">Mon profil</a>.
                            @else
                                Indiquez votre quartier dans <a href="{{ route('profile.edit') }}">Mon profil</a> pour recevoir les rappels.
                            @endif
                        </span>
                    </label>
                </div>
                <noscript><button type="submit" class="btn btn-sm btn-primary mt-2">Enregistrer</button></noscript>
            </form>
        </section>
    @endif

    <section id="securite" aria-labelledby="titre-securite">
        <h2 class="fs-6 text-uppercase text-body-secondary fw-semibold mb-3" id="titre-securite">Sécurité</h2>

        <div class="card mb-4">
            <div class="card-header">
                <h5>{{ $aUnMotDePasse ? 'Mot de passe' : 'Définir un mot de passe' }}</h5>
            </div>
            <div class="card-body">
                @unless($aUnMotDePasse)
                    <p class="small text-body-secondary">
                        Votre compte a été créé avec Google. Choisissez un mot de passe si vous souhaitez aussi
                        vous connecter avec votre adresse e-mail.
                    </p>
                @endunless

                <form method="POST" action="{{ route('settings.password') }}">
                    @csrf
                    @method('PUT')
                    <div class="row g-3">
                        @if($aUnMotDePasse)
                            <div class="col-12 col-md-6">
                                <label for="current_password" class="form-label">Mot de passe actuel</label>
                                <div class="password-field">
                                    <input type="password" class="form-control @error('current_password') is-invalid @enderror" id="current_password" name="current_password" required autocomplete="current-password">
                                    <button type="button" class="toggle-password" data-target="current_password" aria-label="Afficher le mot de passe"><i class="far fa-eye"></i></button>
                                    @error('current_password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="w-100 m-0"></div>
                        @endif
                        <div class="col-md-6">
                            <label for="new_password" class="form-label">Nouveau mot de passe</label>
                            <div class="password-field">
                                <input type="password" class="form-control @error('password') is-invalid @enderror" id="new_password" name="password" required minlength="8" autocomplete="new-password" aria-describedby="aide-mdp">
                                <button type="button" class="toggle-password" data-target="new_password" aria-label="Afficher le mot de passe"><i class="far fa-eye"></i></button>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-text" id="aide-mdp">8 caractères minimum.</div>
                        </div>
                        <div class="col-md-6">
                            <label for="password_confirmation" class="form-label">Confirmer le mot de passe</label>
                            <div class="password-field">
                                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required autocomplete="new-password">
                                <button type="button" class="toggle-password" data-target="password_confirmation" aria-label="Afficher le mot de passe"><i class="far fa-eye"></i></button>
                            </div>
                        </div>
                    </div>
                    <div class="border-top mt-4 pt-3 d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">{{ $aUnMotDePasse ? 'Modifier le mot de passe' : 'Définir le mot de passe' }}</button>
                    </div>
                </form>
            </div>
        </div>

        @if($appareils->isNotEmpty())
            <div class="card">
                <div class="card-header">
                    <h5>Appareils connectés</h5>
                </div>
                <ul class="list-unstyled mb-0 appareils">
                    @foreach($appareils as $appareil)
                        <li>
                            <span class="stat-icon {{ $appareil->actuel ? 'tone-green' : 'tone-slate' }}" aria-hidden="true">
                                <i class="fas {{ $appareil->mobile ? 'fa-mobile-screen' : 'fa-desktop' }}"></i>
                            </span>
                            <div class="min-w-0">
                                <div class="fw-medium">{{ $appareil->navigateur }} sur {{ $appareil->systeme }}</div>
                                <div class="small text-body-secondary">
                                    {{ $appareil->ip }} ·
                                    @if($appareil->actuel)
                                        <span class="text-success fw-medium">Cet appareil</span>
                                    @else
                                        Actif {{ $appareil->derniere_activite->diffForHumans() }}
                                    @endif
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>

                @if($autresAppareils > 0)
                    <div class="card-body border-top">
                        <form method="POST" action="{{ route('settings.sessions.destroy') }}" class="row g-2 align-items-end">
                            @csrf
                            @method('DELETE')
                            @if($aUnMotDePasse)
                                <div class="col-sm">
                                    <label for="password_session" class="form-label small">Confirmez avec votre mot de passe</label>
                                    <input type="password" class="form-control form-control-sm @error('password_session') is-invalid @enderror" id="password_session" name="password_session" required autocomplete="current-password">
                                    @error('password_session')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            @endif
                            <div class="col-sm-auto">
                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                    Déconnecter {{ $autresAppareils > 1 ? 'les ' . $autresAppareils . ' autres appareils' : 'l’autre appareil' }}
                                </button>
                            </div>
                        </form>
                    </div>
                @endif
            </div>
        @endif
    </section>

    <section id="donnees" class="mt-4" aria-labelledby="titre-donnees">
        <h2 class="fs-6 text-uppercase text-body-secondary fw-semibold mb-3" id="titre-donnees">Mes données</h2>
        <div class="card">
            <div class="card-body d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div>
                    <div class="fw-medium">Télécharger mes données</div>
                    <div class="small text-body-secondary">Profil, signalements, demandes et plaintes, dans un fichier lisible (JSON).</div>
                </div>
                <a href="{{ route('settings.donnees') }}" class="btn btn-sm btn-outline-primary">Télécharger</a>
            </div>

            @if($user->role === 'citoyen')
                <div class="card-body border-top">
                    <div class="fw-medium">Supprimer mon compte</div>
                    <p class="small text-body-secondary">
                        Vos informations personnelles sont effacées et vous ne pourrez plus vous connecter.
                        Vos signalements restent utiles au service mais ne sont plus rattachés à votre nom.
                        <a href="{{ route('legal.confidentialite') }}">En savoir plus</a>
                    </p>
                    <form method="POST" action="{{ route('settings.compte.destroy') }}" class="row g-2 align-items-end"
                          onsubmit="return confirm('Supprimer définitivement votre compte ?')">
                        @csrf
                        @method('DELETE')
                        <div class="col-sm">
                            @if($aUnMotDePasse)
                                <label for="password_suppression" class="form-label small">Confirmez avec votre mot de passe</label>
                                <input type="password" class="form-control form-control-sm @error('password_suppression') is-invalid @enderror" id="password_suppression" name="password_suppression" required autocomplete="current-password">
                                @error('password_suppression')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            @else
                                <label for="confirmation" class="form-label small">Tapez SUPPRIMER pour confirmer</label>
                                <input type="text" class="form-control form-control-sm @error('confirmation') is-invalid @enderror" id="confirmation" name="confirmation" required autocomplete="off">
                                @error('confirmation')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            @endif
                        </div>
                        <div class="col-sm-auto">
                            <button type="submit" class="btn btn-sm btn-outline-danger">Supprimer mon compte</button>
                        </div>
                    </form>
                </div>
            @endif
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script>
    // L'apparence change dès qu'on choisit, puis le choix est enregistré sur le compte
    document.querySelectorAll('#form-theme input[type="radio"]').forEach(function (radio) {
        radio.addEventListener('change', function () {
            var html = document.documentElement;
            if (radio.name === 'couleur_accent') html.setAttribute('data-accent', radio.value);
            if (radio.name === 'taille_texte') html.setAttribute('data-taille', radio.value);
            if (radio.name === 'theme' && radio.value !== 'auto') html.setAttribute('data-bs-theme', radio.value);
            document.getElementById('form-theme').submit();
        });
    });
</script>
@endpush

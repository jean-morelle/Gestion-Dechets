@extends('layouts.page')

@section('title', 'Politique de confidentialité')

@php
    // Valeur de config/collectplus.php, ou repère visible tant qu'elle n'est pas renseignée
    $info = fn (string $cle) => config("collectplus.$cle")
        ? e(config("collectplus.$cle"))
        : '<span class="a-completer">[à compléter]</span>';
@endphp

@section('content')
<h1 class="fs-3">Politique de confidentialité</h1>
<p class="text-body-secondary">Dernière mise à jour : {{ \Illuminate\Support\Carbon::parse(config('collectplus.date_conditions'))->translatedFormat('j F Y') }}</p>

<p>
    {{ config('app.name') }} permet aux habitants de signaler des dépôts de déchets, de demander une collecte et de suivre le traitement
    de leurs demandes, et aux agents de la commune d’organiser les tournées. Cette page explique quelles données sont utilisées, pourquoi,
    et comment exercer vos droits, conformément à la loi togolaise n° 2019-014 du 29 octobre 2019 relative à la protection des données
    à caractère personnel.
</p>

<h2>Qui est responsable de vos données ?</h2>
<p>
    Le responsable du traitement est {!! $info('exploitant') !!}, {!! $info('adresse') !!}.<br>
    Contact pour toute question sur vos données : {!! $info('email_contact') !!}.<br>
    Déclaration auprès de l’Instance de protection des données à caractère personnel (IPDCP) : {!! $info('declaration_ipdcp') !!}.
</p>

<h2>Quelles données sont utilisées ?</h2>
<ul>
    <li><strong>Votre compte</strong> : nom, adresse e-mail, téléphone, quartier, adresse, photo de profil si vous en ajoutez une.</li>
    <li><strong>Vos signalements et demandes</strong> : description, adresse, position sur la carte, photos que vous joignez.</li>
    <li><strong>Pour les agents et collecteurs</strong> : tournées réalisées, photos et position GPS au moment de chaque passage, incidents signalés.</li>
    <li><strong>Données techniques</strong> : adresse IP, type de navigateur et date de connexion, pour la sécurité du compte (liste des appareils connectés, blocage après plusieurs mots de passe erronés).</li>
</ul>
<p>Votre position n’est relevée que lorsque vous le demandez (bouton « Ma position ») ou, pour les collecteurs, au moment de valider un passage. Elle n’est jamais suivie en continu.</p>

<h2>Pourquoi ?</h2>
<ul>
    <li>traiter vos signalements, demandes de collecte et plaintes, et vous tenir informé de leur suivi ;</li>
    <li>organiser les tournées de collecte et vérifier qu’elles ont été réalisées ;</li>
    <li>vous prévenir la veille des collectes dans votre quartier (vous pouvez le désactiver dans Paramètres) ;</li>
    <li>établir des statistiques d’activité par quartier, sans nom ni coordonnées personnelles.</li>
</ul>
<p>Ces traitements relèvent de la mission de service public de gestion des déchets confiée à la commune.</p>

<h2>Qui y a accès ?</h2>
<p>
    Uniquement les agents habilités de la commune. Les collecteurs voient l’adresse et la position des points où ils doivent intervenir.
    Vos données ne sont ni vendues, ni cédées, ni utilisées à des fins publicitaires.
</p>
<p>
    Hébergement des serveurs : {!! $info('hebergeur') !!}.<br>
    Les fonds de carte proviennent d’OpenStreetMap : votre navigateur contacte ses serveurs pour les afficher, comme pour la recherche
    d’adresse. Si vous choisissez « Continuer avec Google », Google vous authentifie et nous transmet votre nom et votre adresse e-mail.
</p>

<h2>Combien de temps ?</h2>
<ul>
    <li>Compte : tant qu’il est actif. Vous pouvez le supprimer à tout moment.</li>
    <li>Signalements, demandes et plaintes : conservés pour le suivi du service, puis anonymisés lorsque vous supprimez votre compte.</li>
    <li>Données de connexion : le temps de la session, et dans les journaux techniques de sécurité.</li>
</ul>

<h2>Vos droits</h2>
<p>Vous pouvez à tout moment :</p>
<ul>
    <li><strong>consulter et corriger</strong> vos informations dans <em>Mon profil</em> ;</li>
    <li><strong>télécharger</strong> toutes vos données (Paramètres → Mes données) ;</li>
    <li><strong>supprimer votre compte</strong> (Paramètres → Mes données). Vos signalements restent utiles au service mais ne sont plus rattachés à votre nom ;</li>
    <li>vous <strong>opposer</strong> aux rappels de collecte (Paramètres → Notifications).</li>
</ul>
<p>
    Pour toute autre demande, écrivez à {!! $info('email_contact') !!}. Si vous estimez que vos droits ne sont pas respectés,
    vous pouvez saisir l’Instance de protection des données à caractère personnel (IPDCP).
</p>

<h2>Cookies</h2>
<p>
    L’application n’utilise que les cookies nécessaires à son fonctionnement : maintien de votre connexion, protection des formulaires
    et option « Rester connecté ». Aucun cookie de mesure d’audience ni de publicité.
</p>
@endsection

@extends('layouts.page')

@section('title', 'Conditions d’utilisation')

@php
    $info = fn (string $cle) => config("collectplus.$cle")
        ? e(config("collectplus.$cle"))
        : '<span class="a-completer">[à compléter]</span>';
@endphp

@section('content')
<h1 class="fs-3">Conditions d’utilisation</h1>
<p class="text-body-secondary">En vigueur au {{ \Illuminate\Support\Carbon::parse(config('collectplus.date_conditions'))->translatedFormat('j F Y') }}</p>

<h2>1. Le service</h2>
<p>
    {{ config('app.name') }} est un service gratuit proposé par {!! $info('exploitant') !!} pour signaler des problèmes de propreté,
    demander une collecte et suivre leur traitement. Il ne remplace pas les numéros d’urgence : en cas de danger immédiat
    (incendie, déchets toxiques, personne blessée), contactez les secours.
</p>

<h2>2. Votre compte</h2>
<p>
    L’inscription est ouverte à toute personne disposant d’une adresse e-mail. Vous êtes responsable de la confidentialité de votre
    mot de passe et des actions réalisées depuis votre compte. Les comptes des agents et collecteurs sont créés par la commune.
</p>

<h2>3. Ce que vous vous engagez à faire</h2>
<ul>
    <li>donner des informations exactes, en particulier l’emplacement des déchets signalés ;</li>
    <li>publier des photos des déchets uniquement : évitez de photographier des personnes reconnaissables ou des plaques d’immatriculation ;</li>
    <li>rester courtois dans vos messages et plaintes ;</li>
    <li>ne pas envoyer de signalements fictifs ou répétés pour un même problème.</li>
</ul>

<h2>4. Traitement de vos demandes</h2>
<p>
    La commune s’efforce de traiter chaque signalement et chaque demande dans les meilleurs délais, en fonction des moyens disponibles
    et des priorités de salubrité. Le suivi affiché dans l’application reflète l’état réel du traitement. Une demande peut être refusée
    ou classée sans suite ; la raison vous est alors indiquée.
</p>

<h2>5. Abus</h2>
<p>
    En cas d’usage abusif (fausses déclarations, propos injurieux, envois massifs), la commune peut suspendre le compte concerné,
    après avoir tenté de contacter son titulaire lorsque c’est possible.
</p>

<h2>6. Données personnelles</h2>
<p>Leur utilisation est décrite dans la <a href="{{ route('legal.confidentialite') }}">politique de confidentialité</a>.</p>

<h2>7. Évolution des conditions</h2>
<p>Ces conditions peuvent évoluer. La date de mise à jour figure en haut de cette page.</p>

<h2>8. Contact et droit applicable</h2>
<p>
    Contact : {!! $info('email_contact') !!} · {!! $info('telephone') !!}.<br>
    Ces conditions sont régies par le droit togolais.
</p>
@endsection

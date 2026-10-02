@extends('layouts.app')

@section('title', 'Plaintes')

@php
    $statuts = \App\Http\Controllers\AdminPlainteController::STATUTS;
@endphp

@section('content')
<div class="page-header">
    <div>
        <h1>Plaintes</h1>
        <p>Réclamations des habitants sur le service de collecte.</p>
    </div>
</div>

<ul class="nav nav-pills small mb-3">
    @foreach(['a_traiter' => 'À traiter', 'repondues' => 'Répondues', 'toutes' => 'Toutes'] as $valeur => $libelle)
        <li class="nav-item">
            <a class="nav-link {{ $statut === $valeur ? 'active' : '' }}" href="{{ route('admin.plaintes.index', ['statut' => $valeur]) }}">
                {{ $libelle }}
                @if($valeur === 'a_traiter' && $aTraiter > 0)<span class="badge rounded-pill text-bg-light ms-1">{{ $aTraiter }}</span>@endif
            </a>
        </li>
    @endforeach
</ul>

<div class="card">
    @if($plaintes->isEmpty())
        <div class="empty-state">
            <i class="fas fa-comment-dots" aria-hidden="true"></i>
            <p>{{ $statut === 'a_traiter' ? 'Aucune plainte en attente de réponse.' : 'Aucune plainte.' }}</p>
        </div>
    @else
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Plainte</th>
                        <th>Quartier</th>
                        <th>Priorité</th>
                        <th>Reçue</th>
                        <th>Statut</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($plaintes as $p)
                        <tr>
                            <td class="min-w-0">
                                <a href="{{ route('admin.plaintes.show', $p) }}" class="fw-medium">{{ $p->sujet }}</a>
                                <div class="small text-body-secondary">{{ $p->type_plainte_label }} · {{ $p->user->name ?? 'Compte supprimé' }}</div>
                            </td>
                            <td>{{ $p->quartier }}</td>
                            <td><span class="badge {{ in_array($p->priorite, ['urgente', 'elevee']) ? 'tone-red' : 'tone-slate' }}">{{ $p->priorite_label }}</span></td>
                            <td class="text-nowrap">{{ $p->created_at->translatedFormat('j M') }}<div class="small text-body-secondary">{{ $p->created_at->diffForHumans() }}</div></td>
                            <td><span class="badge {{ ['en_attente' => 'tone-amber', 'en_cours' => 'tone-blue', 'traite' => 'tone-green'][$p->statut] ?? 'tone-slate' }}">{{ $statuts[$p->statut] ?? $p->statut }}</span></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
<div class="mt-3">{{ $plaintes->links() }}</div>
@endsection

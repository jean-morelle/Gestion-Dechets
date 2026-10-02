@extends('layouts.app')

@section('title', 'Demandes de collecte')

@section('content')
<div class="page-header">
    <div>
        <h1>Demandes de collecte</h1>
        <p>Enlèvements demandés par les habitants : encombrants, déchets verts, déménagements…</p>
    </div>
</div>

<div class="d-flex flex-wrap justify-content-between gap-2 mb-3">
    <ul class="nav nav-pills small">
        @foreach(['a_traiter' => 'À traiter', 'planifiees' => 'Planifiées', 'terminees' => 'Effectuées', 'refusees' => 'Refusées', 'toutes' => 'Toutes'] as $valeur => $libelle)
            <li class="nav-item">
                <a class="nav-link {{ $statut === $valeur ? 'active' : '' }}" href="{{ route('admin.demandes.index', array_filter(['statut' => $valeur, 'quartier' => request('quartier')])) }}">
                    {{ $libelle }}
                    @if(isset($compteurs[$valeur]) && $compteurs[$valeur] > 0)<span class="badge rounded-pill text-bg-light ms-1">{{ $compteurs[$valeur] }}</span>@endif
                </a>
            </li>
        @endforeach
    </ul>
    <form method="GET" class="d-flex gap-2">
        <input type="hidden" name="statut" value="{{ $statut }}">
        <label for="filtre-quartier" class="visually-hidden">Quartier</label>
        <select class="form-select form-select-sm" id="filtre-quartier" name="quartier" onchange="this.form.submit()">
            <option value="">Tous les quartiers</option>
            @foreach($quartiers as $q)
                <option value="{{ $q }}" @selected(request('quartier') === $q)>{{ $q }}</option>
            @endforeach
        </select>
    </form>
</div>

<div class="card">
    @if($demandes->isEmpty())
        <div class="empty-state">
            <i class="fas fa-truck-ramp-box" aria-hidden="true"></i>
            <p>{{ $statut === 'a_traiter' ? 'Aucune demande en attente. Tout est traité.' : 'Aucune demande dans cette catégorie.' }}</p>
        </div>
    @else
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Demande</th>
                        <th>Quartier</th>
                        <th>Urgence</th>
                        <th>Reçue</th>
                        <th>Statut</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($demandes as $demande)
                        <tr>
                            <td class="min-w-0">
                                <a href="{{ route('admin.demandes.show', $demande) }}" class="fw-medium">{{ $demande->objet }}</a>
                                <div class="small text-body-secondary">{{ $demande->type_collecte_label }} · {{ $demande->user->name ?? 'Compte supprimé' }}</div>
                            </td>
                            <td>{{ $demande->quartier }}</td>
                            <td><span class="badge {{ $demande->urgence_tone }}">{{ $demande->urgence_label }}</span></td>
                            <td class="text-nowrap">
                                {{ $demande->created_at->translatedFormat('j M') }}
                                <div class="small text-body-secondary">{{ $demande->created_at->diffForHumans() }}</div>
                            </td>
                            <td>
                                <span class="badge {{ $demande->statut_tone }}">{{ $demande->statut_label }}</span>
                                @if($demande->date_collecte_prevue && $demande->statut === 'accepte')
                                    <div class="small text-body-secondary">le {{ $demande->date_collecte_prevue->translatedFormat('j M') }}</div>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
<div class="mt-3">{{ $demandes->links() }}</div>
@endsection

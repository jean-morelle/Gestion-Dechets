@extends('layouts.app')

@section('title', 'Signalements')

@section('content')
<div class="page-header">
    <div>
        <h1>Signalements</h1>
        <p>Dépôts sauvages et bacs débordants signalés par les habitants.</p>
    </div>
</div>

<div class="d-flex flex-wrap justify-content-between gap-2 mb-3">
    <ul class="nav nav-pills small">
        @foreach(['a_traiter' => 'À traiter', 'traites' => 'Traités', 'sans_suite' => 'Sans suite', 'tous' => 'Tous'] as $valeur => $libelle)
            <li class="nav-item">
                <a class="nav-link {{ $statut === $valeur ? 'active' : '' }}" href="{{ route('admin.signalements.index', array_filter(['statut' => $valeur, 'quartier' => request('quartier')])) }}">
                    {{ $libelle }}
                    @if($valeur === 'a_traiter' && $aTraiter > 0)<span class="badge rounded-pill text-bg-light ms-1">{{ $aTraiter }}</span>@endif
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
    @if($signalements->isEmpty())
        <div class="empty-state">
            <i class="fas fa-triangle-exclamation" aria-hidden="true"></i>
            <p>{{ $statut === 'a_traiter' ? 'Aucun signalement en attente.' : 'Aucun signalement dans cette catégorie.' }}</p>
        </div>
    @else
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Signalement</th>
                        <th>Quartier</th>
                        <th>Urgence</th>
                        <th>Reçu</th>
                        <th>Statut</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($signalements as $s)
                        <tr>
                            <td class="min-w-0">
                                <a href="{{ route('admin.signalements.show', $s) }}" class="fw-medium">{{ $s->type_dechet_label }}</a>
                                <div class="small text-body-secondary text-truncate" style="max-width: 320px">{{ $s->description }}</div>
                            </td>
                            <td>{{ $s->quartier }}</td>
                            <td><span class="badge {{ in_array($s->priorite, ['urgente', 'elevee']) ? 'tone-red' : 'tone-slate' }}">{{ $s->priorite_label }}</span></td>
                            <td class="text-nowrap">
                                {{ $s->created_at->translatedFormat('j M') }}
                                <div class="small text-body-secondary">{{ $s->created_at->diffForHumans() }}</div>
                            </td>
                            <td><span class="badge {{ ['en_attente' => 'tone-amber', 'en_cours' => 'tone-blue', 'traite' => 'tone-green'][$s->statut] ?? 'tone-slate' }}">{{ \App\Http\Controllers\AdminSignalementController::STATUTS[$s->statut] ?? $s->statut }}</span></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
<div class="mt-3">{{ $signalements->links() }}</div>
@endsection

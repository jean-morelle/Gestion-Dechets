@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header py-2 d-flex justify-content-between align-items-center">
                    <h6 class="card-title mb-0">Calendrier des Collectes</h6>
                    <a href="{{ route('admin.calendrier.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus me-1"></i> Créer
                    </a>
                </div>
                <div class="card-body py-3">
                    @if(isset($collectes) && count($collectes) > 0)
                    <table class="table table-hover table-sm">
                        <thead class="table-light">
                            <tr>
                                <th>Nom</th>
                                <th>Type</th>
                                <th>Quartier</th>
                                <th>Fréquence</th>
                                <th>Jour</th>
                                <th>Heure</th>
                                <th>Statut</th>
                                <th>Responsable</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($collectes as $calendrier)
                            <tr>
                                <td>{{ Str::limit($calendrier->nom, 30) }}</td>
                                <td>
                                    <span class="badge {{ $calendrier->type_collecte_class ?? 'bg-info' }}">
                                        {{ $calendrier->type_collecte_label ?? $calendrier->type_collecte }}
                                    </span>
                                </td>
                                <td>{{ Str::limit($calendrier->quartier, 15) }}</td>
                                <td>{{ $calendrier->frequence_label ?? $calendrier->frequence }}</td>
                                <td>{{ $calendrier->jour_semaine_label ?? 'Non défini' }}</td>
                                <td>{{ $calendrier->heure_debut ?? '07:00' }} - {{ $calendrier->heure_fin ?? '18:00' }}</td>
                                <td>
                                    <span class="badge {{ $calendrier->statut_class ?? 'bg-success' }}">
                                        {{ $calendrier->statut_label ?? 'Actif' }}
                                    </span>
                                </td>
                                <td>{{ Str::limit($calendrier->responsable->name ?? 'Non assigné', 15) }}</td>
                                <td>
                                    <a href="#" class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="#" class="btn btn-outline-danger btn-sm">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @else
                    <div class="text-center py-4">
                        <i class="fas fa-calendar-alt fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Aucun calendrier de collecte</p>
                        <a href="{{ route('admin.calendrier.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Créer un calendrier
                        </a>
                    </div>
                    @endif

                    @if(isset($collectes) && $collectes->hasPages())
                    <div class="text-center mt-3">
                        <a href="{{ $collectes->nextPageUrl() }}" class="btn btn-outline-primary">Suivant</a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
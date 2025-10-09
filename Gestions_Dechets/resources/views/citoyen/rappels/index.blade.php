@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header py-2 d-flex justify-content-between align-items-center">
                    <h6 class="card-title mb-0">Rappels automatiques</h6>
                    <a href="{{ route('citoyen.rappels.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus me-1"></i>Nouveau rappel
                    </a>
                </div>
                <div class="card-body py-2">
                    @if($rappels->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Calendrier</th>
                                        <th>Type</th>
                                        <th>Délai</th>
                                        <th>Statut</th>
                                        <th>Dernier envoi</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($rappels as $rappel)
                                    <tr>
                                        <td>
                                            <div>
                                                <strong>{{ $rappel->calendrier->nom }}</strong>
                                                <br>
                                                <small class="text-muted">{{ $rappel->calendrier->quartier }} - {{ $rappel->calendrier->type_collecte_label }}</small>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $rappel->type_rappel === 'email' ? 'primary' : ($rappel->type_rappel === 'sms' ? 'success' : 'info') }}">
                                                {{ ucfirst($rappel->type_rappel) }}
                                            </span>
                                        </td>
                                        <td>
                                            {{ $rappel->delai_heures }}h avant
                                        </td>
                                        <td>
                                            @if($rappel->actif)
                                                <span class="badge bg-success">Actif</span>
                                            @else
                                                <span class="badge bg-secondary">Inactif</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($rappel->derniere_envoi)
                                                {{ $rappel->derniere_envoi->format('d/m/Y H:i') }}
                                            @else
                                                <span class="text-muted">Jamais</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('citoyen.rappels.show', $rappel) }}" class="btn btn-outline-primary">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('citoyen.rappels.edit', $rappel) }}" class="btn btn-outline-secondary">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form method="POST" action="{{ route('citoyen.rappels.toggle', $rappel) }}" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-outline-{{ $rappel->actif ? 'warning' : 'success' }}" 
                                                            title="{{ $rappel->actif ? 'Désactiver' : 'Activer' }}">
                                                        <i class="fas fa-{{ $rappel->actif ? 'pause' : 'play' }}"></i>
                                                    </button>
                                                </form>
                                                <form method="POST" action="{{ route('citoyen.rappels.destroy', $rappel) }}" class="d-inline"
                                                      onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce rappel ?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-outline-danger">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        <div class="d-flex justify-content-center mt-3">
                            {{ $rappels->links() }}
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-bell-slash fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Aucun rappel configuré</p>
                            <a href="{{ route('citoyen.rappels.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-1"></i>Créer un rappel
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


















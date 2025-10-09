@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Itinéraires</h2>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.itineraires.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Créer un itinéraire
                    </a>
                    <select class="form-select" style="width: auto;" onchange="filterByStatus(this.value)">
                        <option value="">Tous les statuts</option>
                        <option value="planifie">Planifié</option>
                        <option value="en_cours">En cours</option>
                        <option value="termine">Terminé</option>
                        <option value="annule">Annulé</option>
                    </select>
                </div>
            </div>

            <!-- Liste des itinéraires -->
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Nom</th>
                                    <th>Description</th>
                                    <th>Type</th>
                                    <th>Collecteur</th>
                                    <th>Statut</th>
                                    <th>Date prévue</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($itineraires as $itineraire)
                                <tr>
                                    <td>{{ $itineraire->nom }}</td>
                                    <td>{{ Str::limit($itineraire->description, 50) }}</td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $itineraire->type_label }}</span>
                                    </td>
                                    <td>{{ $itineraire->collecteur->name ?? 'Non assigné' }}</td>
                                    <td>
                                        <span class="{{ $itineraire->statut_class }}">
                                            {{ $itineraire->statut_label }}
                                        </span>
                                    </td>
                                    <td>{{ $itineraire->date_debut ? $itineraire->date_debut->format('d/m/Y') : '-' }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.itineraires.edit', $itineraire) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-edit"></i> {{ __('app.edit') ?? 'Modifier' }}
                                            </a>
                                            <form method="POST" action="{{ route('admin.itineraires.destroy', $itineraire) }}" 
                                                  style="display: inline;" onsubmit="return confirm('{{ __('routes.delete_message') ?? 'Êtes-vous sûr de vouloir supprimer cet itinéraire ?' }}')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted">Aucun itinéraire pour l'instant.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    @if($itineraires->hasPages())
                    <div class="d-flex justify-content-center mt-4">
                        {{ $itineraires->links() }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function filterByStatus(status) {
    const url = new URL(window.location);
    if (status) {
        url.searchParams.set('statut', status);
    } else {
        url.searchParams.delete('statut');
    }
    window.location.href = url.toString();
}
</script>
@endsection








































































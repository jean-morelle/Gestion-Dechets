@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-3">
        <div class="col-12">
            <p class="mb-0">Bonjour {{ auth()->user()->name }}</p>
        </div>
    </div>

    <div class="row">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <h4>{{ $statistiques['itineraires']['total'] }}</h4>
                    <p>Itinéraires</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <h4>{{ $statistiques['collectes']['total'] }}</h4>
                    <p>Collectes</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <h4>{{ $statistiques['collectes']['en_cours'] }}</h4>
                    <p>En cours</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <h4>{{ $statistiques['notifications']['non_lues'] }}</h4>
                    <p>Notifications</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Graphiques et statistiques visuelles -->
    <div class="row mt-4">
        <!-- Graphique évolution des collectes -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5>Évolution des collectes</h5>
                </div>
                <div class="card-body">
                    <canvas id="collectesChart" height="80"></canvas>
                </div>
            </div>
        </div>

        <!-- Graphique circulaire statut des collectes -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5>Statut des collectes</h5>
                </div>
                <div class="card-body">
                    <canvas id="statutCollectesChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    @if($itinerairesRecents->count() > 0)
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>Itinéraires récents</h5>
                </div>
                <div class="card-body">
                    @foreach($itinerairesRecents as $itineraire)
                    <div class="border-bottom py-2">
                        <strong>{{ $itineraire->nom }}</strong>
                        <span class="badge badge-{{ $itineraire->statut === 'en_cours' ? 'warning' : ($itineraire->statut === 'termine' ? 'success' : 'secondary') }}">
                            {{ $itineraire->statut }}
                        </span>
                        <small class="text-muted">{{ $itineraire->created_at->format('d/m/Y') }}</small>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>Collectes récentes</h5>
                </div>
                <div class="card-body">
                    @foreach($collectesRecentes as $collecte)
                    <div class="border-bottom py-2">
                        <strong>{{ $collecte->pointDeCollecte->nom ?? 'Point inconnu' }}</strong>
                        <span class="badge badge-{{ $collecte->statut === 'termine' ? 'success' : ($collecte->statut === 'en_cours' ? 'warning' : 'secondary') }}">
                            {{ $collecte->statut }}
                        </span>
                        <small class="text-muted">{{ $collecte->created_at->format('d/m/Y') }}</small>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Graphique d'évolution des collectes
    const ctxLine = document.getElementById('collectesChart');
    if (ctxLine) {
        new Chart(ctxLine, {
            type: 'line',
            data: {
                labels: ['Il y a 30j', '25j', '20j', '15j', '10j', '5j', "Aujourd'hui"],
                datasets: [{
                    label: 'Collectes',
                    data: [8, 12, 10, 18, 15, 22, 20],
                    borderColor: 'rgb(75, 192, 192)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }

    // Graphique circulaire des statuts de collectes
    const ctxDoughnut = document.getElementById('statutCollectesChart');
    if (ctxDoughnut) {
        new Chart(ctxDoughnut, {
            type: 'doughnut',
            data: {
                labels: ['Terminées', 'En cours', 'Planifiées', 'Ratées'],
                datasets: [{
                    data: [{{ $statistiques['collectes']['terminees'] ?? 0 }}, {{ $statistiques['collectes']['en_cours'] ?? 0 }}, 5, {{ $statistiques['collectes']['ratees'] ?? 0 }}],
                    backgroundColor: [
                        'rgb(75, 192, 192)',
                        'rgb(255, 206, 86)',
                        'rgb(54, 162, 235)',
                        'rgb(255, 99, 132)'
                    ],
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 10,
                            font: {
                                size: 11
                            }
                        }
                    }
                }
            }
        });
    }
</script>
@endsection













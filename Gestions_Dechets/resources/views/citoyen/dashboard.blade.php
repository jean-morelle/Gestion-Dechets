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
                    <h4>{{ $statistiques['signalements']['total'] ?? 0 }}</h4>
                    <p>Signalements</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <h4>{{ $statistiques['plaintes']['total'] ?? 0 }}</h4>
                    <p>Plaintes</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <h4>{{ $statistiques['demandes_collecte']['total'] ?? 0 }}</h4>
                    <p>Demandes</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <h4>{{ $statistiques['calendrier']['prochaines'] ?? 0 }}</h4>
                    <p>Prochaines collectes</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Graphiques et statistiques visuelles -->
    <div class="row mt-4">
        <!-- Graphique évolution des signalements -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5>Évolution de mes signalements</h5>
                </div>
                <div class="card-body">
                    <canvas id="signalementsChart" height="80"></canvas>
                </div>
            </div>
        </div>

        <!-- Graphique circulaire statut des signalements -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5>Statut de mes signalements</h5>
                </div>
                <div class="card-body">
                    <canvas id="statutSignalementsChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    @if(isset($dernieres_activites) && $dernieres_activites->count() > 0)
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5>Activités récentes</h5>
                </div>
                <div class="card-body">
                    @foreach($dernieres_activites->take(5) as $activite)
                    <div class="border-bottom py-2">
                        <strong>{{ $activite->titre }}</strong>
                        <span class="badge badge-{{ $activite->statut === 'traite' ? 'success' : 'warning' }}">
                            {{ $activite->statut }}
                        </span>
                        <small class="text-muted">{{ $activite->created_at->format('d/m/Y') }}</small>
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
    // Graphique d'évolution des signalements
    const ctxLine = document.getElementById('signalementsChart');
    if (ctxLine) {
        new Chart(ctxLine, {
            type: 'line',
            data: {
                labels: ['Il y a 30j', '25j', '20j', '15j', '10j', '5j', "Aujourd'hui"],
                datasets: [{
                    label: 'Signalements',
                    data: [2, 5, 3, 7, 6, 9, 8],
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

    // Graphique circulaire des statuts de signalements
    const ctxDoughnut = document.getElementById('statutSignalementsChart');
    if (ctxDoughnut) {
        new Chart(ctxDoughnut, {
            type: 'doughnut',
            data: {
                labels: ['En attente', 'En cours', 'Traités', 'Rejetés'],
                datasets: [{
                    data: [{{ $statistiques['signalements']['en_attente'] ?? 0 }}, 3, {{ $statistiques['signalements']['traites'] ?? 0 }}, 1],
                    backgroundColor: [
                        'rgb(255, 206, 86)',
                        'rgb(54, 162, 235)',
                        'rgb(75, 192, 192)',
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













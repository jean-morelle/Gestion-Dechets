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
                    <h4>{{ $statistiques['utilisateurs']['total'] }}</h4>
                    <p>Utilisateurs</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <h4>{{ $statistiques['signalements']['total'] }}</h4>
                    <p>Signalements</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <h4>{{ $statistiques['plaintes']['total'] }}</h4>
                    <p>Plaintes</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <h4>{{ $statistiques['itineraires']['total'] }}</h4>
                    <p>Itinéraires</p>
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
                    <h5>Évolution des signalements</h5>
                </div>
                <div class="card-body">
                    <canvas id="signalementsChart" height="80"></canvas>
                </div>
            </div>
        </div>

        <!-- Graphique circulaire des types de déchets -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5>Types de déchets</h5>
                </div>
                <div class="card-body">
                    <canvas id="typesDechetChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    @if($signalementsRecents->count() > 0)
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5>Signalements récents</h5>
                </div>
                <div class="card-body">
                    @foreach($signalementsRecents as $signalement)
                    <div class="border-bottom py-2">
                        <strong>{{ $signalement->type_dechet_label ?? 'Signalement' }}</strong>
                        <span class="badge badge-{{ $signalement->statut === 'traite' ? 'success' : 'warning' }}">
                            {{ $signalement->statut }}
                        </span>
                        <small class="text-muted">{{ $signalement->created_at->format('d/m/Y') }}</small>
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
                    data: [12, 19, 15, 25, 22, 30, 28],
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

    // Graphique circulaire des types de déchets
    const ctxDoughnut = document.getElementById('typesDechetChart');
    if (ctxDoughnut) {
        new Chart(ctxDoughnut, {
            type: 'doughnut',
            data: {
                labels: ['Plastique', 'Organique', 'Verre', 'Métal', 'Papier'],
                datasets: [{
                    data: [30, 25, 15, 20, 10],
                    backgroundColor: [
                        'rgb(54, 162, 235)',
                        'rgb(75, 192, 192)',
                        'rgb(255, 206, 86)',
                        'rgb(255, 99, 132)',
                        'rgb(153, 102, 255)'
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























































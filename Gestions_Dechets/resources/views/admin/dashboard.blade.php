@extends('layouts.app')

@section('title', 'Tableau de bord')

@section('content')
@php $s = $statistiques; @endphp

<div class="page-header">
    <div>
        <h1>Vue d'ensemble</h1>
        <p>{{ ucfirst(now()->translatedFormat('l j F Y')) }}</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.notifications.create') }}" class="btn btn-outline-primary">
            <i class="fas fa-paper-plane me-1" aria-hidden="true"></i> Notifier
        </a>
        <a href="{{ route('admin.itineraires.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1" aria-hidden="true"></i> Nouvel itinéraire
        </a>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-6 col-xl-3">
        <a href="{{ route('admin.signalements.index') }}" class="card stat-card">
            <span class="stat-icon tone-amber"><i class="fas fa-triangle-exclamation" aria-hidden="true"></i></span>
            <span>
                <span class="stat-value d-block">{{ $s['signalements']['en_attente'] }}</span>
                <span class="stat-label">Signalements à traiter sur {{ $s['signalements']['total'] }}</span>
            </span>
        </a>
    </div>
    <div class="col-6 col-xl-3">
        <a href="{{ route('admin.plaintes.index', ['statut' => 'en_attente']) }}" class="card stat-card">
            <span class="stat-icon tone-red"><i class="fas fa-comment-dots" aria-hidden="true"></i></span>
            <span>
                <span class="stat-value d-block">{{ $s['plaintes']['en_attente'] }}</span>
                <span class="stat-label">Plaintes en attente sur {{ $s['plaintes']['total'] }}</span>
            </span>
        </a>
    </div>
    <div class="col-6 col-xl-3">
        <a href="{{ route('admin.itineraires.index') }}" class="card stat-card">
            <span class="stat-icon tone-green"><i class="fas fa-route" aria-hidden="true"></i></span>
            <span>
                <span class="stat-value d-block">{{ $s['itineraires']['actifs'] }}</span>
                <span class="stat-label">Tournées en cours · {{ $s['itineraires']['total'] }} au total</span>
            </span>
        </a>
    </div>
    <div class="col-6 col-xl-3">
        <a href="{{ route('admin.utilisateurs.index') }}" class="card stat-card">
            <span class="stat-icon tone-blue"><i class="fas fa-users" aria-hidden="true"></i></span>
            <span>
                <span class="stat-value d-block">{{ $s['utilisateurs']['total'] }}</span>
                <span class="stat-label">{{ $s['utilisateurs']['citoyens'] }} citoyen{{ $s['utilisateurs']['citoyens'] > 1 ? 's' : '' }} · {{ $s['utilisateurs']['collecteurs'] }} collecteur{{ $s['utilisateurs']['collecteurs'] > 1 ? 's' : '' }}</span>
            </span>
        </a>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-lg-8">
        <div class="card h-100">
            <div class="card-header">
                <h5>Signalements reçus — 30 derniers jours</h5>
            </div>
            <div class="card-body">
                @if(array_sum($evolution['valeurs']) === 0)
                    <div class="empty-state">
                        <i class="fas fa-chart-column d-block" aria-hidden="true"></i>
                        <p class="mb-0">Aucun signalement reçu sur la période.</p>
                    </div>
                @else
                    <div class="chart-box">
                        <canvas id="evolutionChart" role="img" aria-label="Nombre de signalements reçus par jour sur les 30 derniers jours"></canvas>
                    </div>
                @endif
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header">
                <h5>Par type de déchet</h5>
            </div>
            <div class="card-body">
                @if($parType->isEmpty())
                    <div class="empty-state">
                        <i class="fas fa-chart-bar d-block" aria-hidden="true"></i>
                        <p class="mb-0">Pas encore de données.</p>
                    </div>
                @else
                    @php $max = max($parType->max('total'), 1); @endphp
                    <ul class="list-unstyled mb-0">
                        @foreach($parType as $t)
                            <li class="mb-3">
                                <div class="d-flex justify-content-between small mb-1">
                                    <span>{{ $t['label'] }}</span>
                                    <span class="fw-semibold">{{ $t['total'] }}</span>
                                </div>
                                <div class="progress" style="height: 8px" role="progressbar" aria-label="{{ $t['label'] }}" aria-valuenow="{{ $t['total'] }}" aria-valuemin="0" aria-valuemax="{{ $max }}">
                                    <div class="progress-bar bg-primary" style="width: {{ round($t['total'] / $max * 100) }}%"></div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5>Signalements en attente</h5>
                <a href="{{ route('admin.signalements.index') }}" class="small text-decoration-none">Tout voir</a>
            </div>
            @if($signalementsRecents->isEmpty())
                <div class="empty-state">
                    <i class="fas fa-circle-check d-block" aria-hidden="true"></i>
                    <p class="mb-0">Tous les signalements ont été pris en charge.</p>
                </div>
            @else
                <ul class="activity-list">
                    @foreach($signalementsRecents as $signalement)
                        <li>
                            <div class="flex-grow-1 min-w-0">
                                <a href="{{ route('admin.signalements.show', $signalement) }}" class="d-block text-truncate">{{ $signalement->type_dechet_label }}</a>
                                <div class="activity-meta text-truncate">{{ $signalement->quartier ?: $signalement->adresse }} · {{ $signalement->created_at->diffForHumans() }}</div>
                            </div>
                            <a href="{{ route('admin.signalements.show', $signalement) }}" class="btn btn-sm btn-outline-primary">Traiter</a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5>Plaintes en attente</h5>
                <a href="{{ route('admin.plaintes.index') }}" class="small text-decoration-none">Tout voir</a>
            </div>
            @if($plaintesRecentes->isEmpty())
                <div class="empty-state">
                    <i class="fas fa-circle-check d-block" aria-hidden="true"></i>
                    <p class="mb-0">Aucune plainte en attente.</p>
                </div>
            @else
                <ul class="activity-list">
                    @foreach($plaintesRecentes as $plainte)
                        <li>
                            <div class="flex-grow-1 min-w-0">
                                <a href="{{ route('admin.plaintes.show', $plainte) }}" class="d-block text-truncate">{{ $plainte->sujet }}</a>
                                <div class="activity-meta text-truncate">{{ $plainte->quartier ?: $plainte->adresse }} · {{ $plainte->created_at->diffForHumans() }}</div>
                            </div>
                            <a href="{{ route('admin.plaintes.show', $plainte) }}" class="btn btn-sm btn-outline-primary">Traiter</a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
@if(array_sum($evolution['valeurs']) > 0)
<script src="{{ $vendorAsset('chartjs') }}"></script>
<script>
    (function () {
        var styles = getComputedStyle(document.documentElement);
        var primary = styles.getPropertyValue('--bs-primary').trim() || '#1d7a4c';
        var muted = styles.getPropertyValue('--bs-secondary-color').trim();
        var grid = styles.getPropertyValue('--bs-border-color').trim();

        new Chart(document.getElementById('evolutionChart'), {
            type: 'bar',
            data: {
                labels: @json($evolution['labels']),
                datasets: [{
                    label: 'Signalements',
                    data: @json($evolution['valeurs']),
                    backgroundColor: primary,
                    borderRadius: 4,
                    maxBarThickness: 18
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        displayColors: false,
                        callbacks: {
                            label: function (ctx) { return ctx.parsed.y + ' signalement' + (ctx.parsed.y > 1 ? 's' : ''); }
                        }
                    }
                },
                scales: {
                    x: { grid: { display: false }, ticks: { color: muted, maxRotation: 0, autoSkipPadding: 12 } },
                    y: { beginAtZero: true, grid: { color: grid }, border: { display: false }, ticks: { color: muted, precision: 0 } }
                }
            }
        });
    })();
</script>
@endif
@endsection

@extends('layouts.app')

@section('title', 'Détail Itinéraire')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title"><i class="fas fa-route me-2"></i>Itinéraire: {{ $itineraire->nom }}</h4>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('collecteur.itineraires.index') }}">Mes Itinéraires</a></li>
                        <li class="breadcrumb-item active">Détail</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Informations</h5>
                    <div>
                        @php($nextCollecte = collect($collectes ?? [])->first(fn($c) => ($c->statut ?? null) !== 'termine'))
                        @if(isset($nextCollecte->pointDeCollecte) && $nextCollecte->pointDeCollecte->latitude && $nextCollecte->pointDeCollecte->longitude)
                            @php($dest = $nextCollecte->pointDeCollecte->latitude . ',' . $nextCollecte->pointDeCollecte->longitude)
                            <a href="https://www.google.com/maps/dir/?api=1&destination={{ $dest }}&travelmode=driving" target="_blank" rel="noopener" class="btn btn-sm btn-outline-primary me-2">
                                Naviguer vers le prochain point
                            </a>
                        @endif
                        <form action="{{ route('collecteur.itineraires.demarrer', $itineraire->id) }}" method="POST" class="d-inline">
                            @csrf
                            <button class="btn btn-sm btn-primary" {{ $itineraire->statut === 'en_cours' ? 'disabled' : '' }}>
                                Démarrer
                            </button>
                        </form>
                        <form action="{{ route('collecteur.itineraires.terminer', $itineraire->id) }}" method="POST" class="d-inline ms-2">
                            @csrf
                            <button class="btn btn-sm btn-success" {{ $itineraire->statut === 'termine' ? 'disabled' : '' }}>
                                Terminer
                            </button>
                        </form>
                        <a href="{{ route('collecteur.incidents.create', ['itineraire_id' => $itineraire->id]) }}" class="btn btn-sm btn-outline-danger ms-2">
                            Signaler un incident
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Type:</strong> {{ $itineraire->type_label ?? ucfirst($itineraire->type) }}</p>
                            <p><strong>Statut:</strong> <span class="badge {{ $itineraire->statut_class ?? 'bg-info' }}">{{ $itineraire->statut_label ?? ucfirst($itineraire->statut) }}</span></p>
                            <p><strong>Date:</strong> {{ $itineraire->date_debut?->format('d/m/Y') }} - {{ $itineraire->date_fin?->format('d/m/Y') }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Heure:</strong> {{ $itineraire->heure_debut?->format('H:i') }} - {{ $itineraire->heure_fin?->format('H:i') }}</p>
                            <p><strong>Distance estimée:</strong> {{ $itineraire->distance_estimee ?? 'N/A' }} km</p>
                            <p><strong>Durée estimée:</strong> {{ $itineraire->duree_estimee ?? 'N/A' }} min</p>
                        </div>
                    </div>
                </div>
            </div>

            @php($activeTab = request('tab', 'points'))
            <ul class="nav nav-tabs mt-3" role="tablist">
                <li class="nav-item" role="presentation">
                    <a class="nav-link {{ $activeTab==='points' ? 'active' : '' }}" href="{{ request()->fullUrlWithQuery(['tab' => 'points']) }}">Points</a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link {{ $activeTab==='carte' ? 'active' : '' }}" href="{{ request()->fullUrlWithQuery(['tab' => 'carte']) }}">Carte</a>
                </li>
            </ul>

            <div class="card border-top-0">
                <div class="card-body">
                    @if($activeTab==='points')
                        @forelse($collectes ?? [] as $collecte)
                            <div class="d-flex align-items-center border rounded p-2 mb-2">
                                <div class="me-3">
                                    <span class="badge {{ $collecte->statut_class }}">{{ $collecte->statut_label }}</span>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="fw-semibold">{{ $collecte->type_dechet_label ?? 'Collecte' }} — {{ $collecte->pointDeCollecte->adresse ?? 'Point' }}</div>
                                    <small class="text-muted">{{ $collecte->date_collecte?->format('d/m/Y') }} {{ $collecte->heure_debut?->format('H:i') }} - {{ $collecte->heure_fin?->format('H:i') }}</small>
                                </div>
                                <div class="d-flex gap-2">
                                    @if(($collecte->pointDeCollecte->latitude ?? null) && ($collecte->pointDeCollecte->longitude ?? null))
                                        @php($dest = $collecte->pointDeCollecte->latitude . ',' . $collecte->pointDeCollecte->longitude)
                                        <a class="btn btn-sm btn-outline-primary" href="https://www.google.com/maps/dir/?api=1&destination={{ $dest }}&travelmode=driving" target="_blank" rel="noopener" title="Naviguer">
                                            <i class="fas fa-location-arrow"></i>
                                        </a>
                                    @endif
                                    <a class="btn btn-sm btn-outline-secondary" href="{{ route('collecteur.collectes.show', $collecte->id) }}">Ouvrir</a>
                                </div>
                            </div>
                        @empty
                            <p class="text-muted mb-0">Aucune collecte listée pour cet itinéraire.</p>
                        @endforelse
                    @else
                        <div class="d-flex justify-content-end mb-2">
                            <button id="btn_nav_full" type="button" class="btn btn-sm btn-outline-primary">
                                Naviguer toute la tournée
                            </button>
                        </div>
                        <div id="map_itineraire" style="height: 60vh;" class="rounded border"></div>
                        <script>
                        (function(){
                            const points = @json(($pointsCollecte ?? collect())->map(function($p){
                                return ['lat' => (float) $p->latitude, 'lng' => (float) $p->longitude, 'label' => $p->adresse];
                            }));
                            if (!points.length) return;
                            window.itinPoints = points; // pour le bouton navigation

                            const apiKey = "{{ config('services.google_maps.key') }}";
                            const callbackName = 'initItineraireMap_' + Math.random().toString(36).slice(2);

                            window[callbackName] = function(){
                                const map = new google.maps.Map(document.getElementById('map_itineraire'), {
                                    zoom: 13,
                                    center: points[0]
                                });

                                const path = points.map(p => ({ lat: p.lat, lng: p.lng }));
                                const polyline = new google.maps.Polyline({
                                    path,
                                    geodesic: true,
                                    strokeColor: '#0d6efd',
                                    strokeOpacity: 0.9,
                                    strokeWeight: 5
                                });
                                polyline.setMap(map);

                                const bounds = new google.maps.LatLngBounds();
                                points.forEach((p, idx) => {
                                    const marker = new google.maps.Marker({ position: p, map, label: String(idx+1) });
                                    const info = new google.maps.InfoWindow({ content: (idx+1)+'. '+(p.label||'Point') });
                                    marker.addListener('click', () => info.open({ anchor: marker, map }));
                                    bounds.extend(p);
                                });
                                map.fitBounds(bounds, 50);
                            };

                            const script = document.createElement('script');
                            script.src = `https://maps.googleapis.com/maps/api/js?key=${apiKey}&callback=${callbackName}`;
                            script.async = true;
                            document.head.appendChild(script);

                            // Handler bouton navigation complète (cap waypoints 23)
                            document.getElementById('btn_nav_full').addEventListener('click', function(){
                                const pts = window.itinPoints || [];
                                if (!pts.length) { window.open('https://www.google.com/maps', '_blank'); return; }
                                const buildUrl = function(origin){
                                    const dest = pts[pts.length-1].lat + ',' + pts[pts.length-1].lng;
                                    const intermediates = pts.slice(0, -1); // sans destination
                                    // Google URL Directions limite à 23 waypoints max
                                    const maxWaypoints = 23;
                                    const waypointsList = intermediates.slice(0, maxWaypoints).map(p => p.lat + ',' + p.lng);
                                    const waypointsParam = waypointsList.join('|');
                                    const base = `https://www.google.com/maps/dir/?api=1&destination=${encodeURIComponent(dest)}&travelmode=driving`;
                                    return origin
                                        ? base + `&origin=${encodeURIComponent(origin)}&waypoints=${encodeURIComponent(waypointsParam)}`
                                        : base + `&waypoints=${encodeURIComponent(waypointsParam)}`;
                                };

                                if (!navigator.geolocation) {
                                    window.open(buildUrl(null), '_blank');
                                    return;
                                }
                                navigator.geolocation.getCurrentPosition(function(pos){
                                    const origin = pos.coords.latitude + ',' + pos.coords.longitude;
                                    window.open(buildUrl(origin), '_blank');
                                }, function(){
                                    window.open(buildUrl(null), '_blank');
                                }, { enableHighAccuracy: true, timeout: 8000 });
                            });
                        })();
                        </script>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header"><h5 class="card-title mb-0">Actions rapides</h5></div>
                <div class="card-body">
                    <a href="{{ route('collecteur.incidents.create', ['itineraire_id' => $itineraire->id]) }}" class="btn btn-outline-danger w-100 mb-2">
                        Signaler un incident
                    </a>
                    <a href="{{ route('collecteur.itineraires.index') }}" class="btn btn-outline-secondary w-100">Retour à la liste</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection












{{-- Affichage d'un emplacement enregistré, avec lien d'itinéraire --}}
@props([
    'latitude',
    'longitude',
    'libelle' => null,
    'hauteur' => '220px',
])

<div class="cp-carte-apercu" data-carte-apercu
     data-lat="{{ $latitude }}" data-lng="{{ $longitude }}" data-libelle="{{ $libelle }}">
    <div class="cp-carte" style="height: {{ $hauteur }}" aria-label="Carte de l’emplacement"></div>
    <div class="d-flex justify-content-between align-items-center mt-1 small">
        <span class="text-body-secondary">
            <i class="fas fa-map-marker-alt me-1"></i>{{ number_format((float) $latitude, 5) }}, {{ number_format((float) $longitude, 5) }}
        </span>
        <a href="https://www.google.com/maps/dir/?api=1&destination={{ $latitude }},{{ $longitude }}"
           target="_blank" rel="noopener">
            Itinéraire <i class="fas fa-arrow-up-right-from-square ms-1"></i>
        </a>
    </div>
</div>

<x-carte.scripts />

{{--
    Carte de plusieurs points : [{ lat, lng, nom, detail?, url?, etat? }]
    etat : afaire (ambre), fait (vert), rate (rouge), inactif (gris)
--}}
@props([
    'points' => [],
    'relier' => false,
    'numeroter' => false,
    'hauteur' => '360px',
])

<div {{ $attributes }} data-carte-points data-points='@json(array_values(collect($points)->all()))'
     @if($relier) data-relier @endif @if($numeroter) data-numeroter @endif>
    <div class="cp-carte" style="height: {{ $hauteur }}" aria-label="Carte des points"></div>
</div>

<x-carte.scripts />

{{-- Photo de profil, ou initiales (« Kossi Mensah » → « KM ») à défaut --}}
@props(['user', 'taille' => 36])

@php
    $initiales = collect(preg_split('/\s+/', trim($user->name)))
        ->filter()
        ->take(2)
        ->map(fn ($mot) => mb_strtoupper(mb_substr($mot, 0, 1)))
        ->implode('');
    $style = "width:{$taille}px;height:{$taille}px;font-size:" . round($taille * 0.38) . 'px';
@endphp

@if($user->photo)
    <img src="{{ asset('storage/' . $user->photo) }}" alt="" {{ $attributes->merge(['class' => 'avatar object-fit-cover']) }} style="{{ $style }}">
@else
    <span {{ $attributes->merge(['class' => 'avatar']) }} style="{{ $style }}" aria-hidden="true">{{ $initiales }}</span>
@endif

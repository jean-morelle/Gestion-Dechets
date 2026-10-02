{{-- Leaflet + public/js/carte.js, chargés une seule fois par page --}}
@once
    @push('styles')
        <link href="{{ $vendorAsset('leaflet_css') }}" rel="stylesheet">
    @endpush
    @push('scripts')
        <script src="{{ $vendorAsset('leaflet_js') }}"></script>
        <script src="{{ asset('js/carte.js') }}?v={{ filemtime(public_path('js/carte.js')) }}"></script>
    @endpush
@endonce

<?php

/*
|--------------------------------------------------------------------------
| Bibliothèques front-end
|--------------------------------------------------------------------------
|
| Chaque fichier est servi depuis public/vendor s'il a été téléchargé
| (php artisan assets:telecharger), sinon depuis le CDN. L'application
| reste ainsi utilisable sans connexion Internet.
|
*/

return [
    'bootstrap_css' => [
        'local' => 'vendor/bootstrap/bootstrap.min.css',
        'cdn' => 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css',
    ],
    'bootstrap_js' => [
        'local' => 'vendor/bootstrap/bootstrap.bundle.min.js',
        'cdn' => 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js',
    ],
    'fontawesome_css' => [
        'local' => 'vendor/fontawesome/css/all.min.css',
        'cdn' => 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css',
        // Polices référencées par la feuille de style (chemin relatif ../webfonts/)
        'extras' => [
            'vendor/fontawesome/webfonts/fa-solid-900.woff2' => 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/webfonts/fa-solid-900.woff2',
            'vendor/fontawesome/webfonts/fa-solid-900.ttf' => 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/webfonts/fa-solid-900.ttf',
            'vendor/fontawesome/webfonts/fa-regular-400.woff2' => 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/webfonts/fa-regular-400.woff2',
            'vendor/fontawesome/webfonts/fa-regular-400.ttf' => 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/webfonts/fa-regular-400.ttf',
            'vendor/fontawesome/webfonts/fa-brands-400.woff2' => 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/webfonts/fa-brands-400.woff2',
            'vendor/fontawesome/webfonts/fa-brands-400.ttf' => 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/webfonts/fa-brands-400.ttf',
            'vendor/fontawesome/webfonts/fa-v4compatibility.woff2' => 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/webfonts/fa-v4compatibility.woff2',
            'vendor/fontawesome/webfonts/fa-v4compatibility.ttf' => 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/webfonts/fa-v4compatibility.ttf',
        ],
    ],
    'chartjs' => [
        'local' => 'vendor/chartjs/chart.umd.min.js',
        'cdn' => 'https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js',
    ],
    // Cartes (fond OpenStreetMap, aucune clé d'API nécessaire)
    'leaflet_css' => [
        'local' => 'vendor/leaflet/leaflet.css',
        'cdn' => 'https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.css',
    ],
    'leaflet_js' => [
        'local' => 'vendor/leaflet/leaflet.js',
        'cdn' => 'https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.js',
    ],
];

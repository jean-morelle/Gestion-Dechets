/*
 * CollectPlus Togo — cartes Leaflet
 *
 * [data-carte-choix]  : l'utilisateur place un point (clic, glisser, recherche
 *                       ou géolocalisation). Les champs cachés latitude/longitude
 *                       sont remplis, ainsi que l'adresse et le quartier tant que
 *                       l'utilisateur ne les a pas saisis lui-même.
 * [data-carte-apercu] : affichage d'un point enregistré, en lecture seule.
 */
(function () {
    'use strict';

    var LOME = [6.1375, 1.2123];
    var NOMINATIM = 'https://nominatim.openstreetmap.org';

    function fondDeCarte(carte) {
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
        }).addTo(carte);
    }

    function icone() {
        return L.divIcon({
            className: 'cp-pin',
            html: '<span></span>',
            iconSize: [28, 28],
            iconAnchor: [14, 34] // pointe de la goutte
        });
    }

    // Leaflet calcule mal sa taille si la carte était masquée (onglet, modale…)
    function surveillerTaille(carte, el) {
        if ('ResizeObserver' in window) {
            new ResizeObserver(function () { carte.invalidateSize(); }).observe(el);
        }
    }

    function formaterAdresse(a) {
        var rue = [a.house_number, a.road].filter(Boolean).join(' ');
        var lieu = a.neighbourhood || a.suburb || a.quarter || a.city_district;
        var ville = a.city || a.town || a.village;
        return [rue, lieu, ville].filter(Boolean).join(', ');
    }

    function initChoix(racine) {
        var elCarte = racine.querySelector('.cp-carte');
        var champLat = racine.querySelector('[data-champ="lat"]');
        var champLng = racine.querySelector('[data-champ="lng"]');
        var statut = racine.querySelector('[data-role="statut"]');
        var btnRetirer = racine.querySelector('[data-action="retirer"]');
        var btnPosition = racine.querySelector('[data-action="position"]');
        var champRecherche = racine.querySelector('[data-role="recherche"]');
        var btnRecherche = racine.querySelector('[data-action="rechercher"]');
        var form = racine.closest('form');

        var champAdresse = racine.dataset.adresse ? document.querySelector(racine.dataset.adresse) : null;
        var champQuartier = racine.dataset.quartier ? document.querySelector(racine.dataset.quartier) : null;

        // Un champ saisi à la main n'est plus jamais écrasé par la carte
        [champAdresse, champQuartier].forEach(function (champ) {
            if (!champ) return;
            champ.addEventListener('input', function () { champ.dataset.saisiManuellement = '1'; });
        });

        var lat = parseFloat(champLat.value);
        var lng = parseFloat(champLng.value);
        var aUnPoint = !isNaN(lat) && !isNaN(lng);

        var carte = L.map(elCarte, { scrollWheelZoom: false })
            .setView(aUnPoint ? [lat, lng] : LOME, aUnPoint ? 17 : 13);
        fondDeCarte(carte);
        surveillerTaille(carte, elCarte);

        // Zoom molette seulement après un premier clic : la page reste défilable
        carte.once('focus', function () { carte.scrollWheelZoom.enable(); });

        var marqueur = null;
        var cercle = null;
        var requeteAdresse = 0;

        function afficherStatut(texte, erreur) {
            statut.textContent = texte;
            statut.classList.toggle('text-danger', !!erreur);
        }

        function remplir(champ, valeur) {
            if (!champ || !valeur || champ.dataset.saisiManuellement) return;
            champ.value = valeur;
            champ.classList.remove('is-invalid');
        }

        function chercherAdresse(p) {
            var numero = ++requeteAdresse;
            afficherStatut('Recherche de l’adresse…');
            fetch(NOMINATIM + '/reverse?format=jsonv2&zoom=18&accept-language=fr&lat=' + p.lat + '&lon=' + p.lng)
                .then(function (r) { return r.ok ? r.json() : Promise.reject(); })
                .then(function (data) {
                    if (numero !== requeteAdresse) return; // une réponse plus récente est attendue
                    var a = data.address || {};
                    remplir(champAdresse, formaterAdresse(a));
                    remplir(champQuartier, a.neighbourhood || a.suburb || a.quarter || a.city_district);
                    afficherStatut(formaterAdresse(a) || 'Emplacement enregistré.');
                })
                .catch(function () {
                    if (numero === requeteAdresse) afficherStatut('Emplacement enregistré.');
                });
        }

        function placer(p, precision) {
            champLat.value = p.lat.toFixed(7);
            champLng.value = p.lng.toFixed(7);
            racine.classList.remove('is-invalid');

            if (!marqueur) {
                marqueur = L.marker(p, { icon: icone(), draggable: true, keyboard: true }).addTo(carte);
                marqueur.on('dragend', function () { placer(marqueur.getLatLng()); });
            } else {
                marqueur.setLatLng(p);
            }

            if (cercle) { carte.removeLayer(cercle); cercle = null; }
            if (precision) {
                cercle = L.circle(p, { radius: precision, weight: 1, color: '#1d7a4c', fillOpacity: 0.08 }).addTo(carte);
            }

            btnRetirer.hidden = false;
            chercherAdresse(p);
        }

        function retirer() {
            if (marqueur) { carte.removeLayer(marqueur); marqueur = null; }
            if (cercle) { carte.removeLayer(cercle); cercle = null; }
            champLat.value = '';
            champLng.value = '';
            btnRetirer.hidden = true;
            requeteAdresse++;
            afficherStatut('Cliquez sur la carte pour indiquer l’emplacement.');
        }

        if (aUnPoint) {
            marqueur = L.marker([lat, lng], { icon: icone(), draggable: true }).addTo(carte);
            marqueur.on('dragend', function () { placer(marqueur.getLatLng()); });
            btnRetirer.hidden = false;
            afficherStatut('Emplacement enregistré. Faites glisser le repère pour l’ajuster.');
        }

        carte.on('click', function (e) { placer(e.latlng); });
        btnRetirer.addEventListener('click', retirer);

        btnPosition.addEventListener('click', function () {
            if (!navigator.geolocation) {
                afficherStatut('La géolocalisation n’est pas disponible sur cet appareil.', true);
                return;
            }
            btnPosition.disabled = true;
            afficherStatut('Localisation en cours…');
            navigator.geolocation.getCurrentPosition(function (pos) {
                btnPosition.disabled = false;
                var p = L.latLng(pos.coords.latitude, pos.coords.longitude);
                carte.setView(p, 17);
                placer(p, pos.coords.accuracy);
            }, function (err) {
                btnPosition.disabled = false;
                afficherStatut(err.code === 1
                    ? 'Accès à la position refusé. Placez le repère sur la carte.'
                    : 'Position introuvable. Placez le repère sur la carte.', true);
            }, { enableHighAccuracy: true, timeout: 15000, maximumAge: 0 });
        });

        function rechercher() {
            var q = champRecherche.value.trim();
            if (!q) return;
            btnRecherche.disabled = true;
            afficherStatut('Recherche de « ' + q + ' »…');
            fetch(NOMINATIM + '/search?format=jsonv2&limit=1&countrycodes=tg&accept-language=fr&q=' + encodeURIComponent(q))
                .then(function (r) { return r.ok ? r.json() : Promise.reject(); })
                .then(function (res) {
                    if (!res.length) {
                        afficherStatut('Aucun lieu trouvé pour « ' + q + ' ».', true);
                        return;
                    }
                    var p = L.latLng(parseFloat(res[0].lat), parseFloat(res[0].lon));
                    carte.setView(p, 17);
                    placer(p);
                })
                .catch(function () { afficherStatut('Recherche indisponible. Placez le repère sur la carte.', true); })
                .finally(function () { btnRecherche.disabled = false; });
        }

        btnRecherche.addEventListener('click', rechercher);
        champRecherche.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') { e.preventDefault(); rechercher(); }
        });

        if (form && racine.dataset.requis !== undefined) {
            form.addEventListener('submit', function (e) {
                if (champLat.value && champLng.value) return;
                e.preventDefault();
                racine.classList.add('is-invalid');
                afficherStatut('Indiquez l’emplacement sur la carte avant d’envoyer.', true);
                racine.scrollIntoView({ behavior: 'smooth', block: 'center' });
            });
        }
    }

    function initApercu(racine) {
        var p = [parseFloat(racine.dataset.lat), parseFloat(racine.dataset.lng)];
        var elCarte = racine.querySelector('.cp-carte');
        var carte = L.map(elCarte, { scrollWheelZoom: false, zoomControl: true }).setView(p, 16);
        fondDeCarte(carte);
        surveillerTaille(carte, elCarte);
        var marqueur = L.marker(p, { icon: icone(), keyboard: false }).addTo(carte);
        if (racine.dataset.libelle) marqueur.bindTooltip(racine.dataset.libelle);
    }

    /*
     * [data-carte-points] : plusieurs points (JSON dans data-points), numérotés
     * et reliés dans l'ordre si data-relier est présent. Chaque point :
     * { lat, lng, nom, url?, etat? (« fait », « rate », « afaire », « inactif ») }.
     * La liste peut être remplacée à chaud : el.dispatchEvent(new CustomEvent('cp:points', { detail: [...] }))
     */
    function iconeNumero(numero, etat) {
        return L.divIcon({
            className: 'cp-pin-num cp-pin-' + (etat || 'afaire'),
            html: '<span>' + (numero || '') + '</span>',
            iconSize: [28, 28],
            iconAnchor: [14, 14]
        });
    }

    function echapper(texte) {
        var div = document.createElement('div');
        div.textContent = texte == null ? '' : String(texte);
        return div.innerHTML;
    }

    function initPoints(racine) {
        var elCarte = racine.querySelector('.cp-carte');
        var relier = racine.dataset.relier !== undefined;
        var numeroter = racine.dataset.numeroter !== undefined;
        var carte = L.map(elCarte, { scrollWheelZoom: false }).setView(LOME, 12);
        fondDeCarte(carte);
        surveillerTaille(carte, elCarte);
        carte.once('focus', function () { carte.scrollWheelZoom.enable(); });

        var calque = L.layerGroup().addTo(carte);

        function afficher(points) {
            calque.clearLayers();
            var coords = [];
            points.forEach(function (p, i) {
                var ll = [p.lat, p.lng];
                coords.push(ll);
                var m = L.marker(ll, { icon: iconeNumero(numeroter ? i + 1 : '', p.etat) }).addTo(calque);
                var html = '<strong>' + echapper(p.nom) + '</strong>';
                if (p.detail) html += '<br><span class="text-body-secondary">' + echapper(p.detail) + '</span>';
                if (p.url) html += '<br><a href="' + encodeURI(p.url) + '">Ouvrir</a>';
                m.bindPopup(html);
            });
            if (relier && coords.length > 1) {
                L.polyline(coords, { color: '#1d7a4c', weight: 4, opacity: 0.7, dashArray: '6 6' }).addTo(calque);
            }
            if (coords.length === 1) carte.setView(coords[0], 16);
            else if (coords.length > 1) carte.fitBounds(coords, { padding: [30, 30] });
        }

        var depart = [];
        try { depart = JSON.parse(racine.dataset.points || '[]'); } catch (e) { depart = []; }
        afficher(depart);
        racine.addEventListener('cp:points', function (e) { afficher(e.detail || []); });
    }

    function init() {
        if (!window.L) return;
        document.querySelectorAll('[data-carte-choix]').forEach(initChoix);
        document.querySelectorAll('[data-carte-apercu]').forEach(initApercu);
        document.querySelectorAll('[data-carte-points]').forEach(initPoints);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();

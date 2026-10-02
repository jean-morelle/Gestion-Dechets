/*
 * CollectPlus Togo — réduction des photos avant envoi
 *
 * Une photo de téléphone pèse souvent 3 à 8 Mo. Avant l'envoi, chaque image
 * choisie dans un champ <input type="file" accept="image/…"> est redimensionnée
 * (1600 px de côté au plus) et réenregistrée en JPEG : quelques centaines de Ko,
 * envoi rapide même en 3G, et plus d'erreur « fichier trop lourd ».
 * Si le navigateur ne sait pas lire l'image, le fichier d'origine est envoyé tel quel.
 */
(function () {
    'use strict';

    var COTE_MAX = 1600;
    var QUALITE = 0.82;
    var SEUIL_OCTETS = 1024 * 1024; // en dessous d'1 Mo, une image de taille raisonnable est gardée telle quelle

    if (!window.DataTransfer || !window.HTMLCanvasElement) return;

    function chargerImage(fichier) {
        // createImageBitmap applique l'orientation EXIF (photos prises en portrait)
        if (window.createImageBitmap) {
            return createImageBitmap(fichier, { imageOrientation: 'from-image' });
        }
        return new Promise(function (resolve, reject) {
            var img = new Image();
            img.onload = function () { resolve(img); };
            img.onerror = reject;
            img.src = URL.createObjectURL(fichier);
        });
    }

    function reduire(fichier) {
        if (!/^image\/(jpeg|png|webp|heic|heif)$/i.test(fichier.type) && !/\.(heic|heif)$/i.test(fichier.name)) {
            return Promise.resolve(fichier); // GIF animé, SVG… : on ne touche pas
        }

        return chargerImage(fichier).then(function (img) {
            var w = img.width, h = img.height;
            var ratio = Math.min(1, COTE_MAX / Math.max(w, h));
            var dejaLegere = ratio === 1 && fichier.size <= SEUIL_OCTETS && /jpe?g|png|webp/i.test(fichier.type);
            if (dejaLegere) return fichier;

            var canvas = document.createElement('canvas');
            canvas.width = Math.round(w * ratio);
            canvas.height = Math.round(h * ratio);
            var ctx = canvas.getContext('2d');
            ctx.fillStyle = '#fff'; // fond blanc pour les PNG transparents
            ctx.fillRect(0, 0, canvas.width, canvas.height);
            ctx.drawImage(img, 0, 0, canvas.width, canvas.height);

            return new Promise(function (resolve) {
                canvas.toBlob(function (blob) {
                    if (!blob || blob.size >= fichier.size) return resolve(fichier);
                    var nom = fichier.name.replace(/\.[^.]+$/, '') + '.jpg';
                    resolve(new File([blob], nom, { type: 'image/jpeg', lastModified: Date.now() }));
                }, 'image/jpeg', QUALITE);
            });
        }).catch(function () { return fichier; });
    }

    function enMo(octets) {
        return (octets / 1024 / 1024).toFixed(1).replace('.', ',') + ' Mo';
    }

    var enCours = new WeakMap(); // formulaire -> promesses de réduction en attente

    document.addEventListener('change', function (e) {
        var input = e.target;
        if (!(input instanceof HTMLInputElement) || input.type !== 'file') return;
        if (!/^image\//.test(input.accept || '') && input.accept !== 'image/*') return;
        if (!input.files || !input.files.length) return;

        var origine = Array.prototype.slice.call(input.files);
        var travail = Promise.all(origine.map(reduire)).then(function (fichiers) {
            var dt = new DataTransfer();
            fichiers.forEach(function (f) { dt.items.add(f); });
            input.files = dt.files;

            var avant = origine.reduce(function (s, f) { return s + f.size; }, 0);
            var apres = fichiers.reduce(function (s, f) { return s + f.size; }, 0);
            if (apres < avant) {
                input.title = 'Photo optimisée : ' + enMo(avant) + ' → ' + enMo(apres);
            }
        });

        if (input.form) {
            var liste = enCours.get(input.form) || [];
            liste.push(travail);
            enCours.set(input.form, liste);
            travail.then(function () {
                var reste = (enCours.get(input.form) || []).filter(function (p) { return p !== travail; });
                enCours.set(input.form, reste);
            });
        }
    });

    // Si l'utilisateur valide pendant la réduction, on attend qu'elle se termine
    document.addEventListener('submit', function (e) {
        var form = e.target;
        var liste = enCours.get(form);
        if (!liste || !liste.length) return;
        e.preventDefault();
        Promise.all(liste).then(function () {
            enCours.set(form, []);
            if (form.requestSubmit) form.requestSubmit(e.submitter || undefined);
            else form.submit();
        });
    }, true);
})();

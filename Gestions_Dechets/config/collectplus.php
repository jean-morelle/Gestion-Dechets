<?php

/*
|--------------------------------------------------------------------------
| Informations de l'organisme exploitant
|--------------------------------------------------------------------------
|
| Affichées dans la politique de confidentialité et les conditions
| d'utilisation. À renseigner dans le fichier .env avant la mise en service.
|
*/

return [
    'exploitant' => env('EXPLOITANT_NOM'),                 // ex. : Mairie de Lomé — Direction de l'assainissement
    'adresse' => env('EXPLOITANT_ADRESSE'),
    'email_contact' => env('EXPLOITANT_EMAIL'),            // adresse pour exercer ses droits sur ses données
    'telephone' => env('EXPLOITANT_TELEPHONE'),
    'hebergeur' => env('HEBERGEUR'),                       // nom et pays de l'hébergeur des serveurs
    'declaration_ipdcp' => env('DECLARATION_IPDCP'),       // numéro de déclaration auprès de l'IPDCP
    'date_conditions' => env('CONDITIONS_DATE', '2026-10-02'),
];

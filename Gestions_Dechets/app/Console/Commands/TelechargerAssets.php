<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;

class TelechargerAssets extends Command
{
    protected $signature = 'assets:telecharger';

    protected $description = 'Télécharge Bootstrap, Font Awesome et Chart.js dans public/vendor pour fonctionner hors ligne';

    public function handle(): int
    {
        $fichiers = [];
        foreach (config('assets') as $asset) {
            $fichiers[$asset['local']] = $asset['cdn'];
            $fichiers += $asset['extras'] ?? [];
        }

        $erreurs = 0;
        foreach ($fichiers as $local => $url) {
            try {
                $reponse = Http::timeout(30)->get($url);
            } catch (ConnectionException) {
                $this->error('Connexion impossible : vérifiez votre accès Internet puis relancez la commande.');

                return self::FAILURE;
            }

            if (! $reponse->successful()) {
                $this->error("Échec : {$url}");
                $erreurs++;
                continue;
            }

            File::ensureDirectoryExists(dirname(public_path($local)));
            File::put(public_path($local), $reponse->body());
            $this->line("<info>OK</info> {$local}");
        }

        return $erreurs === 0 ? self::SUCCESS : self::FAILURE;
    }
}

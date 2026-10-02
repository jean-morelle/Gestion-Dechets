<?php

namespace App\Console\Commands;

use App\Models\CalendrierCollecte;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

/**
 * La veille d'un passage, prévient les habitants du quartier concerné
 * (notification dans l'application). Lancée chaque soir par le planificateur.
 */
class RappelerCollectesCommand extends Command
{
    protected $signature = 'collectes:rappeler {--date= : Jour de collecte à annoncer (AAAA-MM-JJ), demain par défaut}';

    protected $description = 'Prévient les habitants des collectes du lendemain dans leur quartier';

    public function handle(): int
    {
        $jour = $this->option('date') ? Carbon::parse($this->option('date')) : today()->addDay();

        $calendriers = CalendrierCollecte::where('statut', CalendrierCollecte::STATUT_ACTIF)->get()
            ->filter(fn ($c) => $c->aLieuLe($jour));

        $envoyes = 0;
        foreach ($calendriers as $calendrier) {
            $habitants = User::where('role', 'citoyen')
                ->where('statut', 'actif')
                ->whereRaw('LOWER(quartier) = ?', [mb_strtolower($calendrier->quartier)])
                ->get()
                ->filter(fn ($u) => ($u->notification_preferences['rappel_collecte'] ?? true) !== false);

            $cle = "rappel-{$calendrier->id}-{$jour->toDateString()}";
            $quand = $jour->isTomorrow() ? 'Demain' : ucfirst($jour->translatedFormat('l j F'));
            $horaire = $calendrier->heure_debut?->format('H\hi') . ' – ' . $calendrier->heure_fin?->format('H\hi');

            foreach ($habitants as $habitant) {
                // Une seule notification par habitant et par passage, même si la commande est relancée
                if (Notification::where('user_id', $habitant->id)->where('data->cle', $cle)->exists()) {
                    continue;
                }

                Notification::create([
                    'user_id' => $habitant->id,
                    'type' => Notification::TYPE_CALENDRIER,
                    'titre' => "{$quand} : collecte {$calendrier->type_collecte_label_court} à {$calendrier->quartier}",
                    'message' => "Passage prévu {$horaire}. Sortez vos déchets avant l’heure de passage, dans des sacs fermés.",
                    'data' => ['cle' => $cle],
                    'statut' => Notification::STATUT_NON_LU,
                    'priorite' => 'moyenne',
                    'date_envoi' => now(),
                    'lien_action' => route('citoyen.calendrier.index', [], false),
                ]);
                $envoyes++;
            }
        }

        $this->info("{$calendriers->count()} passage(s) le {$jour->toDateString()}, {$envoyes} habitant(s) prévenu(s).");

        return self::SUCCESS;
    }
}

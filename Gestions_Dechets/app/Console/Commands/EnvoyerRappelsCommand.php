<?php

namespace App\Console\Commands;

use App\Models\Rappel;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class EnvoyerRappelsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rappels:envoyer';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envoie les rappels automatiques avant le passage du collecteur';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Début de l\'envoi des rappels automatiques...');

        $rappelsEnvoyes = 0;
        $rappelsErreur = 0;

        // Récupérer tous les rappels actifs qui doivent être envoyés
        $rappels = Rappel::with(['user', 'calendrier'])
            ->actif()
            ->get()
            ->filter(function ($rappel) {
                return $rappel->doitEtreEnvoye();
            });

        foreach ($rappels as $rappel) {
            try {
                $this->envoyerRappel($rappel);
                $rappel->marquerCommeEnvoye();
                $rappelsEnvoyes++;
                
                $this->info("Rappel envoyé à {$rappel->user->name} pour la collecte du {$rappel->calendrier->quartier}");
            } catch (\Exception $e) {
                $rappelsErreur++;
                Log::error("Erreur lors de l'envoi du rappel {$rappel->id}: " . $e->getMessage());
                $this->error("Erreur pour le rappel {$rappel->id}: " . $e->getMessage());
            }
        }

        $this->info("Envoi terminé: {$rappelsEnvoyes} rappels envoyés, {$rappelsErreur} erreurs");
        
        return Command::SUCCESS;
    }

    /**
     * Envoyer un rappel spécifique
     */
    private function envoyerRappel(Rappel $rappel)
    {
        $user = $rappel->user;
        $calendrier = $rappel->calendrier;
        $prochaineCollecte = $calendrier->getProchaineCollecte();

        if (!$prochaineCollecte) {
            throw new \Exception("Aucune prochaine collecte trouvée pour le calendrier {$calendrier->id}");
        }

        $donnees = [
            'user' => $user,
            'calendrier' => $calendrier,
            'prochaineCollecte' => $prochaineCollecte,
            'typeRappel' => $rappel->type_rappel,
            'delaiHeures' => $rappel->delai_heures
        ];

        switch ($rappel->type_rappel) {
            case 'email':
                $this->envoyerEmailRappel($donnees);
                break;
            case 'sms':
                $this->envoyerSmsRappel($donnees);
                break;
            case 'push':
                $this->envoyerPushRappel($donnees);
                break;
            default:
                throw new \Exception("Type de rappel non supporté: {$rappel->type_rappel}");
        }
    }

    /**
     * Envoyer un rappel par email
     */
    private function envoyerEmailRappel(array $donnees)
    {
        // Pour l'instant, on log juste l'email
        // Dans une vraie application, on utiliserait Mail::send()
        Log::info("RAPPEL EMAIL envoyé à {$donnees['user']->email}", $donnees);
        
        // TODO: Implémenter l'envoi d'email réel
        // Mail::to($donnees['user']->email)->send(new RappelEmail($donnees));
    }

    /**
     * Envoyer un rappel par SMS
     */
    private function envoyerSmsRappel(array $donnees)
    {
        // Pour l'instant, on log juste le SMS
        Log::info("RAPPEL SMS envoyé au {$donnees['user']->telephone}", $donnees);
        
        // TODO: Implémenter l'envoi de SMS réel
        // SmsService::send($donnees['user']->telephone, $message);
    }

    /**
     * Envoyer un rappel push
     */
    private function envoyerPushRappel(array $donnees)
    {
        // Pour l'instant, on log juste la notification push
        Log::info("RAPPEL PUSH envoyé à {$donnees['user']->name}", $donnees);
        
        // TODO: Implémenter l'envoi de notification push réel
        // PushNotificationService::send($donnees['user'], $message);
    }
}
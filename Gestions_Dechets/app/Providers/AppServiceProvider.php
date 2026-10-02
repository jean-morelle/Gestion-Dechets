<?php

namespace App\Providers;

use Carbon\Carbon;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use App\Models\DemandeCollecte;
use App\Models\Incident;
use App\Models\Message;
use App\Models\Signalement;
use App\Services\NotificationService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(NotificationService::class, function ($app) {
            return new NotificationService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Les vues utilisent Bootstrap 5 : la pagination doit suivre le même style
        Paginator::useBootstrapFive();

        Carbon::setLocale('fr');

        // E-mail de réinitialisation du mot de passe, en français
        ResetPassword::toMailUsing(function ($user, string $token) {
            $lien = route('password.reset', ['token' => $token, 'email' => $user->getEmailForPasswordReset()]);
            $minutes = config('auth.passwords.users.expire');

            return (new MailMessage)
                ->subject('Réinitialisation de votre mot de passe — ' . config('app.name'))
                ->greeting('Bonjour ' . $user->name . ',')
                ->line('Vous avez demandé à changer le mot de passe de votre compte ' . config('app.name') . '.')
                ->action('Choisir un nouveau mot de passe', $lien)
                ->line("Ce lien est valable {$minutes} minutes et ne peut servir qu’une fois.")
                ->line('Si vous n’êtes pas à l’origine de cette demande, ignorez cet e-mail : votre mot de passe reste inchangé.')
                ->salutation('L’équipe ' . config('app.name'));
        });

        // Fichier local s'il a été téléchargé, CDN sinon (voir config/assets.php)
        View::share('vendorAsset', function (string $cle) {
            $asset = config("assets.$cle");

            return file_exists(public_path($asset['local'])) ? asset($asset['local']) : $asset['cdn'];
        });

        // Compteurs affichés dans le menu et la barre supérieure
        View::composer('layouts.app', function ($view) {
            $user = auth()->user();
            if (! $user) {
                return;
            }

            $view->with([
                'notificationsNonLues' => app(NotificationService::class)->compterNotificationsNonLues($user->id),
                'messagesNonLus' => Message::where('receiver_id', $user->id)->where('is_read', false)->count(),
                'signalementsEnAttente' => $user->role === 'admin'
                    ? Signalement::where('statut', Signalement::STATUT_EN_ATTENTE)->count()
                    : 0,
                'demandesEnAttente' => $user->role === 'admin'
                    ? DemandeCollecte::where('statut', DemandeCollecte::STATUT_EN_ATTENTE)->count()
                    : 0,
                'incidentsOuverts' => $user->role === 'admin'
                    ? Incident::where('statut', 'signale')->count()
                    : 0,
            ]);
        });
    }
}

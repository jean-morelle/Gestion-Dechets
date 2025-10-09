<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    /**
     * Afficher la liste des messages
     */
    public function index()
    {
        $user = Auth::user();
        
        // Messages reçus
        $messagesReceived = Message::with(['sender'])
            ->where('receiver_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Messages envoyés
        $messagesSent = Message::with(['receiver'])
            ->where('sender_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Compter les messages non lus
        $unreadCount = Message::where('receiver_id', $user->id)
            ->where('is_read', false)
            ->count();

        return view('messages.index', compact('messagesReceived', 'messagesSent', 'unreadCount'));
    }

    /**
     * Afficher un message spécifique
     */
    public function show(Message $message)
    {
        // Vérifier que l'utilisateur peut voir ce message
        if ($message->sender_id !== Auth::id() && $message->receiver_id !== Auth::id()) {
            abort(403);
        }

        // Marquer comme lu si c'est le destinataire
        if ($message->receiver_id === Auth::id() && !$message->is_read) {
            $message->markAsRead();
        }

        return view('messages.show', compact('message'));
    }

    /**
     * Afficher le formulaire de création
     */
    public function create()
    {
        // Récupérer tous les utilisateurs pour le destinataire, groupés par rôle
        $users = User::where('id', '!=', Auth::id()) // Exclure l'utilisateur connecté
                    ->orderBy('role')
                    ->orderBy('name')
                    ->get()
                    ->groupBy('role');
        
        return view('messages.create', compact('users'));
    }

    /**
     * Enregistrer un nouveau message
     */
    public function store(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'subject' => 'nullable|string|max:255',
            'message' => 'required|string|min:10',
        ]);

        Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $request->receiver_id,
            'subject' => $request->subject,
            'message' => $request->message,
        ]);

        return redirect()->route('messages.index')
            ->with('success', 'Message envoyé avec succès !');
    }

    /**
     * Marquer un message comme lu
     */
    public function markAsRead(Message $message)
    {
        if ($message->receiver_id === Auth::id()) {
            $message->markAsRead();
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false], 403);
    }

    /**
     * Supprimer un message
     */
    public function destroy(Message $message)
    {
        // Vérifier que l'utilisateur peut supprimer ce message
        if ($message->sender_id !== Auth::id() && $message->receiver_id !== Auth::id()) {
            abort(403);
        }

        $message->delete();

        return redirect()->route('messages.index')
            ->with('success', 'Message supprimé avec succès !');
    }

    /**
     * Afficher le formulaire de réponse
     */
    public function reply(Message $message)
    {
        // Vérifier que l'utilisateur peut répondre à ce message
        if ($message->receiver_id !== Auth::id()) {
            abort(403);
        }

        return view('messages.reply', compact('message'));
    }

    /**
     * Enregistrer une réponse
     */
    public function replyStore(Request $request, Message $originalMessage)
    {
        // Vérifier que l'utilisateur peut répondre à ce message
        if ($originalMessage->receiver_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'subject' => 'nullable|string|max:255',
            'message' => 'required|string|min:10',
        ]);

        // Créer la réponse
        Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $originalMessage->sender_id, // Répondre à l'expéditeur original
            'subject' => $request->subject ?: 'Re: ' . $originalMessage->subject,
            'message' => $request->message,
        ]);

        return redirect()->route('messages.index')
            ->with('success', 'Réponse envoyée avec succès !');
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'sender_id',
        'receiver_id',
        'subject',
        'message',
        'is_read',
        'read_at'
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    // Relation avec l'expéditeur
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    // Relation avec le destinataire
    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    // Scope pour les messages non lus
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    // Scope pour les messages d'un utilisateur
    public function scopeForUser($query, $userId)
    {
        return $query->where('receiver_id', $userId);
    }

    // Scope pour les messages envoyés par un utilisateur
    public function scopeSentBy($query, $userId)
    {
        return $query->where('sender_id', $userId);
    }

    // Marquer comme lu
    public function markAsRead()
    {
        $this->update([
            'is_read' => true,
            'read_at' => now()
        ]);
    }
}
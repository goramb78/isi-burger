<?php
// app/Models/Commande.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Commande extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'statut', 'total', 'notes'];

    // ─── Statuts ──────────────────────────────────────────────
    const STATUT_EN_ATTENTE    = 'en_attente';
    const STATUT_EN_PREPARATION = 'en_preparation';
    const STATUT_PRETE         = 'prete';
    const STATUT_PAYEE         = 'payee';
    const STATUT_ANNULEE       = 'annulee';

    public static function statuts(): array
    {
        return [
            self::STATUT_EN_ATTENTE     => ['label' => 'En attente',    'badge' => 'warning'],
            self::STATUT_EN_PREPARATION => ['label' => 'En préparation','badge' => 'info'],
            self::STATUT_PRETE          => ['label' => 'Prête',          'badge' => 'primary'],
            self::STATUT_PAYEE          => ['label' => 'Payée',          'badge' => 'success'],
            self::STATUT_ANNULEE        => ['label' => 'Annulée',        'badge' => 'danger'],
        ];
    }

    public function getStatutLabelAttribute(): string
    {
        return self::statuts()[$this->statut]['label'] ?? $this->statut;
    }

    public function getStatutBadgeAttribute(): string
    {
        return self::statuts()[$this->statut]['badge'] ?? 'secondary';
    }

    public function peutEtreAnnulee(): bool
    {
        return in_array($this->statut, [self::STATUT_EN_ATTENTE, self::STATUT_EN_PREPARATION]);
    }

    // ─── Relations ────────────────────────────────────────────
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(CommandeItem::class);
    }

    public function paiement()
    {
        return $this->hasOne(Paiement::class);
    }
}

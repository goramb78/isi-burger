<?php
// app/Models/Paiement.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Paiement extends Model
{
    use HasFactory;

    protected $fillable = ['commande_id', 'montant', 'date_paiement', 'mode'];

    protected $casts = [
        'date_paiement' => 'datetime',
        'montant'       => 'decimal:2',
    ];

    // ─── Relations ────────────────────────────────────────────
    public function commande()
    {
        return $this->belongsTo(Commande::class);
    }
}

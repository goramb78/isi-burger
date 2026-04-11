<?php
// app/Models/CommandeItem.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CommandeItem extends Model
{
    use HasFactory;

    protected $fillable = ['commande_id', 'burger_id', 'quantite', 'prix_unitaire'];

    protected $casts = [
        'prix_unitaire' => 'decimal:2',
    ];

    //  Helpers
    public function getSousTotalAttribute(): float
    {
        return $this->quantite * $this->prix_unitaire;
    }

    //  Relations 
    public function commande()
    {
        return $this->belongsTo(Commande::class);
    }

    public function burger()
    {
        return $this->belongsTo(Burger::class);
    }
}

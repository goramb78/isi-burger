<?php
// app/Models/Burger.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Burger extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id', 'nom', 'prix', 'image',
        'description', 'stock', 'archived',
    ];

    protected $casts = [
        'archived' => 'boolean',
        'prix'     => 'decimal:2',
    ];

    //  Scopes 
    public function scopeActif($query)
    {
        return $query->where('archived', false)->where('stock', '>', 0);
    }

    public function scopeDisponible($query)
    {
        return $query->where('archived', false);
    }

    //  Helpers
    public function isEnRupture(): bool
    {
        return $this->stock <= 0;
    }

    public function getImageUrlAttribute(): string
    {
        return $this->image
            ? asset('storage/' . $this->image)
            : asset('images/default-burger.png');
    }

    //  Relations 
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function commandeItems()
    {
        return $this->hasMany(CommandeItem::class);
    }
}

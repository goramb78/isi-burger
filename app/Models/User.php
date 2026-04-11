<?php
// app/Models/User.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = ['name', 'email', 'password', 'role'];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
    ];

    //  Helpers 
    public function isGestionnaire(): bool
    {
        return $this->role === 'gestionnaire';
    }

    public function isClient(): bool
    {
        return $this->role === 'client';
    }

    //  Relations 
    public function commandes()
    {
        return $this->hasMany(Commande::class);
    }
}

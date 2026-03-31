<?php
// database/seeders/DatabaseSeeder.php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Burger;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Gestionnaire
        User::create([
            'name'     => 'Admin ISI Burger',
            'email'    => 'admin@isiburger.com',
            'password' => Hash::make('password'),
            'role'     => 'gestionnaire',
        ]);

        // Client test
        User::create([
            'name'     => 'Client Test',
            'email'    => 'client@isiburger.com',
            'password' => Hash::make('password'),
            'role'     => 'client',
        ]);

        // Catégories
        $categories = [
            ['nom' => 'Classiques'],
            ['nom' => 'Spéciaux'],
            ['nom' => 'Végétariens'],
            ['nom' => 'Chicken'],
        ];

        foreach ($categories as $cat) {
            Category::create($cat);
        }

        // Burgers
        $burgers = [
            ['category_id' => 1, 'nom' => 'ISI Classic',      'prix' => 3500, 'stock' => 50, 'description' => 'Le burger classique avec steak haché, salade, tomate, oignon.'],
            ['category_id' => 1, 'nom' => 'Double Stack',      'prix' => 5000, 'stock' => 30, 'description' => 'Double steak haché, fromage fondu, sauce spéciale.'],
            ['category_id' => 2, 'nom' => 'ISI Spécial',       'prix' => 5500, 'stock' => 20, 'description' => 'Burger signature avec bacon, oeuf et sauce maison.'],
            ['category_id' => 3, 'nom' => 'Veggie Burger',     'prix' => 3000, 'stock' => 25, 'description' => 'Steak de légumes, avocat, sauce yaourt.'],
            ['category_id' => 4, 'nom' => 'Crispy Chicken',    'prix' => 4000, 'stock' => 40, 'description' => 'Poulet croustillant, coleslaw maison, sauce piquante.'],
            ['category_id' => 4, 'nom' => 'Chicken Cheese',    'prix' => 4500, 'stock' => 35, 'description' => 'Filet de poulet grillé, cheddar fondu, sauce ranch.'],
        ];

        foreach ($burgers as $b) {
            Burger::create($b);
        }
    }
}

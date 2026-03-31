<?php
// database/migrations/xxxx_create_paiements_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('paiements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('commande_id')->unique()->constrained()->onDelete('cascade');
            $table->decimal('montant', 10, 2);
            $table->timestamp('date_paiement');
            $table->enum('mode', ['especes'])->default('especes');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('paiements');
    }
};

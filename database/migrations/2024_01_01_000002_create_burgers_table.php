<?php
// database/migrations/xxxx_create_burgers_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('burgers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->string('nom');
            $table->decimal('prix', 10, 2);
            $table->string('image')->nullable();
            $table->text('description')->nullable();
            $table->integer('stock')->default(0);
            $table->boolean('archived')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('burgers');
    }
};

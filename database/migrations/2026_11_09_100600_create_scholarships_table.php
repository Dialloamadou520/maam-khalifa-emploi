<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('scholarships', function (Blueprint $table) {
            $table->id();
            $table->string('titre');
            $table->string('slug')->unique();
            $table->string('organisme');
            $table->string('type', 20)->index();
            $table->string('pays', 100)->nullable();
            $table->string('niveau', 100)->nullable();
            $table->string('domaine', 150)->nullable();
            $table->string('montant', 150)->nullable();
            $table->text('description');
            $table->text('conditions')->nullable();
            $table->string('lien_externe')->nullable();
            $table->date('date_limite')->nullable();
            $table->boolean('est_publiee')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scholarships');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('candidate_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('titre_profil')->nullable();
            $table->date('date_naissance')->nullable();
            $table->string('genre', 10)->nullable();
            $table->string('adresse')->nullable();
            $table->string('ville', 100)->nullable();
            $table->string('pays', 100)->default('Sénégal');
            $table->string('niveau_etude', 100)->nullable();
            $table->string('domaine', 150)->nullable();
            $table->unsignedTinyInteger('annees_experience')->nullable();
            $table->text('competences')->nullable();
            $table->string('langues')->nullable();
            $table->string('disponibilite', 100)->nullable();
            $table->unsignedInteger('pretention_salariale')->nullable();
            $table->text('presentation')->nullable();
            $table->string('linkedin')->nullable();
            $table->boolean('visible_recruteurs')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('candidate_profiles');
    }
};

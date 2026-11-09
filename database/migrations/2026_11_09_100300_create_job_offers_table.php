<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_offers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('titre');
            $table->string('slug')->unique();
            $table->string('type_contrat', 30);
            $table->string('lieu', 150);
            $table->string('secteur', 150)->nullable();
            $table->string('niveau_etude', 100)->nullable();
            $table->unsignedTinyInteger('experience_min')->nullable();
            $table->unsignedInteger('salaire_min')->nullable();
            $table->unsignedInteger('salaire_max')->nullable();
            $table->unsignedSmallInteger('nombre_postes')->default(1);
            $table->text('description');
            $table->text('profil_recherche')->nullable();
            $table->text('avantages')->nullable();
            $table->date('date_limite')->nullable();
            $table->string('statut', 20)->default('en_attente')->index();
            $table->text('motif_rejet')->nullable();
            $table->timestamp('publiee_le')->nullable();
            $table->foreignId('validee_par')->nullable()->constrained('users')->nullOnDelete();
            $table->unsignedInteger('vues')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_offers');
    }
};

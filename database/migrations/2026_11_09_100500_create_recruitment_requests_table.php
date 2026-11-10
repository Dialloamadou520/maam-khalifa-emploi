<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recruitment_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('intitule_poste');
            $table->unsignedSmallInteger('nombre_postes')->default(1);
            $table->string('type_contrat', 30);
            $table->string('lieu', 150)->nullable();
            $table->text('description');
            $table->text('competences_requises')->nullable();
            $table->unsignedInteger('budget')->nullable();
            $table->date('date_souhaitee')->nullable();
            $table->string('statut', 20)->default('soumise')->index();
            $table->text('note_admin')->nullable();
            $table->foreignId('traitee_par')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recruitment_requests');
    }
};

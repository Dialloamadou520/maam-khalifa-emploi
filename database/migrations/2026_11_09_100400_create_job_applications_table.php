<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_offer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('cv_document_id')->nullable()->constrained('documents')->nullOnDelete();
            $table->foreignId('lettre_document_id')->nullable()->constrained('documents')->nullOnDelete();
            $table->text('message')->nullable();
            $table->string('statut', 20)->default('envoyee')->index();
            $table->text('note_recruteur')->nullable();
            $table->timestamps();

            $table->unique(['job_offer_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_applications');
    }
};

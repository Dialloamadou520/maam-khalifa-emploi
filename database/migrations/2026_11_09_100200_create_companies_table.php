<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('nom');
            $table->string('secteur', 150)->nullable();
            $table->string('taille', 50)->nullable();
            $table->string('ninea', 50)->nullable();
            $table->string('adresse')->nullable();
            $table->string('ville', 100)->nullable();
            $table->string('pays', 100)->default('Sénégal');
            $table->string('telephone', 30)->nullable();
            $table->string('email_contact')->nullable();
            $table->string('site_web')->nullable();
            $table->text('description')->nullable();
            $table->boolean('est_verifiee')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};

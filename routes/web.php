<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Candidat\DashboardController as CandidatDashboardController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\JobOfferController;
use App\Http\Controllers\PublicPageController;
use App\Http\Controllers\Recruteur\DashboardController as RecruteurDashboardController;
use App\Http\Controllers\ScholarshipController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PublicPageController::class, 'accueil'])->name('accueil');
Route::get('/prestations', [PublicPageController::class, 'prestations'])->name('prestations');
Route::get('/agence', [PublicPageController::class, 'apropos'])->name('a-propos');

Route::get('/offres', [JobOfferController::class, 'index'])->name('offres.index');
Route::get('/offres/{offre}', [JobOfferController::class, 'show'])->name('offres.show');

Route::get('/bourses', [ScholarshipController::class, 'index'])->name('bourses.index');
Route::get('/bourses/{bourse}', [ScholarshipController::class, 'show'])->name('bourses.show');

Route::get('/dashboard', DashboardController::class)->middleware('auth')->name('dashboard');

Route::middleware(['auth', 'role:candidat'])->prefix('candidat')->name('candidat.')->group(function () {
    Route::get('/tableau-de-bord', CandidatDashboardController::class)->name('dashboard');
});

Route::middleware(['auth', 'role:recruteur'])->prefix('recruteur')->name('recruteur.')->group(function () {
    Route::get('/tableau-de-bord', RecruteurDashboardController::class)->name('dashboard');
});

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/tableau-de-bord', AdminDashboardController::class)->name('dashboard');
});

require __DIR__.'/auth.php';

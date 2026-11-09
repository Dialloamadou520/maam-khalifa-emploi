<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Candidat\CandidatureController as CandidatCandidatureController;
use App\Http\Controllers\Candidat\DashboardController as CandidatDashboardController;
use App\Http\Controllers\Candidat\DocumentController as CandidatDocumentController;
use App\Http\Controllers\Candidat\DossierController;
use App\Http\Controllers\Candidat\ProfilController as CandidatProfilController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\JobOfferController;
use App\Http\Controllers\PublicPageController;
use App\Http\Controllers\Recruteur\BesoinController;
use App\Http\Controllers\Recruteur\CandidatureController as RecruteurCandidatureController;
use App\Http\Controllers\Recruteur\DashboardController as RecruteurDashboardController;
use App\Http\Controllers\Recruteur\EntrepriseController;
use App\Http\Controllers\Recruteur\OffreController as RecruteurOffreController;
use App\Http\Controllers\Recruteur\ProfilController as RecruteurProfilController;
use App\Http\Controllers\Recruteur\RecrutementController;
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

    Route::get('/profil', [CandidatProfilController::class, 'edit'])->name('profil.edit');
    Route::put('/profil', [CandidatProfilController::class, 'update'])->name('profil.update');

    Route::get('/documents', [CandidatDocumentController::class, 'index'])->name('documents.index');
    Route::post('/documents', [CandidatDocumentController::class, 'store'])->name('documents.store');
    Route::get('/documents/{document}', [CandidatDocumentController::class, 'show'])->name('documents.show');
    Route::delete('/documents/{document}', [CandidatDocumentController::class, 'destroy'])->name('documents.destroy');

    Route::get('/candidatures', [CandidatCandidatureController::class, 'index'])->name('candidatures.index');
    Route::get('/offres/{offre}/postuler', [CandidatCandidatureController::class, 'create'])->name('candidatures.create');
    Route::post('/offres/{offre}/postuler', [CandidatCandidatureController::class, 'store'])->name('candidatures.store');
    Route::delete('/candidatures/{candidature}', [CandidatCandidatureController::class, 'destroy'])->name('candidatures.destroy');

    Route::get('/dossiers', [DossierController::class, 'index'])->name('dossiers.index');
    Route::get('/dossiers/nouveau', [DossierController::class, 'create'])->name('dossiers.create');
    Route::post('/dossiers', [DossierController::class, 'store'])->name('dossiers.store');
    Route::get('/dossiers/{dossier}', [DossierController::class, 'show'])->name('dossiers.show');
});

Route::middleware(['auth', 'role:recruteur'])->prefix('recruteur')->name('recruteur.')->group(function () {
    Route::get('/tableau-de-bord', RecruteurDashboardController::class)->name('dashboard');

    Route::get('/entreprise', [EntrepriseController::class, 'edit'])->name('entreprise.edit');
    Route::put('/entreprise', [EntrepriseController::class, 'update'])->name('entreprise.update');

    Route::get('/offres', [RecruteurOffreController::class, 'index'])->name('offres.index');
    Route::get('/offres/nouvelle', [RecruteurOffreController::class, 'create'])->name('offres.create');
    Route::post('/offres', [RecruteurOffreController::class, 'store'])->name('offres.store');
    Route::get('/offres/{offre}', [RecruteurOffreController::class, 'show'])->name('offres.show');
    Route::get('/offres/{offre}/modifier', [RecruteurOffreController::class, 'edit'])->name('offres.edit');
    Route::put('/offres/{offre}', [RecruteurOffreController::class, 'update'])->name('offres.update');
    Route::patch('/offres/{offre}/cloturer', [RecruteurOffreController::class, 'cloturer'])->name('offres.cloturer');

    Route::get('/candidatures', [RecruteurCandidatureController::class, 'index'])->name('candidatures.index');
    Route::get('/candidatures/{candidature}', [RecruteurCandidatureController::class, 'show'])->name('candidatures.show');
    Route::patch('/candidatures/{candidature}', [RecruteurCandidatureController::class, 'update'])->name('candidatures.update');
    Route::get('/candidatures/{candidature}/{type}', [RecruteurCandidatureController::class, 'document'])
        ->whereIn('type', ['cv', 'lettre'])->name('candidatures.document');

    Route::get('/besoins', [BesoinController::class, 'index'])->name('besoins.index');
    Route::get('/besoins/nouveau', [BesoinController::class, 'create'])->name('besoins.create');
    Route::post('/besoins', [BesoinController::class, 'store'])->name('besoins.store');
    Route::get('/besoins/{besoin}', [BesoinController::class, 'show'])->name('besoins.show');
    Route::patch('/besoins/{besoin}/annuler', [BesoinController::class, 'annuler'])->name('besoins.annuler');

    Route::get('/profils', [RecruteurProfilController::class, 'index'])->name('profils.index');
    Route::get('/profils/{profil}', [RecruteurProfilController::class, 'show'])->name('profils.show');
    Route::post('/profils/{profil}/mise-en-relation', [RecruteurProfilController::class, 'contacter'])->name('profils.contacter');

    Route::get('/recrutements', [RecrutementController::class, 'index'])->name('recrutements.index');
    Route::patch('/recrutements/{miseEnRelation}', [RecrutementController::class, 'update'])->name('recrutements.update');

    Route::get('/dossiers', [DossierController::class, 'index'])->name('dossiers.index');
    Route::get('/dossiers/nouveau', [DossierController::class, 'create'])->name('dossiers.create');
    Route::post('/dossiers', [DossierController::class, 'store'])->name('dossiers.store');
    Route::get('/dossiers/{dossier}', [DossierController::class, 'show'])->name('dossiers.show');
});

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/tableau-de-bord', AdminDashboardController::class)->name('dashboard');
});

require __DIR__.'/auth.php';

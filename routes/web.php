<?php

use App\Http\Controllers\Project\ProjectController;
use App\Http\Controllers\Skill\SkillController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::group(['prefix' => 'skills', 'namespace' => 'Skill'], function () {
        Route::get('/', [SkillController::class, 'index'])->name('skills.index');
        Route::get('/create', [SkillController::class, 'create']);
        Route::post('/store', [SkillController::class, 'store'])->name('skills.store');
        Route::get('/{skill}/edit', [SkillController::class, 'edit']);
        Route::put('/{skill}/update', [SkillController::class, 'update'])->name('skills.update');
        Route::delete('/{skill}/delete', [SkillController::class, 'delete'])->name('skills.delete');
    });

    Route::group(['prefix' => 'projects', 'namespace' => 'Project'], function () {
        Route::get('/', [ProjectController::class, 'index'])->name('projects.index');
        Route::get('/create', [ProjectController::class, 'create']);
        Route::post('/store', [ProjectController::class, 'store'])->name('projects.store');
        Route::get('/{project}/edit', [ProjectController::class, 'edit']);
        Route::put('/{project}/update', [ProjectController::class, 'update'])->name('projects.update');
        Route::delete('/{project}/delete', [ProjectController::class, 'delete'])->name('projects.delete');
    });
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';

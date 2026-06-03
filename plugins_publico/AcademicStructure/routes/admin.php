<?php

use Illuminate\Support\Facades\Route;
use Plugins\AcademicStructure\Controllers\AcademicStructureController;

Route::prefix('academicstructure')->group(function () {
    Route::get('/', [AcademicStructureController::class, 'index'])->name('academicstructure.index');

    Route::prefix('gestiones')->name('academicstructure.gestions.')->group(function () {
        Route::get('/', [AcademicStructureController::class, 'gestionsIndex'])->name('index');
        Route::post('/', [AcademicStructureController::class, 'gestionsStore'])->name('store');
        Route::put('/{id}', [AcademicStructureController::class, 'gestionsUpdate'])->name('update');
        Route::delete('/{id}', [AcademicStructureController::class, 'gestionsDestroy'])->name('destroy');
    });

    Route::prefix('semestres')->name('academicstructure.semesters.')->group(function () {
        Route::get('/', [AcademicStructureController::class, 'semestersIndex'])->name('index');
        Route::post('/', [AcademicStructureController::class, 'semestersStore'])->name('store');
        Route::put('/{id}', [AcademicStructureController::class, 'semestersUpdate'])->name('update');
        Route::delete('/{id}', [AcademicStructureController::class, 'semestersDestroy'])->name('destroy');
    });

    Route::prefix('grupos')->name('academicstructure.groups.')->group(function () {
        Route::get('/', [AcademicStructureController::class, 'groupsIndex'])->name('index');
        Route::post('/', [AcademicStructureController::class, 'groupsStore'])->name('store');
        Route::put('/{id}', [AcademicStructureController::class, 'groupsUpdate'])->name('update');
        Route::delete('/{id}', [AcademicStructureController::class, 'groupsDestroy'])->name('destroy');
    });
});

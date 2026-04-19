<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TokoElektronik;

Route::prefix('items')->group(function () {

    Route::get('/', [TokoElektronik::class, 'index']);        // GET semua item
    Route::get('/{id}', [TokoElektronik::class, 'show']);     // GET by ID

    Route::post('/', [TokoElektronik::class, 'store']);       // CREATE
    Route::put('/{id}', [TokoElektronik::class, 'update']);   // UPDATE semua
    Route::patch('/{id}', [TokoElektronik::class, 'patch']);  // UPDATE sebagian

    Route::delete('/{id}', [TokoElektronik::class, 'destroy']); // DELETE
});
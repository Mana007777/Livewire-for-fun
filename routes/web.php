<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::livewire('dashboard', 'admin.dashboard')->name('dashboard');
    Route::prefix('companies')->group(function () {
        Route::livewire('/', 'admin.companies.index')->name('companies.index');
        Route::livewire('/create', 'admin.companies.create')->name('companies.create');
        Route::livewire('/{company}/edit', 'admin.companies.edit')->name('companies.edit');
    });
})->name('dashboard');

require __DIR__.'/settings.php';

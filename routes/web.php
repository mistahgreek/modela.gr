<?php

use App\Http\Controllers\ModelController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QuoteController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('models.index');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Models routes
Route::get('/models', [ModelController::class, 'index'])->name('models.index');
Route::get('/models/{slug}', [ModelController::class, 'show'])->name('models.show');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/my-models', [ModelController::class, 'myModels'])->name('models.my');
    Route::get('/models-create', [ModelController::class, 'create'])->name('models.create');
    Route::post('/models-create', [ModelController::class, 'store'])->name('models.store');
    Route::get('/models-edit/{model}', [ModelController::class, 'edit'])->name('models.edit');
    Route::put('/models-edit/{model}', [ModelController::class, 'update'])->name('models.update');
    Route::delete('/models-delete/{model}', [ModelController::class, 'destroy'])->name('models.destroy');
});

// Quote routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/quotes', [QuoteController::class, 'index'])->name('quotes.index');
    Route::get('/quotes/create', [QuoteController::class, 'create'])->name('quotes.create');
    Route::post('/quotes', [QuoteController::class, 'store'])->name('quotes.store');
    Route::get('/quotes/{quote}', [QuoteController::class, 'show'])->name('quotes.show');
    Route::post('/api/quotes/calculate', [QuoteController::class, 'calculate'])->name('api.quotes.calculate');
});

require __DIR__.'/auth.php';

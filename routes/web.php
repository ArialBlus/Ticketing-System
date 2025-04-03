<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\TicketController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\StatusController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;


Route::get('/', function () {
    return view('welcome');
});


Route::get('/dashboard', function () {
    return view('dashboard');
    #Route::resource('tickets', TicketController::class);
    
})->middleware(['auth', 'verified'])->name('dashboard');



Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});



Route::middleware(['auth'])->group(function () {
    // Gestión de tickets
    Route::get('/tickets', [TicketController::class, 'index'])->name('tickets.index');
    Route::get('/tickets/create', [TicketController::class, 'create'])->name('tickets.create');
    Route::post('/tickets', [TicketController::class, 'store'])->name('tickets.store');
    Route::get('/tickets/{id}', [TicketController::class, 'show'])->name('tickets.show');
    Route::get('/tickets/{id}/edit', [TicketController::class, 'edit'])->name('tickets.edit');
    Route::put('/tickets/{id}', [TicketController::class, 'update'])->name('tickets.update');
    Route::delete('/tickets/{id}', [TicketController::class, 'destroy'])->name('tickets.destroy');

    // Gestión de categorías
    Route::resource('categories', CategoryController::class);

    // Gestión de estados
    Route::resource('statuses', StatusController::class);

    // Comentarios
    Route::post('/tickets/{id}/comments', [CommentController::class, 'store'])->name('comments.store');

    // ruta para cambiar el estado de un ticket
    Route::put('/tickets/{id}/status', [TicketController::class, 'changeStatus'])->name('tickets.changeStatus');

    //user controller
    Route::resource('users', UserController::class)->middleware('auth');
    
    //Route::get('/users', [UserController::class, 'index'])->name('users.index');
    //Route::resource('users', UserController::class)->middleware(['auth', 'role:admin']);

});



require __DIR__.'/auth.php';

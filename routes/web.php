<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PagesController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes();

Route::get('', [PagesController::class, 'home'])->name('home');
Route::get('/home', [PagesController::class, 'home'])->name('home');

Route::middleware(['auth', 'verified'])->group(function(){
    // Rotas logadas
    Route::get('dashboard', [PagesController::class, 'dashboard'])->name('dashboard');
});

Route::prefix('admin')->middleware(['auth', 'verified'])->group(function () {
    // Rotas logadas no sistema
    Route::get('', [PagesController::class, 'dashboard'])->name('dashboard');
    Route::resource('/users', UserController::class)->names('users');
});

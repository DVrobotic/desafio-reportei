<?php

use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PagesController;
use App\Http\Controllers\BoxController;
use App\Http\Controllers\ContentController;

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

//rota redirecionando para login
Route::get('', [PagesController::class, 'home'])->name('home');

route::post('/webhook/payload', [PagesController::class, 'payloadHandler'])->name('webhook.payload');

//github login
Route::get('/auth/redirect', [LoginController::class, 'githubRedirect'])->name('github.redirect');

Route::get('/auth/callback', [LoginController::class, 'githubLogin'])->name('github.login');

Route::middleware(['auth'])->group(function(){
    // Rotas logadas
    Route::get('dashboard', [PagesController::class, 'dashboard'])->name('dashboard');
});

// Rotas logadas no sistema
Route::prefix('admin')->middleware(['auth'])->group(function () {

    //users
    Route::resource('/users', UserController::class)->names('users');

    //boxes
    Route::post('/boxes/banner', [BoxController::class, 'changeBanner'])->name('boxes.banner')->middleware('can:create,App\Models\Box');;
    Route::resource('/boxes', BoxController::class)->names('boxes');

    //content
    Route::delete('/content/delete', [ContentController::class, 'destroy'])->name('contents.destroy')->middleware('can:delete,App\Models\Content');
    Route::post('/content/download/', [ContentController::class, 'downloadFile'])->name('contents.download');
});

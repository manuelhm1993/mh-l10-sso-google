<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use Laravel\Socialite\Facades\Socialite;

use App\Models\User;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

// --------------------------------- Rutas OAuth Socialite
Route::get('/google-auth/redirect', function () {
    return Socialite::driver('google')->redirect();
});
 
Route::get('/google-auth/callback', function () {
    // El método stateless permite eliminar la verificación de estado de sesión, al hacer google auth no se necesitan cookies
    $googleUser = Socialite::driver('google')->stateless()->user();

    //dd($user);
    
    // Busca si existe un usuario con el google_id que nos envía socialite y actualiza, si no, crea un nuevo usuario en BD
    $user = User::updateOrCreate(
        [
            'google_id' => $googleUser->id,
        ], 
        [
            'name' => $googleUser->name,
            'email' => $googleUser->email,
        ]
    );
 
    // Hacer login con el usuario
    Auth::login($user);
 
    // Redirigirlo al dashboard
    return redirect('/dashboard');
 
    // $user->token
});

// --------------------------------- Rutas Admin
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

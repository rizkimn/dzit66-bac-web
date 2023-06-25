<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
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

Route::get('/info', function () {
    dd(Auth::getUser()->role);
})->middleware('auth');

Route::get('/admin/dashboard', function () {
    return view('admin');
})->middleware('auth');

Route::get('/user/dashboard', function () {
    return view('user');
})->middleware('auth');

Route::get('/login', function () {
    return view('login');
})->name('login')->middleware('guest');

Route::post('/login', function (Request $request) {
    $credentials = $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);

    if (Auth::attempt($credentials)) {
        if (Auth::getUser()->role == 'admin') {
            return redirect('/admin/dashboard');
        }

        return redirect('/user/dashboard');
    }

    return back()->withErrors([
            'password' => 'Your Login is Failed! Remember Your Identity!'
        ]);
});

Route::get('/register', function () {
    return view('register');
})->middleware('guest');

Route::post('/register', function (Request $request) {
    $validated = $request->validate([
        'username' => 'required|string',
        'email'    => 'required|email',
        'password' => 'required|min:8'
        ]);

    $validated['password'] = Hash::make($validated['password']);

    User::create($validated);

    return redirect('/login');
});

Route::delete('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/login');
});

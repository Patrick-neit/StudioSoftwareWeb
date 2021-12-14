<?php

use Illuminate\Support\Facades\Route;

use App\Models\User;

use App\Http\Controllers\SessionController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ModuloController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|4 agost
*/

Route::get('/', function () {
    return view('login');
});

/*Route::get('/', function () {
    return view('jitsi_view');
});*/












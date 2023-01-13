<?php

use App\Http\Controllers\DealsController;
use App\Http\Controllers\FavouritesController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;

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

Route::get('/', function () {
    return view('home');
});

Route::get('/home', function () {
    return view('home');
});

Route::get('/clearsession', function() {
    Session::flush();
    return redirect()->back();
});

Route::get('/register', [UserController::class, 'registration']);

Route::get('/login', function() {
    return redirect('/');
});

Route::get('/logout', [UserController::class, 'logout']);

Route::get('/clearOrder', [OrderController::class, 'clearOrder']);

Route::get('/saveFavourite', [FavouritesController::class, 'saveFavourite']);

Route::get('/getFavourite', [FavouritesController::class, 'getFavourite']);

Route::get('/removeFavourite', [FavouritesController::class, 'removeFavourite']);

Route::get('/submitOrder', [OrderController::class, 'submitOrder']);

Route::post('/login', [UserController::class, 'login']);

Route::post('/store', [UserController::class, 'store']);

Route::post('/addPizza', [OrderController::class, 'addToOrderInput']);

Route::post('/addDeals', [DealsController::class, 'addDeals']);

Route::post('/selectDelivery', [OrderController::class, 'selectDelivery']);



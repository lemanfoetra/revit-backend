<?php

use App\Http\Controllers\Owner\Service;
use App\Http\Controllers\Seeker\Bengkel;
use App\Http\Controllers\Seeker\CariBengkel;
use App\Http\Controllers\Seeker\Home;
use App\Http\Controllers\Visitor;
use App\Http\Controllers\Seeker\Wishlist;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('register', [UserController::class, 'register']);
Route::post('login', [UserController::class, 'login']);
Route::get(
    'user',
    [UserController::class, 'getAuthenticatedUser']
)->middleware('auth.api.role:SEEKER,OWNER');

Route::put(
    'user',
    [UserController::class, 'update']
)->middleware('auth.api.role:SEEKER,OWNER');


Route::prefix('owner')->group(function () {
    // Route Managment Service
    Route::get('/service', [Service::class, 'index'])->middleware('auth.api.role:SEEKER,OWNER');
    Route::get('/service/{service_id}', [Service::class, 'show'])->middleware('auth.api.role:OWNER,SEEKER');
    Route::post('/service', [Service::class, 'store'])->middleware('auth.api.role:OWNER');
    Route::put('/service/{service}', [Service::class, 'update'])->middleware('auth.api.role:OWNER');
    Route::delete('/service/{service}', [Service::class, 'delete'])->middleware('auth.api.role:OWNER');

    // Route get total visitor
    Route::get('/visitor', [Visitor::class, 'index'])->middleware('auth.api.role:OWNER');
});


Route::prefix('seeker')->group(function () {
    // Route Tab Home Seeker
    Route::get('/home/slider', [Home::class, 'slider'])->middleware('auth.api.role:SEEKER');
    Route::get('/home/bengkel_terdekat/{latitude}/{longitude}/{limit?}', [Home::class, 'bengkelTerdekat'])->middleware('auth.api.role:SEEKER');

    // Route get data bengkel
    Route::get('/bengkel/{id}', [Bengkel::class, 'index'])->middleware('auth.api.role:SEEKER');
    Route::get('/bengkel/{id}/services', [Bengkel::class, 'allServices'])->middleware('auth.api.role:SEEKER');
    Route::get('/bengkel/{id}/services/{service_id}', [Bengkel::class, 'service'])->middleware('auth.api.role:SEEKER');

    // Route pencarian bengkel
    Route::get('/bengkel/cari/{keyword}/{latitude}/{longitude}/{limit?}', [CariBengkel::class, 'cariBengkelTerdekat'])
        ->middleware('auth.api.role:SEEKER');

    // Route wishlist
    Route::get('/wishlist', [Wishlist::class, 'index'])->middleware('auth.api.role:SEEKER');
    Route::post('/wishlist', [Wishlist::class, 'store'])->middleware('auth.api.role:SEEKER');
    Route::delete('/wishlist/{id}', [Wishlist::class, 'destroy'])->middleware('auth.api.role:SEEKER');

    // Route add visitor
    Route::post('/visitor', [Visitor::class, 'store'])->middleware('auth.api.role:SEEKER');
});

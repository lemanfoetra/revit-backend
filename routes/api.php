<?php

use App\Http\Controllers\Owner\Service;
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


Route::prefix('service')->group(function () {
    Route::get('/', [Service::class, 'index'])->middleware('auth.api.role:SEEKER,OWNER');
    Route::get('/{service_id}', [Service::class, 'show'])->middleware('auth.api.role:OWNER,SEEKER');
    Route::post('/', [Service::class, 'store'])->middleware('auth.api.role:OWNER');
    Route::put('/{service}', [Service::class, 'update'])->middleware('auth.api.role:OWNER');
    Route::delete('/{service}', [Service::class, 'delete'])->middleware('auth.api.role:OWNER');
});

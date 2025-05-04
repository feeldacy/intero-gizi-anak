<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HealthmapAdmin\RegisterController as HealthmapAdminRegisterController;
use App\Http\Controllers\NutritrackAdmin\RegisterController as NutritrackAdminRegisterController;


Route::get('/user', function (Request $request) {
    $user = $request->user();

    return response()->json([
        'id' => $user->id,
        'name' => $user->name,
        'email' => $user->email,
        'roles' => $user->getRoleNames()
    ]);
})->middleware('auth:sanctum');

Route::post('/register/nutritrack/admin', NutritrackAdminRegisterController::class);
Route::post('/register/healthmap/admin', HealthmapAdminRegisterController::class);

Route::post('/login', LoginController::class);
Route::post('/logout', LogoutController::class)->middleware('auth:sanctum');

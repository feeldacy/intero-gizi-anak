<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Children\AddChildController;
use App\Http\Controllers\Children\UpdateChildrenData;
use App\Http\Controllers\NutritrackAdmin\NutritionRecordController;
use App\Http\Controllers\Children\MonitoringController as MonitoringChildrenController;
use App\Http\Controllers\HealthmapAdmin\RegisterController as HealthmapAdminRegisterController;
use App\Http\Controllers\NutritrackAdmin\RegisterController as NutritrackAdminRegisterController;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return response()->json([
            'id' => $request->user()->id,
            'name' => $request->user()->name,
            'email' => $request->user()->email,
            'roles' => $request->user()->getRoleNames(),
        ]);
    });

    Route::post('/logout', LogoutController::class);

    /**
     * Children
     */
    Route::prefix('monitoring/children')->group(function () {
        Route::post('/create', [AddChildController::class, 'createChildData']);
        Route::get('/', [MonitoringChildrenController::class, 'index']);
        Route::put('/{id}', [UpdateChildrenData::class, 'updateChildData']);
    });

    /**
     * NutriTrack
     */
    Route::prefix('/nutritrack')->group(function () {
        Route::post('/create', [NutritionRecordController::class, 'store']);
    });

});

/**
 * Auth
 */
Route::post('/login', LoginController::class)->name('login');

/**
 * Registration
 */
Route::post('/register/nutritrack/admin', NutritrackAdminRegisterController::class);
Route::post('/register/healthmap/admin', HealthmapAdminRegisterController::class);












// Route::get('/user', function (Request $request) {
//     $user = $request->user();

//     return response()->json([
//         'id' => $user->id,
//         'name' => $user->name,
//         'email' => $user->email,
//         'roles' => $user->getRoleNames()
//     ]);
// })->middleware('auth:sanctum');

// Route::post('/register/nutritrack/admin', NutritrackAdminRegisterController::class);
// Route::post('/register/healthmap/admin', HealthmapAdminRegisterController::class);

// Route::post('/login', LoginController::class);
// Route::post('/logout', LogoutController::class)->middleware('auth:sanctum');

// Route::post('/monitoring/children/create', [AddChildController::class, 'createChildData'])->middleware('auth:sanctum');
// Route::get('/monitoring/children', [MonitoringChildrenController::class, 'index'])->middleware('auth:sanctum');

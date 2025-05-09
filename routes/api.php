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

Route::middleware(['auth:sanctum', 'role:nutritrackAdmin'])->group(function () {
    Route::get('/user', function (Request $request) {
        return response()->json([
            'id' => $request->user()->id,
            'name' => $request->user()->name,
            'email' => $request->user()->email,
            'unit_posyandu' => $request->user()->unit_id,
            'roles' => $request->user()->getRoleNames(),
        ]);
    });

    /**
     * Children
     */
    Route::prefix('monitoring/child-data')->group(function () {
        Route::post('/create', [AddChildController::class, 'createChildData']);
        Route::put('/update/{id}', [UpdateChildrenData::class, 'updateChildData']);
    });

    /**
     * NutriTrack
     */
    Route::prefix('/nutritrack/nutrition-record')->group(function () {
        Route::post('/create', [NutritionRecordController::class, 'store']);
    });

});



Route::middleware(['auth:sanctum', 'role:nutritrackAdmin|healthmapAdmin'])->group(function(){
    /**
     * Read Data
     */
    Route::get('/monitoring/child-data/get', [MonitoringChildrenController::class, 'index']);


    /**
     * Logout
     */
    Route::post('/logout', LogoutController::class);

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


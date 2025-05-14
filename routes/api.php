<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Children\AddChildController;
use App\Http\Controllers\Children\DeleteChildController;
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
        Route::delete('/delete/{id}', DeleteChildController::class);
    });

    /**
     * NutriTrack
     */
    Route::prefix('/nutritrack/nutrition-record')->group(function () {
        // Create nutrition record
        Route::post('/create', [NutritionRecordController::class, 'store']);

        // Update nutrition record
        Route::post('/update/{nutritionRecord:id}', [NutritionRecordController::class, 'update']);

        // Get latest nutrition records for all children
        Route::get('/', [NutritionRecordController::class, 'index']);

        // Get latest nutrition records by Posyandu unit
        Route::get('/by-posyandu/{unitId}', [NutritionRecordController::class, 'getByPosyanduUnit']);

        // Get latest nutrition record for a specific child
        Route::get('/child/{childId}', [NutritionRecordController::class, 'getChildLatestNutrition']);

        // Get nutrition history for a specific child
        Route::get('/child-history/{childId}', [NutritionRecordController::class, 'getChildNutritionHistory']);

        // Delete nutrition record
        Route::delete('/delete/{nutritionRecordId}', [NutritionRecordController::class, 'destroy']);
    });

});



Route::middleware(['auth:sanctum', 'role:nutritrackAdmin|healthmapAdmin'])->group(function(){
    /**
     * Read Data
     */
    Route::get('/monitoring/child-data/get', [MonitoringChildrenController::class, 'index']);

    /**
     * HealthMap (ini sebenernya bisa diakses nutritrack & healthmap kan yak? kutaruh sini dulu ya, tolong koreksi kalo misal ga tepat)
     */
    Route::prefix('/healthmap/nutrition-record')->group(function () {

        // Get summary of nutrition records for pie chart in healthmap dashboard
        Route::get('/summary', [NutritionRecordController::class, 'getNutritionSummary']);
    });


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


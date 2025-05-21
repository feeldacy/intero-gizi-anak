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
use App\Http\Controllers\HealthmapAdmin\MalnutritionController;
use App\Http\Controllers\HealthmapAdmin\DashboardController;


Route::middleware(['auth:sanctum', 'role:nutritrackAdmin|healthmapAdmin'])->group(function () {
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
        Route::put('/update/{childId}', [UpdateChildrenData::class, 'updateChildData']);
        Route::delete('/delete/{childId}', DeleteChildController::class);
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

    // Route::prefix('/healthmap/nutrition-record')->group(function () {

    // });


    /**
     * Logout
     */
    Route::post('/logout', LogoutController::class);

});

// Tambahkan di bagian yang sesuai, misalnya di dalam middleware auth:sanctum dengan role healthmapAdmin
Route::middleware(['auth:sanctum', 'role:healthmapAdmin'])->group(function(){
    // Malnutrition data endpoints
    Route::prefix('/healthmap')->group(function() {
        // Get latest malnutrition data grouped by posyandu
        Route::get('/malnutrition', [MalnutritionController::class, 'getLatestMalnutrition']);

        // Get detailed malnutrition data for a specific posyandu
        Route::get('/malnutrition/posyandu/{posyanduId}', [MalnutritionController::class, 'getPosyanduMalnutritionDetail']);

        // Get summary of nutrition records for pie chart in healthmap dashboard
        Route::get('/nutrition-record/summary', [DashboardController::class, 'getNutritionSummary']);

        // Get sum of child with malnutrition for every kecamatan
        Route::get('/malnutrition/kecamatan', [MalnutritionController::class, 'getMalnutritionStatsByKecamatan']);
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




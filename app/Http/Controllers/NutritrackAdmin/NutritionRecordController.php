<?php

namespace App\Http\Controllers\NutritrackAdmin;

use App\Models\NutritionRecord;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Services\NutritionRecordService;
use App\Http\Requests\StoreNutritionRecordRequest;
use App\Http\Requests\UpdateNutritionRecordRequest;

class NutritionRecordController extends Controller
{
    protected NutritionRecordService $nutritionService;

    public function __construct(NutritionRecordService $nutritionService){
        $this->nutritionService = $nutritionService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreNutritionRecordRequest $request)
    {
        try {
            //Validate and create the nutrition record
            $data = $request->validated();

            // Calculate BMI (weight in kg / (height in m)^2)
            $data['bmi'] = $this->nutritionService->calculateBMI($data['weight_kg'], $data['height_cm']);
            $data['nutrition_status'] = $this->nutritionService->classifyBMI($data['bmi']);

            // Store the nutrition record
            $nutritionRecord = $this->nutritionService->storeNutritionRecord($data);

            return response()->json([
                'message' => 'Nutrition record created successfully',
                'data' => $nutritionRecord
            ], 201);
        } catch (\Exception $e){
            return response()->json([
                'message' => 'Failed to create Nutrition Record',
                'status' => 'Error',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(NutritionRecord $nutritionRecord)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateNutritionRecordRequest $request, NutritionRecord $nutritionRecord)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(NutritionRecord $nutritionRecord)
    {
        //
    }
}

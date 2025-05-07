<?php

namespace App\Http\Controllers\NutritrackAdmin;

use App\Models\NutritionRecord;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreNutritionRecordRequest;
use App\Http\Requests\UpdateNutritionRecordRequest;
use Illuminate\Contracts\Cache\Store;

class NutritionRecordController extends Controller
{
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
        // //Check if the authenticated user has the 'nutritrackAdmin' role
        // if (!Auth::user()) {
        //     return response()->json([
        //         'message' => 'Unauthorized. Only Nutritrack Admins can add nutrition records.'
        //     ], 403);
        // }

        try {
            //Validate and create the nutrition record
            $data = $request->validated();

            // Calculate BMI (weight in kg / (height in m)^2)
            $heightInMeters = $data['height_cm'] / 100; // Convert height from cm to meters
            $bmi = $data['weight_kg'] / ($heightInMeters * $heightInMeters);

            // Add the calculated BMI to the validated data
            $data['bmi'] = round($bmi, 2); // Round BMI to 2 decimal places


            $nutritionRecord = NutritionRecord::create($data);

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

<?php

namespace App\Http\Controllers\Children;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreChildRequest;
use App\Models\Children;
use Illuminate\Http\JsonResponse;

class AddChildController extends Controller
{
    public function createChildData(StoreChildRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();

            $child = Children::create($validated);

            return response()->json([
                'message' => 'Child data created successfully',
                'data' => $child
            ], 201);
        } catch (\Exception $e){
            return response()->json([
                'message' => 'Failed to create Child Data',
                'status' => 'Error',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}


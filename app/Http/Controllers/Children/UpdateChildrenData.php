<?php

namespace App\Http\Controllers\Children;

use App\Http\Controllers\Controller;
use \App\Http\Requests\StoreChildRequest;
use App\Models\Children;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Exception;

class UpdateChildrenData extends Controller
{

    public function show(string $childId){
        try {
            $child = Children::findOrFail($childId);
            Log::info("Fetching child data for ID: $child");
            return response([
                'id' => $child->id,
                'name' => $child->name,
                'gender' => $child->gender,
                'birth_date' => $child->birth_date
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Child not found',
                'error' => $e->getMessage()
            ], 404);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'An error occurred while updating child data',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    /**
     * Update child data.
     *
     * @param StoreChildRequest $request
     * @param string $id
     * @return JsonResponse
     */
    public function updateChildData(StoreChildRequest $request, string $childId): JsonResponse
    {
        try {
            $child = Children::findOrFail($childId);

            $validatedData = $request->validated();
            $child->update($validatedData);

            return response()->json([
                'message' => 'Child data updated successfully',
                'data' => $child
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Child not found',
                'error' => $e->getMessage()
            ], 404);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'An error occurred while updating child data',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

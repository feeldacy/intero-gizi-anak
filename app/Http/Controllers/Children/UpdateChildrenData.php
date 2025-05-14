<?php

namespace App\Http\Controllers\Children;

use App\Http\Controllers\Controller;
use \App\Http\Requests\StoreChildRequest;
use App\Models\Children;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Exception;

class UpdateChildrenData extends Controller
{
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
            ], 422); // 422 Unprocessable Entity untuk error validasi
        } catch (Exception $e) {
            // Tangani error database atau error server lainnya
            return response()->json([
                'message' => 'An error occurred while updating child data',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

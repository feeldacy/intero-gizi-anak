<?php

namespace App\Http\Controllers\Children;

use App\Http\Controllers\Controller;
use App\Models\Children;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class DeleteChildController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(string $childId)
    {
        try {
            $childData = Children::findOrFail($childId);

            $childData->delete();

            return response()->json([
                'message' => 'Child data succesfully deleted'
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Child data record not found',
                'status' => 'Error',
                'error' => 'The requested child data does not exist'
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Failed to delete child data',
                'status' => 'Error',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

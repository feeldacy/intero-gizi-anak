<?php

namespace App\Http\Controllers\HealthmapAdmin;

use App\Models\NutritionRecord;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Services\NutritionRecordService;
use App\Http\Requests\StoreNutritionRecordRequest;
use App\Http\Requests\UpdateNutritionRecordRequest;
use App\Models\Children;
use App\Models\UnitPosyandu;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Exception;
use Illuminate\Http\Request;


class DashboardController extends Controller
{

    public function getNutritionSummary(): JsonResponse
    {
        try {
            $allChildren = Children::with(['nutritionRecords' => function ($query) {
                $query->latest()->take(1);
            }])->get();

            $giziBuruk = 0;
            $giziTercukupi = 0;

            foreach ($allChildren as $child) {
                $latestRecord = $child->nutritionRecords->first();

                if ($latestRecord) {
                    if ($latestRecord->nutrition_status !== 'Normal') {
                        $giziBuruk++;
                    } else {
                        $giziTercukupi++;
                    }
                }
            }

            return response()->json([
                'message' => 'Nutrition summary retrieved successfully',
                'data' => [
                    'Gizi Tercukupi' => ['Jumlah Anak' => $giziTercukupi],
                    'Gizi Buruk' => ['Jumlah Anak' => $giziBuruk],
                ]
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve nutrition summary',
                'status' => 'Error',
                'error' => $e->getMessage()
            ], 500);
        }
    }

}

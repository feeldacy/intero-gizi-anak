<?php

namespace App\Http\Controllers\HealthmapAdmin;

use App\Http\Controllers\Controller;
use App\Models\NutritionRecord;
use App\Models\UnitPosyandu;
use App\Models\Kecamatan;
use App\Models\Children;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;

class MalnutritionController extends Controller
{
    /**
     * Get latest malnutrition data grouped by posyandu
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getLatestMalnutrition(Request $request): JsonResponse
    {
        try {
            // Ambil tanggal terakhir dari catatan nutrisi yang bukan Normal
            $latestDateSubquery = NutritionRecord::where('nutrition_status', '!=', 'Normal')
                ->select('child_id', DB::raw('MAX(created_at) as latest_date'))
                ->groupBy('child_id');

            // Query untuk menggabungkan data dari berbagai tabel
            $malnutritionByPosyandu = UnitPosyandu::select(
                    'unit_posyandu.id as posyandu_id',
                    'unit_posyandu.name as posyandu_name',
                    'kecamatan.name as kecamatan_name',
                    DB::raw('MAX(nutrition_records.created_at) as latest_date'),
                    DB::raw('COUNT(DISTINCT nutrition_records.child_id) as case_count')
                )
                ->join('kecamatan', 'unit_posyandu.kecamatan_id', '=', 'kecamatan.id')
                ->join('children', 'kecamatan.id', '=', 'children.kecamatan_id')
                ->join('nutrition_records', 'children.id', '=', 'nutrition_records.child_id')
                // Join dengan subquery untuk mengambil hanya catatan nutrisi terbaru untuk setiap anak
                ->joinSub($latestDateSubquery, 'latest_records', function($join) {
                    $join->on('nutrition_records.child_id', '=', 'latest_records.child_id')
                         ->on('nutrition_records.created_at', '=', 'latest_records.latest_date');
                })
                ->where('nutrition_records.nutrition_status', '!=', 'Normal')
                ->groupBy('unit_posyandu.id', 'unit_posyandu.name', 'kecamatan.name')
                ->orderByDesc('latest_date'); // Urutkan berdasarkan tanggal terbaru

            // Tambahkan pagination
            $perPage = $request->input('per_page', 15);
            $page = $request->input('page', 1);
            $results = $malnutritionByPosyandu->paginate($perPage);

            return response()->json([
                'message' => 'Latest malnutrition data by posyandu retrieved successfully',
                'data' => $results
            ], 200);
            
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve malnutrition data',
                'status' => 'Error',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get detailed malnutrition data for a specific posyandu
     *
     * @param Request $request
     * @param string $posyanduId
     * @return JsonResponse
     */
    public function getPosyanduMalnutritionDetail(Request $request, string $posyanduId): JsonResponse
    {
        try {
            // Verify posyandu exists
            $posyandu = UnitPosyandu::with('kecamatan')->findOrFail($posyanduId);
            
            // Ambil tanggal terakhir dari catatan nutrisi untuk setiap anak
            $latestNutritionDateSubquery = NutritionRecord::select('child_id', DB::raw('MAX(created_at) as latest_date'))
                ->groupBy('child_id');
            
            // Query untuk mendapatkan detail anak-anak dengan gizi buruk
            $malnutritionDetails = NutritionRecord::select(
                    'children.id as child_id',
                    'children.name as child_name',
                    'children.birth_date',
                    'nutrition_records.created_at as record_date',
                    'nutrition_records.height_cm',
                    'nutrition_records.weight_kg',
                    'nutrition_records.bmi',
                    'nutrition_records.nutrition_status'
                )
                ->join('children', 'nutrition_records.child_id', '=', 'children.id')
                ->join('kecamatan', 'children.kecamatan_id', '=', 'kecamatan.id')
                // Join dengan subquery untuk mengambil hanya catatan nutrisi terbaru untuk setiap anak
                ->joinSub($latestNutritionDateSubquery, 'latest_nutrition', function($join) {
                    $join->on('nutrition_records.child_id', '=', 'latest_nutrition.child_id')
                         ->on('nutrition_records.created_at', '=', 'latest_nutrition.latest_date');
                })
                ->where('kecamatan.id', $posyandu->kecamatan_id)
                ->where('nutrition_records.nutrition_status', '!=', 'Normal')
                ->orderBy('nutrition_records.created_at', 'desc');
            
            // Tambahkan pagination
            $perPage = $request->input('per_page', 10);
            $results = $malnutritionDetails->paginate($perPage);
            
            // Hitung umur untuk setiap anak
            $resultsWithAge = $results->through(function ($item) {
                // Tambahkan field umur dalam bulan dan tahun
                $birthDate = Carbon::parse($item->birth_date);
                $now = Carbon::now();
                
                $ageInMonths = $birthDate->diffInMonths($now);
                $years = floor($ageInMonths / 12);
                $months = $ageInMonths % 12;
                
                $item->age = [
                    'years' => $years,
                    'months' => $months,
                ];
                
                return $item;
            });
            
            return response()->json([
                'message' => 'Malnutrition details for posyandu retrieved successfully',
                'posyandu' => [
                    'id' => $posyandu->id,
                    'name' => $posyandu->name,
                    'kecamatan' => [
                        'id' => $posyandu->kecamatan_id,
                        'name' => $posyandu->kecamatan->name ?? 'Unknown'
                    ]
                ],
                'statistics' => [
                    'total_cases' => $results->total(),
                ],
                'data' => $resultsWithAge
            ], 200);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Posyandu not found',
                'status' => 'Error',
                'error' => 'The requested posyandu does not exist'
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve malnutrition details',
                'status' => 'Error',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
<?php

namespace App\Http\Controllers\NutritrackAdmin;

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


class NutritionRecordController extends Controller
{
    protected NutritionRecordService $nutritionService;

    public function __construct(NutritionRecordService $nutritionService){
        $this->nutritionService = $nutritionService;
    }

    /**
     * Display a listing of all nutrition records with only latest record per child
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            // Gunakan metode yang berbeda untuk mendapatkan latest records per child
            $childIds = NutritionRecord::select('child_id')
                ->distinct()
                ->pluck('child_id');

            $latestRecords = collect();

            foreach ($childIds as $childId) {
                $latest = NutritionRecord::where('child_id', $childId)
                    ->with(['child' => function($query) {
                        $query->select('id', 'kecamatan_id', 'name', 'birth_date', 'gender', 'created_at', 'updated_at');
                        $query->with(['kecamatan' => function($query) {
                            $query->select('id', 'name');
                        }]);
                    }])
                    ->latest()
                    ->first();

                if ($latest) {
                    $latestRecords->push($latest);
                }
            }

            // Manual pagination (karena kita sudah menggunakan collection)
            $page = $request->input('page', 1);
            $perPage = $request->input('per_page', 15);

            $paginatedRecords = $latestRecords
                ->forPage($page, $perPage)
                ->values();

            $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
                $paginatedRecords,
                $latestRecords->count(),
                $perPage,
                $page,
                ['path' => \Illuminate\Support\Facades\Request::url()]
            );

            return response()->json([
                'message' => 'Latest nutrition records for all children retrieved successfully',
                'data' => $paginator
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve nutrition records',
                'status' => 'Error',
                'error' => $e->getMessage()
            ], 500);
        }
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
     * Get nutrition history for a specific child
     *
     * @param string $childId
     * @return JsonResponse
     */
    public function getChildNutritionHistory(string $childId): JsonResponse
    {
        try {
            // First check if the child exists
            $child = Children::with('kecamatan:id,name')->findOrFail($childId);

            // Get all nutrition records for this child
            $nutritionRecords = NutritionRecord::where('child_id', $childId)
                                               ->orderBy('created_at', 'desc')
                                               ->get();

            // Calculate trends and statistics
            $recordCount = $nutritionRecords->count();

            // If we have at least 2 records, calculate growth trends
            $growthTrends = null;

            if ($recordCount >= 2) {
                $firstRecord = $nutritionRecords->last(); // Oldest record
                $latestRecord = $nutritionRecords->first(); // Newest record

                $growthTrends = [
                    'height_change' => $latestRecord->height_cm - $firstRecord->height_cm,
                    'weight_change' => $latestRecord->weight_kg - $firstRecord->weight_kg,
                    'bmi_change' => $latestRecord->bmi - $firstRecord->bmi,
                    'days_between' => $firstRecord->created_at->diffInDays($latestRecord->created_at),
                ];
            }

            return response()->json([
                'message' => 'Child nutrition history retrieved successfully',
                'child' => [
                    'id' => $child->id,
                    'name' => $child->name,
                    'birth_date' => $child->birth_date,
                    'gender' => $child->gender,
                    'kecamatan' => [
                        'id' => $child->kecamatan_id,
                        'name' => $child->kecamatan->name ?? 'Unknown'
                    ]
                ],
                'statistics' => [
                    'record_count' => $recordCount,
                    'latest_status' => $nutritionRecords->first()->nutrition_status ?? null,
                    'latest_bmi' => $nutritionRecords->first()->bmi ?? null,
                    'growth_trends' => $growthTrends
                ],
                'records' => $nutritionRecords
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Child not found',
                'status' => 'Error',
                'error' => 'The requested child does not exist'
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve child nutrition history',
                'status' => 'Error',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get latest nutrition record for each child in a specific Posyandu unit
     *
     * @param Request $request
     * @param string $unitId
     * @return JsonResponse
     */
    public function getByPosyanduUnit(Request $request, string $unitId): JsonResponse
    {
        try {
            // First verify if the posyandu unit exists
            $posyandu = UnitPosyandu::findOrFail($unitId);

            // Subquery to get latest nutrition record ID for each child in this posyandu
            $latestRecords = NutritionRecord::selectRaw('MAX(nutrition_records.id) as id')
                ->join('children', 'nutrition_records.child_id', '=', 'children.id')
                ->where('children.kecamatan_id', $posyandu->kecamatan_id)
                ->groupBy('nutrition_records.child_id');

            $query = NutritionRecord::whereIn('id', function($query) use ($latestRecords) {
                    $query->select('id')->from($latestRecords);
                })
                ->with(['child' => function($query) {
                    $query->with('kecamatan:id,name');
                }]);

            // Filter by date range if provided
            if ($request->has('start_date') && $request->has('end_date')) {
                $query->whereBetween('created_at', [$request->start_date, $request->end_date]);
            }

            $nutritionRecords = $query->latest()
                                    ->paginate($request->input('per_page', 15));

            // Get some statistics
            $totalChildren = Children::where('kecamatan_id', $posyandu->kecamatan_id)->count();
            $childrenWithRecords = NutritionRecord::join('children', 'nutrition_records.child_id', '=', 'children.id')
                ->where('children.kecamatan_id', $posyandu->kecamatan_id)
                ->distinct('child_id')
                ->count('nutrition_records.child_id');

            return response()->json([
                'message' => 'Latest nutrition records for Posyandu unit retrieved successfully',
                'posyandu' => [
                    'id' => $posyandu->id,
                    'name' => $posyandu->name,
                    'kecamatan' => [
                        'id' => $posyandu->kecamatan_id,
                        'name' => $posyandu->kecamatan->name ?? 'Unknown'
                    ]
                ],
                'statistics' => [
                    'total_children' => $totalChildren,
                    'children_with_records' => $childrenWithRecords,
                    'total_records_shown' => $nutritionRecords->total()
                ],
                'data' => $nutritionRecords
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Posyandu unit not found',
                'status' => 'Error',
                'error' => 'The requested Posyandu unit does not exist'
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve nutrition records for Posyandu unit',
                'status' => 'Error',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display details of the latest nutrition record for a specific child
     *
     * @param string $childId
     * @return JsonResponse
     */
    public function getChildLatestNutrition(string $childId): JsonResponse
    {
        try {
            // First check if the child exists
            $child = Children::with('kecamatan:id,name')->findOrFail($childId);

            // Get the latest nutrition record for this child
            $nutritionRecord = NutritionRecord::where('child_id', $childId)
                                            ->latest()
                                            ->firstOrFail();

            return response()->json([
                'message' => 'Latest nutrition record for child retrieved successfully',
                'child' => [
                    'id' => $child->id,
                    'name' => $child->name,
                    'birth_date' => $child->birth_date,
                    'gender' => $child->gender,
                    'kecamatan' => [
                        'id' => $child->kecamatan_id,
                        'name' => $child->kecamatan->name ?? 'Unknown'
                    ]
                ],
                'nutrition_data' => $nutritionRecord,
            ], 200);

        } catch (ModelNotFoundException $e) {
            $type = strpos($e->getMessage(), 'App\\Models\\Children') !== false ? 'Child' : 'Nutrition record';
            return response()->json([
                'message' => $type . ' not found',
                'status' => 'Error',
                'error' => 'The requested ' . strtolower($type) . ' does not exist'
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve latest nutrition record for child',
                'status' => 'Error',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateNutritionRecordRequest $request, NutritionRecord $nutritionRecord)
    {
        try {
            // Validate and update the nutrition record
            $data = $request->validated();

            // Calculate BMI (weight in kg / (height in m)^2)
            $data['bmi'] = $this->nutritionService->calculateBMI($data['weight_kg'], $data['height_cm']);
            $data['nutrition_status'] = $this->nutritionService->classifyBMI($data['bmi']);

            // Update the nutrition record
            $nutritionRecord = $this->nutritionService->updateNutritionRecord($nutritionRecord, $data);

            return response()->json([
                'message' => 'Nutrition record updated successfully',
                'data' => $nutritionRecord
            ], 200);
        } catch (\Exception $e){
            return response()->json([
                'message' => 'Failed to update Nutrition Record',
                'status' => 'Error',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified nutrition record from storage.
     *
     * @param string $id
     * @return JsonResponse
     */
    public function destroy(string $nutritionRecordId): JsonResponse
    {
        try {
            $record = NutritionRecord::findOrFail($nutritionRecordId);

            $record->delete();

            return response()->json([
                'message' => 'Nutrition record deleted successfully'
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Nutrition record not found',
                'status' => 'Error',
                'error' => 'The requested nutrition record does not exist'
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Failed to delete nutrition record',
                'status' => 'Error',
                'error' => $e->getMessage()
            ], 500);
        }
    }

}

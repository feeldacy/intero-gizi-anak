<?php

namespace App\Http\Controllers\Children;

use App\Http\Controllers\Controller;
use App\Models\Children;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class MonitoringController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        // $query = Children::query()->with('kecamatan');
        $query = Children::query()
        ->join('kecamatan', 'children.kecamatan_id', '=', 'kecamatan.id')
        ->select(
            'children.id',
            'children.name',
            'children.birth_date',
            'children.gender',
            'children.created_at',
            'children.updated_at',
            'kecamatan.name as kecamatan'
        );

        if ($request->has('search')) {
            $searchTerm = $request->get('search');
            $query->where('name', 'LIKE', "%{$searchTerm}%");
        }

        if ($request->has('kecamatan_name')) {
            $kecamatanName = $request->get('kecamatan_name');
            $query->where('kecamatan.name', 'LIKE', "%{$kecamatanName}%");
        }


        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');

        if (in_array($sortBy, ['children.name', 'children.birth_date', 'children.created_at']) && in_array($sortOrder, ['asc', 'desc'])) {
            $query->orderBy($sortBy, $sortOrder);
        }

        $perPage = $request->get('per_page', 15);
        $children = $query->paginate($perPage);

        return response()->json([
            'message' => 'Children data retrieved successfully',
            'data' => $children->items(),
            'pagination' => [
                'total' => $children->total(),
                'per_page' => $children->perPage(),
                'current_page' => $children->currentPage(),
                'last_page' => $children->lastPage(),
            ],
        ], 200);
    }

}

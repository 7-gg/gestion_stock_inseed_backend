<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProductUnit;
use App\Traits\ApiResponses;
use Illuminate\Http\Request;

class ProductUnitController extends Controller
{
    use ApiResponses;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Récupération des filtres depuis la requête
        $filters = $request->only(['search', 'page']);

        $query = ProductUnit::query();

        // Recherche sur le nom de l'unité
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where('name', 'like', "%{$search}%");
        }

        // Tri : d'abord par mise à jour, puis création
        $query->orderBy('updated_at', 'desc');

        // Pagination
        $units = $query->paginate(12);

        return $this->success([
            'items' => $units->items(),
            'meta' => [
                'total'        => $units->total(),
                'per_page'     => $units->perPage(),
                'current_page' => $units->currentPage(),
                'last_page'    => $units->lastPage(),
            ],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        // Vérifier si une unité existe déjà (y compris soft deleted)
        $unit = ProductUnit::withTrashed()->firstWhere('name', $data['name']);

        if ($unit) {
            if ($unit->trashed()) {
                // Restaurer l’unité supprimée
                $unit->restore();
                return response()->json($unit, 200); // OK mais déjà existant
            }

            // L’unité existe déjà
            return response()->json([
                'message' => 'Cette unité existe déjà.'
            ], 409); // 409 = Conflict
        }

        // Créer une nouvelle unité
        $unit = ProductUnit::create($data);

        return response()->json($unit, 201);
    }


    /**
     * Display the specified resource.
     */
    public function show(ProductUnit $unit)
    {
        return $this->success($unit);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ProductUnit $unit)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $unit->update($data);

        return $this->success($unit);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProductUnit $unit)
    {
        $unit->delete();

        return response()->json(null, 204);
    }
}

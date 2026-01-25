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
        $filters = $request->only(['q']);

        $query = ProductUnit::query();

        if (!empty($filters['q'])) {
            $q = $filters['q'];
            $query->where(function ($qB) use ($q) {
                $qB->where('name', 'like', "%$q%");
            });
        }

        // Ici l'ordre s'applique à toute la requête
        $query->orderBy('updated_at', 'desc');

        $units = $query->paginate(15);

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
        ]);

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

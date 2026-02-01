<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProductCategory;
use App\Traits\ApiResponses;
use Illuminate\Http\Request;

class ProductCategoryController extends Controller
{
    use ApiResponses;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $filters = $request->only(['search', 'page']);

        $query = ProductCategory::query();

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where('name', 'like', "%$search%");
        }

        // Tri par mise à jour puis création
        $query->orderBy('updated_at', 'desc')
            ->orderBy('created_at', 'desc');

        $categories = $query->paginate(15);

        return $this->success([
            'items' => $categories->items(),
            'meta' => [
                'total'        => $categories->total(),
                'per_page'     => $categories->perPage(),
                'current_page' => $categories->currentPage(),
                'last_page'    => $categories->lastPage(),
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

        // Vérifier si une catégorie existe déjà (y compris soft-deleted)
        $category = ProductCategory::withTrashed()->firstWhere('name', $data['name']);

        if ($category) {
            if ($category->trashed()) {
                // Restaurer la catégorie supprimée
                $category->restore();
                return response()->json($category, 200); // OK, réactivé
            }

            // La catégorie existe déjà
            return response()->json([
                'message' => 'Cette catégorie existe déjà.'
            ], 409); // 409 = Conflict
        }

        // Créer une nouvelle catégorie
        $category = ProductCategory::create($data);

        return response()->json($category, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(ProductCategory $category)
    {
        return $category;
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ProductCategory $category)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $category->update($data);

        return $this->success($category);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProductCategory $category)
    {
        $category->delete();

        return response()->json(null, 204);
    }
}

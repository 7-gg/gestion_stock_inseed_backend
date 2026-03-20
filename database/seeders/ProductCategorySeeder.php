<?php

namespace Database\Seeders;

use App\Models\ProductCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'FOURNITURES DE BUREAU'],
            ['name' => 'FOURNITURES D’ENTRETIEN'],
            ['name' => 'FOURNITURES INFORMATIQUES'],
        ];

        foreach ($categories as $category) {
            ProductCategory::updateOrCreate(['name' => $category['name']], $category);
        }
    }
}

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
            ['name' => 'Fournitures de bureau', 'description' => 'Papeterie, stylos, et petit matériel de bureau.'],
            ['name' => 'Matériel Informatique', 'description' => 'Consommables imprimantes, toners, et petits accessoires.'],
            ['name' => 'Produits d\'entretien', 'description' => 'Produits de nettoyage et hygiène pour les locaux.'],
            ['name' => 'Mobilier de bureau', 'description' => 'Chaises, lampes et petits mobiliers.'],
            ['name' => 'Matériel de reprographie', 'description' => 'Rames de papier, reliures et transparents.'],
        ];

        foreach ($categories as $category) {
            ProductCategory::create($category);
        }
    }
}

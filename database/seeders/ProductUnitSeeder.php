<?php

namespace Database\Seeders;

use App\Models\ProductUnit;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductUnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $units = [
            ['name' => 'Pièce', 'description' => 'Unité individuelle'],
            ['name' => 'Rame', 'description' => 'Paquet de 500 feuilles (papier)'],
            ['name' => 'Carton', 'description' => 'Conditionnement par boîte'],
            ['name' => 'Lot', 'description' => 'Ensemble de plusieurs articles'],
            ['name' => 'Litre', 'description' => 'Pour les produits d\'entretien liquides'],
        ];

        foreach ($units as $unit) {
            ProductUnit::create($unit);
        }
    }
}

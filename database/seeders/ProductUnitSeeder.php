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
            'boite',
            'unité',
            'paquet de 5',
            'paquet',
            'Carton',
            'paquet de 100',
            'Unité',
            'paquet de 12',
            'paquet de 6',
            'Pièce',
            'paquet de 250',
            'Boite',
            'sachet',
            'Boite de 1 l',
            'Bidon de 2 litre',
            'boite de 1l',
            'jet de 4C',
            'Jet'
        ];

        foreach ($units as $unit) {
            ProductUnit::updateOrCreate(['name' => $unit]);
        }
    }
}

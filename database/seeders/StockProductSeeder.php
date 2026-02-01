<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Stock;
use App\Models\StockProduct;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StockProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $stocks = Stock::all();
        $products = Product::all();

        foreach ($stocks as $stock) {
            foreach ($products->random(6) as $product) {

                $minimum = rand(5, 20);

                // Cas volontaire de stock faible
                $quantity = rand(0, 1)
                    ? rand(0, $minimum - 1)   // inférieur au minimum
                    : rand($minimum + 5, 50); // stock normal

                StockProduct::create([
                    'stock_id' => $stock->id,
                    'product_id' => $product->id,
                    'provider' => fake()->company(),
                    'quantity' => $quantity,
                    'minimum_quantity' => $minimum,
                ]);
            }
        }
    }
}

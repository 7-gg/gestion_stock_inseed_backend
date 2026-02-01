<?php

namespace Database\Seeders;

use App\Models\Stock;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StockSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $admin = User::where('is_admin', true)->first();

        $stocks = [
            ['Stock Central', 'Siège'],
            ['Stock Informatique', 'Service IT'],
            ['Stock Bureau Nord', 'Bureau Nord'],
            ['Stock Bureau Sud', 'Bureau Sud'],
            ['Stock Dépôt', 'Entrepôt'],
        ];

        foreach ($stocks as [$name, $location]) {
            Stock::create([
                'name' => $name,
                'location' => $location,
                'created_by' => $admin->id,
            ]);
        }
    }
}

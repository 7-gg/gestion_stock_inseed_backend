<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductUnit;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::where('is_admin', true)->first()
            ?? User::factory()->create(['is_admin' => true]);

        $catBureau = ProductCategory::where('name', 'Fournitures de bureau')->first();
        $catInfo   = ProductCategory::where('name', 'Matériel Informatique')->first();

        $uPiece = ProductUnit::where('name', 'Pièce')->first();
        $uRame  = ProductUnit::where('name', 'Rame')->first();

        $products = [
            ['Papier A4 80g', $catBureau, $uRame, ['couleur' => 'blanc']],
            ['Papier A3 80g', $catBureau, $uRame, ['couleur' => 'blanc']],
            ['Stylo bleu', $catBureau, $uPiece, ['marque' => 'BIC']],
            ['Stylo rouge', $catBureau, $uPiece, ['marque' => 'BIC']],
            ['Cahier 100 pages', $catBureau, $uPiece, ['format' => 'A4']],
            ['Classeur A4', $catBureau, $uPiece, ['matière' => 'plastique']],
            ['Toner HP 85A', $catInfo, $uPiece, ['couleur' => 'Noir']],
            ['Toner Canon 737', $catInfo, $uPiece, ['couleur' => 'Noir']],
            ['Souris USB', $catInfo, $uPiece, ['type' => 'filaire']],
            ['Clavier USB', $catInfo, $uPiece, ['langue' => 'AZERTY']],
            ['Écran 24"', $catInfo, $uPiece, ['résolution' => 'Full HD']],
            ['Disque dur 1To', $catInfo, $uPiece, ['type' => 'SSD']],
            ['Clé USB 32Go', $catInfo, $uPiece, ['marque' => 'SanDisk']],
            ['Imprimante Laser', $catInfo, $uPiece, ['type' => 'Laser']],
            ['Routeur WiFi', $catInfo, $uPiece, ['norme' => 'WiFi 6']],
        ];

        foreach ($products as [$name, $cat, $unit, $chars]) {
            Product::create([
                'name' => $name,
                'category_id' => $cat->id,
                'unit_id' => $unit->id,
                'characteristics' => $chars,
                'created_by' => $admin->id,
            ]);
        }
    }
}

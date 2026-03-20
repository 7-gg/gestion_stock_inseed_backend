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
        $catBureau = ProductCategory::where('name', 'FOURNITURES DE BUREAU')->first()->id;
        $catEntretien = ProductCategory::where('name', 'FOURNITURES D’ENTRETIEN')->first()->id;
        $catInfo = ProductCategory::where('name', 'FOURNITURES INFORMATIQUES')->first()->id;

        $units = ProductUnit::all()->pluck('id', 'name');

        $data = [
            // FOURNITURES DE BUREAU
            ['name' => 'Agrafe (4mm Novus 8/4)', 'unit' => 'boite', 'cat' => $catBureau],
            ['name' => 'Agrafe (Novus stabil 23/10)', 'unit' => 'boite', 'cat' => $catBureau],
            ['name' => 'Agrafe (Novus stabil 23/13)', 'unit' => 'boite', 'cat' => $catBureau],
            ['name' => 'Agrafe (Novus stabil 23/15)', 'unit' => 'boite', 'cat' => $catBureau],
            ['name' => 'Agrafe (Novus stabil 23/17)', 'unit' => 'boite', 'cat' => $catBureau],
            ['name' => 'Agrafe (Novus stabil 26/6)', 'unit' => 'boite', 'cat' => $catBureau],
            ['name' => 'Agrafe (Novus stabil 23/6)', 'unit' => 'boite', 'cat' => $catBureau],
            ['name' => 'Agrafe (Novus stabil 24/6)', 'unit' => 'boite', 'cat' => $catBureau],
            ['name' => 'Agrafe pour agrafeuse tapissier', 'unit' => 'Boîte', 'cat' => $catBureau],
            ['name' => 'Agrafeuse Moyenne NOVUS B 40/4', 'unit' => 'unité', 'cat' => $catBureau],
            ['name' => 'Agrafeuse petite KANGARO DS-210', 'unit' => 'unité', 'cat' => $catBureau],
            ['name' => 'Bac courrier départ - courrier arrivée', 'unit' => 'unité', 'cat' => $catBureau],
            ['name' => 'Bloc note (A5)', 'unit' => 'paquet de 5', 'cat' => $catBureau],
            ['name' => 'Bloc note à spiral A5', 'unit' => 'paquet', 'cat' => $catBureau],
            ['name' => 'Chemise à rabat', 'unit' => 'Carton', 'cat' => $catBureau],
            ['name' => 'Chemise à sangle kraft carton de 25 unités', 'unit' => 'Carton', 'cat' => $catBureau],
            ['name' => 'Chemise ordinaire, paquet de 100', 'unit' => 'paquet', 'cat' => $catBureau],
            ['name' => 'Classeurs avec perforateur', 'unit' => 'unité', 'cat' => $catBureau],
            ['name' => 'Correcteur à bic (blanco)', 'unit' => 'paquet', 'cat' => $catBureau],
            ['name' => 'Crayon hb', 'unit' => 'paquet', 'cat' => $catBureau],
            ['name' => 'Criterium (07mm)', 'unit' => 'paquet', 'cat' => $catBureau],
            ['name' => 'Encreur bleu', 'unit' => 'Unité', 'cat' => $catBureau],
            ['name' => 'Encreur rouge', 'unit' => 'Unité', 'cat' => $catBureau],
            ['name' => 'Enveloppe A4 Carton de 10 paquets de 25 unités', 'unit' => 'Carton', 'cat' => $catBureau],
            ['name' => 'Enveloppe rectangulaire blanche', 'unit' => 'paquet', 'cat' => $catBureau],
            ['name' => 'Gomme (techno)', 'unit' => 'paquet', 'cat' => $catBureau],
            ['name' => 'Marqueur Effaçable', 'unit' => 'Paquet', 'cat' => $catBureau],
            ['name' => 'Marqueur permanent (bleu-rouge-noir)', 'unit' => 'paquet', 'cat' => $catBureau],
            ['name' => 'Mouilleur', 'unit' => 'Unité', 'cat' => $catBureau],
            ['name' => 'Papier bristol couleur pour séparation paquet de 10 unités', 'unit' => 'paquet', 'cat' => $catBureau],
            ['name' => 'Papier cartonné pour reliure (bleu rouge rose)', 'unit' => 'paquet de 100', 'cat' => $catBureau],
            ['name' => 'Papier étiquette d\'adresse AVERY 105x37mm 16', 'unit' => 'paquet de 100', 'cat' => $catBureau],
            ['name' => 'Papier étiquette separateur', 'unit' => 'Paquet', 'cat' => $catBureau],
            ['name' => 'Papiers rame double A 80g', 'unit' => 'Carton de 5 paquets', 'cat' => $catBureau],
            ['name' => 'Parafeurs avec bas renforcée', 'unit' => 'unité', 'cat' => $catBureau],
            ['name' => 'Perforateur V220', 'unit' => 'unité', 'cat' => $catBureau],
            ['name' => 'Pinces clip à dessin', 'unit' => 'paquet de 12', 'cat' => $catBureau],
            ['name' => 'Porte stylo bureau design', 'unit' => 'unité', 'cat' => $catBureau],
            ['name' => 'Post it grand formant ( carré )', 'unit' => 'paquet de 12', 'cat' => $catBureau],
            ['name' => 'Post it moyen rectangulaire', 'unit' => 'paquet', 'cat' => $catBureau],
            ['name' => 'Post it petit rectangulaire', 'unit' => 'paquet', 'cat' => $catBureau],
            ['name' => 'Registre courrier arrivée', 'unit' => 'unité', 'cat' => $catBureau],
            ['name' => 'Registre courrier départ', 'unit' => 'unité', 'cat' => $catBureau],
            ['name' => 'Registre de transmission', 'unit' => 'unité', 'cat' => $catBureau],
            ['name' => 'Règle plate en plastique', 'unit' => 'Unité', 'cat' => $catBureau],
            ['name' => 'Scotch Kaki GM', 'unit' => 'unité', 'cat' => $catBureau],
            ['name' => 'Blocs note format (spirale, forma A4)', 'unit' => 'Paquet de 6', 'cat' => $catBureau],
            ['name' => 'Lait peak', 'unit' => 'Carton', 'cat' => $catBureau],
            ['name' => 'Autocolant A5 (2 étiquettes par page)', 'unit' => 'Paquet', 'cat' => $catBureau],
            ['name' => 'Classeurs plastique avec anneaux (26x32 cm)', 'unit' => 'Pièce', 'cat' => $catBureau],
            ['name' => 'Séparateurs (papier intercalaire en couleur)', 'unit' => 'Paquet', 'cat' => $catBureau],
            ['name' => 'Sous-chemise paquet de 250', 'unit' => 'paquet de 250', 'cat' => $catBureau],
            ['name' => 'Stylo bleu à bille Schneider', 'unit' => 'paquet', 'cat' => $catBureau],
            ['name' => 'Stylo bleu à bille TRIO', 'unit' => 'paquet', 'cat' => $catBureau],
            ['name' => 'Stylo feutre Roller-TRIO MATE', 'unit' => 'Boite', 'cat' => $catBureau],
            ['name' => 'Stylo noir à bille TRIO', 'unit' => 'paquet', 'cat' => $catBureau],
            ['name' => 'Stylo rouge à bille TRIO', 'unit' => 'paquet', 'cat' => $catBureau],
            ['name' => 'Stylo Schneider topball 857', 'unit' => 'Boite', 'cat' => $catBureau],
            ['name' => 'Stylo vert à bille TRIO', 'unit' => 'paquet', 'cat' => $catBureau],
            ['name' => 'Surligneur (vert, rouge, bleu,rose,jaune)', 'unit' => 'paquet', 'cat' => $catBureau],
            ['name' => 'Tombome 23/8', 'unit' => 'boite', 'cat' => $catBureau],
            ['name' => 'Transparent reliure', 'unit' => 'paquet de 100', 'cat' => $catBureau],
            ['name' => 'Trombone 25mm', 'unit' => 'boite', 'cat' => $catBureau],
            ['name' => 'Trombone 32mm', 'unit' => 'boite', 'cat' => $catBureau],
            ['name' => 'Trombone 50mm', 'unit' => 'boite', 'cat' => $catBureau],

            // FOURNITURES D'ENTRETIEN
            ['name' => 'Aspirateur pour véhicule', 'unit' => 'unité', 'cat' => $catEntretien],
            ['name' => 'Balai avec manche en plastique', 'unit' => 'unité', 'cat' => $catEntretien],
            ['name' => 'Balai Magique avec Manche', 'unit' => 'unité', 'cat' => $catEntretien],
            ['name' => 'Boule désodorisant (kanfo)', 'unit' => 'sachet', 'cat' => $catEntretien],
            ['name' => 'Brosse à main (en plastic avec manche)', 'unit' => 'unité', 'cat' => $catEntretien],
            ['name' => 'Déodorisant pour WC (Scratch)', 'unit' => 'paquet', 'cat' => $catEntretien],
            ['name' => 'Désodorisant essence de citronnelle 200ml', 'unit' => 'unité', 'cat' => $catEntretien],
            ['name' => 'Désodorisants à voiture en liquide', 'unit' => 'unité', 'cat' => $catEntretien],
            ['name' => 'Désodorisants pour bureau air freshener', 'unit' => 'unité', 'cat' => $catEntretien],
            ['name' => 'Eau de javel 1l', 'unit' => 'Boite de 1 l', 'cat' => $catEntretien],
            ['name' => 'Insecticide (SOLDAT)', 'unit' => 'unité', 'cat' => $catEntretien],
            ['name' => 'Lave vitres carrefour 1litre', 'unit' => 'unité', 'cat' => $catEntretien],
            ['name' => 'Nettoyant tableau de bord voiture', 'unit' => 'unité', 'cat' => $catEntretien],
            ['name' => 'Papier hygiénique Roca (paquet de 10 unités)', 'unit' => 'paquet', 'cat' => $catEntretien],
            ['name' => 'Papier mouchoir amylis paquet de 15 unités', 'unit' => 'paquet', 'cat' => $catEntretien],
            ['name' => 'Papier serviette pliée plat 100 feuillets', 'unit' => 'boite', 'cat' => $catEntretien],
            ['name' => 'Savon en poudre Omo multiactive de 1kg', 'unit' => 'sachet', 'cat' => $catEntretien],
            ['name' => 'Savon liquide Mir 2 litre', 'unit' => 'Bidon de 2 litre', 'cat' => $catEntretien],
            ['name' => 'Savon vaisselle leader price 1l', 'unit' => 'boite de 1l', 'cat' => $catEntretien],
            ['name' => 'Serpillière', 'unit' => 'unité', 'cat' => $catEntretien],
            ['name' => 'Tuyau de pression pour lavage véhicule (50 m)', 'unit' => 'unité', 'cat' => $catEntretien],

            // FOURNITURES INFORMATIQUES
            ['name' => 'Aérosol dépoussiérant clavier (fellows)', 'unit' => 'unité', 'cat' => $catInfo],
            ['name' => 'Antivirus quatre postes kapeski', 'unit' => 'unité', 'cat' => $catInfo],
            ['name' => 'Cable HDMI 2m', 'unit' => 'unité', 'cat' => $catInfo],
            ['name' => 'Cable HDMI 5m', 'unit' => 'unité', 'cat' => $catInfo],
            ['name' => 'Câble USB pour imprimante', 'unit' => 'unité', 'cat' => $catInfo],
            ['name' => 'Cartouche d\'encre 17 A(Original)', 'unit' => 'unité', 'cat' => $catInfo],
            ['name' => 'Cartouche d\'encre 26A(Original)', 'unit' => 'unité', 'cat' => $catInfo],
            ['name' => 'Cartouche d\'encre 53 A (HP 2015)(Original)', 'unit' => 'unité', 'cat' => $catInfo],
            ['name' => 'Cartouche d\'encre 59A(Original)', 'unit' => 'unité', 'cat' => $catInfo],
            ['name' => 'Cartouche d\'encre 78 A(Original)', 'unit' => 'unité', 'cat' => $catInfo],
            ['name' => 'Cartouche d\'encre 79 A(Original)', 'unit' => 'unité', 'cat' => $catInfo],
            ['name' => 'Cartouche d\'encre 80A(Original)', 'unit' => 'unité', 'cat' => $catInfo],
            ['name' => 'Cartouche d\'encre 85 A(Original)', 'unit' => 'unité', 'cat' => $catInfo],
            ['name' => 'Cartouche d\'encre color 410 A(Original)', 'unit' => 'jet de 4C', 'cat' => $catInfo],
            ['name' => 'Cartouche d\'encre hp 4500 (901) couleur', 'unit' => 'unité', 'cat' => $catInfo],
            ['name' => 'Cartouche d\'encre hp 4500 (901) noir', 'unit' => 'unité', 'cat' => $catInfo],
            ['name' => 'Cartouche laserjet 1320 49A original', 'unit' => 'unité', 'cat' => $catInfo],
            ['name' => 'Cartouche laserjet HP original 05A', 'unit' => 'unité', 'cat' => $catInfo],
            ['name' => 'Clavier USB & HP', 'unit' => 'unité', 'cat' => $catInfo],
            ['name' => 'Clé USB 16 Go', 'unit' => 'unité', 'cat' => $catInfo],
            ['name' => 'Clé USB 4Go', 'unit' => 'unité', 'cat' => $catInfo],
            ['name' => 'Clé USB 8 Go', 'unit' => 'unité', 'cat' => $catInfo],
            ['name' => 'Connecteur RJ45', 'unit' => 'sachet', 'cat' => $catInfo],
            ['name' => 'Disque dure externe 1 terra (Toshiba)', 'unit' => 'unité', 'cat' => $catInfo],
            ['name' => 'Multiprise parafoudre 8 prises', 'unit' => 'unité', 'cat' => $catInfo],
            ['name' => 'Nettoyant Handboss Universel ordinateur', 'unit' => 'unité', 'cat' => $catInfo],
            ['name' => 'Souris USB & HP', 'unit' => 'unité', 'cat' => $catInfo],
            ['name' => 'Souris sans fil & HP', 'unit' => 'unité', 'cat' => $catInfo],
            ['name' => 'Switch 5ports', 'unit' => 'unité', 'cat' => $catInfo],
            ['name' => 'Toner Canon CEXV-14 Original', 'unit' => 'boite de 1', 'cat' => $catInfo],
            ['name' => 'Toner Canon CEXV-33 Original', 'unit' => 'Boite', 'cat' => $catInfo],
            ['name' => 'Toner C-EXV60', 'unit' => 'unité', 'cat' => $catInfo],
            ['name' => 'Toner laser pro 400 color 305 A', 'unit' => 'jet de 4C', 'cat' => $catInfo],
            ['name' => 'Toner HP-Laser Jet 100 color 126 A', 'unit' => 'Jet', 'cat' => $catInfo],
            ['name' => 'Toner color laserjet pro M276nw 131A', 'unit' => 'jet de 4C', 'cat' => $catInfo],
            ['name' => 'Toner color laserjet pro M454 415A', 'unit' => 'jet de 4C', 'cat' => $catInfo],
            ['name' => 'Toner color laserjet pro MFP M479fdw 415A', 'unit' => 'jet de 4C', 'cat' => $catInfo],
        ];

        foreach ($data as $item) {
            Product::updateOrCreate(
                ['name' => $item['name']],
                [
                    'category_id' => $item['cat'],
                    'unit_id' => $units[$item['unit']] ?? $units['unité'],
                    'created_by' => 1,
                ]
            );
        }
    }
}

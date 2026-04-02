<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;

class RestockExport implements FromCollection
{
    protected $products;

    public function __construct($products)
    {
        $this->products = $products;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return $this->products;
    }

    // Définition des colonnes du fichier Excel
    public function headings(): array
    {
        return [
            'Dépôt / Stock',
            'Désignation du Produit',
            'Quantité Actuelle',
            'Seuil d\'Alerte',
            'Statut'
        ];
    }

    // Correspondance des données
    public function map($row): array
    {
        return [
            $row->stock->name ?? 'Stock Inconnu',      // Affiche le nom au lieu de l'ID
            $row->product->name ?? 'Produit Inconnu', // Affiche le nom au lieu de l'ID    
            $row->quantity,
            $row->minimum_quantity,
            $row->quantity <= $row->minimum_quantity ? 'RUPTURE' : 'CRITIQUE'
        ];
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStockMovementsTable extends Migration
{
    public function up()
    {
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();

            $table->foreignId('stock_product_id')->constrained('stock_products')->restrictOnDelete();

            $table->enum('movement', ['ENTREE', 'SORTIE']);
            $table->unsignedInteger('quantity');

            // pour les entrées de stock
            $table->decimal('price', 10, 2)->nullable();
            // $table->json('serial_numbers')->nullable(); // c'est pour identifier specifiquement chaque unité en stock
            // pour les sorties de stock
            $table->string('beneficiary')->nullable();
            $table->string('beneficiary_email')->nullable();
            // la confirmation de réception ou de la livraison
            $table->foreignId('validated_by')->nullable()->constrained('users')->restrictOnDelete();
            $table->timestamp('validated_at')->nullable();
            // Commentaire
            $table->text('comment')->nullable();

            // retracer le responsable
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->timestamp('created_at')->useCurrent();


            // Contraintes et index
            $table->index(['stock_product_id', 'created_at']);
            $table->index(['movement', 'created_at']);
            $table->index(['stock_product_id', 'movement', 'created_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('stock_movements');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStocksTable extends Migration
{
    public function up()
    {
        Schema::create('stocks', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('location')->nullable();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->json('history')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index('created_by');
        });

        Schema::create('stock_users', function (Blueprint $table) {
            $table->id();

            // Relation vers la table stocks
            $table->foreignId('stock_id')
                ->constrained()
                ->cascadeOnDelete();

            // Relation vers la table users
            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            // le responable du stock
            $table->boolean('is_chief')->default(false);

            $table->text('comment')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }


    public function down()
    {
        Schema::dropIfExists('stock_users');
        Schema::dropIfExists('stocks');
    }
}

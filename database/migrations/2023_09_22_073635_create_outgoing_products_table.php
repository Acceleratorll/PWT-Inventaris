<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('outgoing_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('process_plan_id')->constrained();
            $table->foreignId('product_transaction_location_id')->constrained('product_transaction_location');
            $table->float('amount');
            $table->float('product_amount');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('outgoing_products');
    }
};

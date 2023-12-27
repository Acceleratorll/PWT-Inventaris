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
            $table->foreignId('process_plan_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained();
            $table->float('amount');
            $table->float('product_amount');
            $table->date('expired');
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

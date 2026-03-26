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
        Schema::create('product_variant_option_values', function (Blueprint $table) {
            $table->foreignId('product_variant_id')
                  ->constrained('product_variants')
                  ->cascadeOnDelete();

            $table->foreignId('option_value_id')
                  ->constrained('product_option_values')
                  ->cascadeOnDelete();

            $table->primary(['product_variant_id', 'option_value_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_variant_option_values');
    }
};

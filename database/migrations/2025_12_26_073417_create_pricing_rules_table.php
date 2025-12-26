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
        Schema::create('pricing_rules', function (Blueprint $table) {
            $table->id();
            $table->string('rule_name');
            $table->foreignId('material_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('printer_id')->nullable()->constrained()->onDelete('cascade');
            $table->decimal('min_volume', 15, 2)->nullable(); // in mm³
            $table->decimal('max_volume', 15, 2)->nullable(); // in mm³
            $table->decimal('min_weight', 10, 2)->nullable(); // in grams
            $table->decimal('max_weight', 10, 2)->nullable(); // in grams
            $table->decimal('price_per_gram', 10, 4)->nullable();
            $table->decimal('setup_fee', 10, 2)->default(0);
            $table->decimal('machine_time_rate', 10, 2)->nullable(); // per hour
            $table->decimal('margin_percent', 5, 2)->default(0); // Profit margin percentage
            $table->decimal('min_price', 10, 2)->default(5.00); // Minimum order price
            $table->integer('priority')->default(0); // Higher priority rules are matched first
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('material_id');
            $table->index('printer_id');
            $table->index(['is_active', 'priority']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pricing_rules');
    }
};

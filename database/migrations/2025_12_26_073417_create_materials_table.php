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
        Schema::create('materials', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type'); // PLA, PETG, ABS, Resin, etc.
            $table->decimal('density_g_cm3', 8, 4); // Material density in g/cm³
            $table->json('available_colors')->nullable();
            $table->decimal('base_cost_per_kg', 10, 2); // Base cost per kilogram
            $table->text('description')->nullable();
            $table->json('properties')->nullable(); // strength, flexibility, heat resistance, etc.
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('type');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('materials');
    }
};

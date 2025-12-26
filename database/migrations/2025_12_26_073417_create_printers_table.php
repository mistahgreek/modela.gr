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
        Schema::create('printers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('manufacturer')->nullable();
            $table->integer('build_volume_x'); // in mm
            $table->integer('build_volume_y'); // in mm
            $table->integer('build_volume_z'); // in mm
            $table->json('nozzle_sizes'); // e.g., [0.4, 0.6, 0.8]
            $table->json('supported_materials')->nullable(); // material IDs or names
            $table->decimal('hourly_rate', 10, 2)->nullable(); // Cost per hour of operation
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('printers');
    }
};

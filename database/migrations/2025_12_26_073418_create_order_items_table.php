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
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('three_d_model_id')->nullable()->constrained()->onDelete('set null');
            $table->string('item_type')->default('print'); // print, model_download, custom
            $table->string('title'); // Product title snapshot
            $table->integer('quantity')->default(1);
            $table->foreignId('material_id')->nullable()->constrained()->onDelete('set null');
            $table->string('color')->nullable();
            $table->decimal('layer_height', 4, 2)->nullable(); // in mm
            $table->integer('infill')->nullable(); // percentage
            $table->json('print_settings')->nullable(); // Additional settings
            $table->text('notes')->nullable();
            $table->decimal('unit_price', 10, 2);
            $table->decimal('total_price', 10, 2);
            $table->json('price_breakdown')->nullable();
            $table->string('production_file_path')->nullable(); // Path to file for production
            $table->timestamps();

            $table->index('order_id');
            $table->index('three_d_model_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};

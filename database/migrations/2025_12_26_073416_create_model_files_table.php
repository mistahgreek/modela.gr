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
        Schema::create('model_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('three_d_model_id')->constrained()->onDelete('cascade');
            $table->string('original_name');
            $table->string('disk')->default('public'); // public, s3, etc.
            $table->string('path');
            $table->string('mime_type');
            $table->bigInteger('size'); // in bytes
            $table->string('format'); // stl, obj, 3mf
            $table->string('checksum', 64); // SHA256 hash
            $table->json('metadata')->nullable(); // dimensions, volume, triangles, etc.
            $table->enum('processing_status', ['pending', 'processing', 'completed', 'failed'])->default('pending');
            $table->text('processing_error')->nullable();
            $table->timestamps();

            $table->index('three_d_model_id');
            $table->index('format');
            $table->index('processing_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('model_files');
    }
};

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
        Schema::create('three_d_models', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->nullable()->constrained()->onDelete('set null');
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('license')->default('personal'); // personal, commercial, creative_commons, etc.
            $table->json('tags')->nullable();
            $table->enum('visibility', ['public', 'private', 'unlisted'])->default('public');
            $table->enum('price_type', ['free', 'paid', 'print_only'])->default('free');
            $table->decimal('model_price', 10, 2)->nullable(); // Price for downloading the model
            $table->enum('status', ['draft', 'published', 'flagged', 'archived'])->default('draft');
            $table->integer('download_count')->default(0);
            $table->integer('view_count')->default(0);
            $table->integer('like_count')->default(0);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('slug');
            $table->index('user_id');
            $table->index('category_id');
            $table->index('status');
            $table->index('visibility');
            $table->index(['status', 'visibility']);
            $table->fullText(['title', 'description']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('three_d_models');
    }
};

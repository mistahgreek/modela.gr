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
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->unique()->nullable()->after('email');
            $table->string('avatar')->nullable()->after('password');
            $table->text('bio')->nullable()->after('avatar');
            $table->string('company')->nullable()->after('bio');
            $table->string('website')->nullable()->after('company');
            $table->string('location')->nullable()->after('website');
            $table->boolean('is_admin')->default(false)->after('location');
            $table->boolean('is_verified')->default(false)->after('is_admin');
            $table->timestamp('banned_at')->nullable()->after('is_verified');
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'username',
                'avatar',
                'bio',
                'company',
                'website',
                'location',
                'is_admin',
                'is_verified',
                'banned_at',
            ]);
            $table->dropSoftDeletes();
        });
    }
};

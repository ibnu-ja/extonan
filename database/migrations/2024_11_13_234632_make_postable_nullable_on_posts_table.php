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
        Schema::table('posts', function (Blueprint $table) {
            // Make the postable columns nullable
            $table->string('postable_type')->nullable()->change();
            $table->unsignedBigInteger('postable_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            // Reverse the changes if rolling back
            $table->string('postable_type')->nullable(false)->change();
            $table->unsignedBigInteger('postable_id')->nullable(false)->change();
        });
    }
};

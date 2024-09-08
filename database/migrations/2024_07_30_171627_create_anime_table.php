<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('anime', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('anilist_id');
            $table->json('description');
            $table->json('slug');
            $table->json('title');
            $table->foreignId('author_id')->references('id')->on('users');
            $table->json('metadata');
            $table->timestamps();
            $table->drafts();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('anime');
    }
};

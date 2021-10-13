<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAlbumsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('albums', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('name_real')->nullable();
            $table->string('name_trans')->nullable();
            $table->string('catalog')->nullable();
            $table->string('barcode')->nullable();
            $table->date('release_date')->nullable();
            $table->json('discs');
            $table->string('media_format')->nullable();
            $table->longText('desc')->nullable();
            $table->string('slug');
            // $table->unsignedBigInteger('event_id')->nullable();
            // $table->foreign('event_id')->references('id')->on('events');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('albums');
    }
}

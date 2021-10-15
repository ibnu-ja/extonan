<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArtistsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('artists', function (Blueprint $table) {
            $table->id();
            $table->date('birthdate')->nullable();
            $table->string('birthplace')->nullable();
            $table->string('name');
            $table->string('name_real')->nullable();
            $table->string('name_trans')->nullable();
            $table->longText('desc')->nullable();
            $table->char('sex')->nullable();
            $table->string('slug');
            $table->json('meta')->nullable();
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
        Schema::dropIfExists('artists');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trafficdata', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('unionads')->nullable();
            $table->string('videoads')->nullable();
            $table->string('searchengine')->nullable();
            $table->string('direct')->nullable();
            $table->string('email')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('trafficdata');
    }
};

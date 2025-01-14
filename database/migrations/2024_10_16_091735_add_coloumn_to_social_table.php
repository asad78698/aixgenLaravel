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
        Schema::table('social', function (Blueprint $table) {
            $table->unsignedBigInteger('trafficdata_id')->after('id');
            $table->foreign('trafficdata_id')
                ->references('id')->on('trafficdata')
                ->onUpdate('cascade')
                ->onDelete('cascade');     
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('social', function (Blueprint $table) {
            //
        });
    }
};

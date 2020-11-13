<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIBPSTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('i_b_p_s', function (Blueprint $table) {
            $table->id();
            $table->string('createdBy')->nullable();
            $table->text('placeName')->nullable();
            $table->text('flagNotes')->nullable();
            $table->integer('noOfIdentifications')->nullable();
            $table->string('createdOn')->nullable();
            $table->text('associatedMedia')->nullable();
            $table->string('locationLat')->nullable();
            $table->string('locationLon')->nullable();
            $table->string('locationScale')->nullable();
            $table->string('fromDate')->nullable();
            $table->string('toDate')->nullable();
            $table->string('rank')->nullable();
            $table->string('scientificName')->nullable();
            $table->string('commonName')->nullable();
            $table->string('family')->nullable();
            $table->string('genus')->nullable();
            $table->string('species')->nullable();
            $table->string('state')->nullable();
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
        Schema::dropIfExists('i_b_p_s');
    }
}

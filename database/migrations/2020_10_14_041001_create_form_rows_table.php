<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFormRowsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('form_rows', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('count_form_id');
            $table->string('sl_no')->nullable();
            $table->string('common_name')->nullable();
            $table->string('scientific_name')->nullable();
            $table->string('no_of_individuals')->nullable();
            $table->string('remarks')->nullable();
            $table->timestamps();

            $table->foreign('count_form_id')->references('id')->on('count_forms')->onUpdate("cascade");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('form_rows');
    }
}

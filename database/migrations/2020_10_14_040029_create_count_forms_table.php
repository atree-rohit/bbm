<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCountFormsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('count_forms', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('affilation')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->text('team_members')->nullable();
            $table->text('photo_link')->nullable();
            $table->string('location')->nullable();
            $table->string('coordinates')->nullable();
            $table->string('date')->nullable();
            $table->string('altitude')->nullable();
            $table->string('distance')->nullable();
            $table->string('weather')->nullable();
            $table->text('comments')->nullable();
            $table->string('filename');
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
        Schema::dropIfExists('count_forms');
    }
}

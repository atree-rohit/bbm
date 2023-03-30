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
        Schema::create('count_forms', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('name')->nullable();
            $table->string('affiliation')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->text('team_members')->nullable();
            $table->text('photo_link')->nullable();
            $table->string('location')->nullable();
            $table->string('state')->nullable();
            $table->string('district')->nullable();
            $table->string('coordinates')->nullable();
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            $table->string('date')->nullable();
            $table->string('date_cleaned')->nullable();
            $table->string('start_time')->nullable();
            $table->string('end_time')->nullable();
            $table->string('altitude')->nullable();
            $table->string('distance')->nullable();
            $table->text('weather')->nullable();
            $table->text('comments')->nullable();
            $table->string('file')->nullable();
            $table->string('original_filename')->nullable();
            $table->boolean('duplicate')->default(false);
            $table->boolean('validated')->default(false);
            $table->boolean('flag')->default(false);
            $table->text('flag_notes')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onUpdate("cascade");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('count_forms');
    }
};

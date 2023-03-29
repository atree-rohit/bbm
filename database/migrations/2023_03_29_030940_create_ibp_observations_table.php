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
        Schema::create('ibp_observations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('taxa_id')->nullable();
            $table->string('createdBy')->nullable();
            $table->text('placeName')->nullable();
            $table->string('flagNotes')->nullable();
            $table->string('createdOn')->nullable();
            $table->text('associatedMedia')->nullable();
            $table->string('locationLat')->nullable();
            $table->string('locationLon')->nullable();
            $table->string('fromDate')->nullable();
            $table->string('rank')->nullable();
            $table->string('scientificName')->nullable();
            $table->string('commonName')->nullable();
            $table->text('higherClassificationId')->nullable();
            $table->string('state')->nullable();
            $table->string('observedInMonth')->nullable();
            $table->string('scientific_name_cleaned')->nullable();
            $table->boolean('flag')->default(false);
            $table->text('flag_notes')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('taxa_id')->references('id')->on('taxas');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ibp_observations');
    }
};

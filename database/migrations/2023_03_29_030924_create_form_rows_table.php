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
        Schema::create('form_rows', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('count_form_id');
            $table->string('sl_no')->nullable();
            $table->string('common_name')->nullable();
            $table->string('scientific_name')->nullable();
            $table->unsignedBigInteger('taxa_id')->nullable();
            $table->string('individuals')->nullable();
            $table->integer('no_of_individuals_cleaned')->default(0);
            $table->string('remarks')->nullable();
            $table->string('id_quality')->nullable();
            $table->boolean('flag')->default(false);
            $table->text('flag_notes')->nullable();
            $table->timestamps();

            $table->foreign('count_form_id')->references('id')->on('count_forms')->onUpdate("cascade");
            $table->foreign('taxa_id')->references('id')->on('taxas')->onUpdate("cascade");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('form_rows');
    }
};

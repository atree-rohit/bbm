<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateINatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('i_nats', function (Blueprint $table) {
            $table->id();
            $table->string('observed_on_string')->nullable();
            $table->string('observed_on')->nullable();
            $table->string('time_observed_at')->nullable();
            $table->bigInteger('user_id')->nullable();
            $table->string('user_login')->nullable();
            $table->string('inat_created_at')->nullable();
            $table->string('inat_updated_at')->nullable();
            $table->string('quality_grade')->nullable();
            $table->string('license')->nullable();
            $table->text('image_url')->nullable();
            $table->text('tag_list')->nullable();
            $table->text('description')->nullable();
            $table->integer('num_identification_agreements')->nullable();
            $table->integer('num_identification_disagreements')->nullable();
            $table->text('place_guess')->nullable();
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            $table->string('coordinates_obscured')->nullable();
            $table->string('species_guess')->nullable();
            $table->string('scientific_name')->nullable();
            $table->string('common_name')->nullable();
            $table->bigInteger('taxon_id')->nullable();
            $table->string('taxon_family_name')->nullable();
            $table->string('taxon_subfamily_name')->nullable();
            $table->string('taxon_tribe_name')->nullable();
            $table->string('taxon_subtribe_name')->nullable();
            $table->string('taxon_genus_name')->nullable();
            $table->string('taxon_species_name')->nullable();
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
        Schema::dropIfExists('i_nats');
    }
}

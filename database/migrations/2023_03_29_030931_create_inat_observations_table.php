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
        Schema::create('inat_observations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('taxa_id')->nullable();
            $table->string('observed_on')->nullable();
            $table->string('location')->nullable();
            $table->float('latitude', 8, 6)->nullable();
            $table->float('longitude', 8, 6)->nullable();
            $table->string('place_guess')->nullable();
            $table->string('state')->nullable();
            $table->string('district')->nullable();
            $table->text('img_url')->nullable();
            $table->boolean('is_lepidoptera')->nullable();
            $table->text('description')->nullable();
            $table->string('quality_grade');
            $table->string('license_code')->nullable();
            $table->string('oauth_application_id')->nullable();
            $table->string('inat_created_at')->nullable();
            $table->string('inat_updated_at')->nullable();
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
        Schema::dropIfExists('inat_observations');
    }
};

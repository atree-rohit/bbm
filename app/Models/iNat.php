<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class iNat extends Model
{
    use LogsActivity, HasFactory;

    protected $fillable = ["observed_on_string", "observed_on", "time_observed_at", "user_id", "user_login", "created_at", "updated_at", "quality_grade", "license", "image_url", "tag_list", "description", "num_identification_agreements", "num_identification_disagreements", "place_guess", "latitude", "longitude", "coordinates_obscured", "species_guess", "scientific_name", "common_name", "taxon_id", "taxon_family_name", "taxon_subfamily_name", "taxon_tribe_name", "taxon_subtribe_name", "taxon_genus_name", "taxon_species_name"];

    protected static $logAttributes = ["observed_on_string", "observed_on", "time_observed_at", "user_id", "user_login", "created_at", "updated_at", "quality_grade", "license", "image_url", "tag_list", "description", "num_identification_agreements", "num_identification_disagreements", "place_guess", "latitude", "longitude", "coordinates_obscured", "species_guess", "scientific_name", "common_name", "taxon_id", "taxon_family_name", "taxon_subfamily_name", "taxon_tribe_name", "taxon_subtribe_name", "taxon_genus_name", "taxon_species_name"];

    protected static $logFillable = true;
    protected static $logOnlyDirty = true;
}

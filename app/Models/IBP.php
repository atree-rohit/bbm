<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class IBP extends Model
{
    use LogsActivity, HasFactory;

    protected $fillable = ["id", "createdBy", "placeName", "flagNotes", "noOfIdentifications", "createdOn", "associatedMedia", "locationLat", "locationLon", "locationScale", "fromDate", "toDate", "rank", "scientificName", "commonName", "family", "genus", "species", "state"];

    protected static $logAttributes = ["id", "createdBy", "placeName", "flagNotes", "noOfIdentifications", "createdOn", "associatedMedia", "locationLat", "locationLon", "locationScale", "fromDate", "toDate", "rank", "scientificName", "commonName", "family", "genus", "species", "state"];

    protected static $logFillable = true;
    protected static $logOnlyDirty = true;
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CountForm extends Model
{
    use LogsActivity, HasFactory;

    protected $fillable = ['name', 'affilation', 'phone', 'email', 'team_members', 'photo_link', 'location', 'coordinates', 'date', 'altiutude', 'distance', 'weather', 'comments', 'filename', 'duplicate'];

    protected static $logAttributes = ['name', 'affilation', 'phone', 'email', 'team_members', 'photo_link', 'location', 'coordinates', 'date', 'altiutude', 'distance', 'weather', 'comments', 'filename', 'duplicate'];
    protected static $logFillable = true;
    protected static $logOnlyDirty = true;


    public function rows()
    {
        return $this->hasMany(FormRow::class);
    }

}

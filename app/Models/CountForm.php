<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CountForm extends Model
{
    use HasFactory;

    public function rows()
    {
        return $this->hasMany(FormRow::class);
    }
}

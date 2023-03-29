<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormRow extends Model
{
    use HasFactory;

    public function taxa()
    {
        return $this->belongsTo(Taxa::class);
    }

    public function form()
    {
        return $this->belongsTo(CountForm::class, 'count_form_id');
    }

}

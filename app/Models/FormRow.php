<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormRow extends Model
{
    use HasFactory;

    protected $fillable = ['count_form_id', 'sl_no', 'common_name', 'scientific_name', 'taxa_id', 'individuals', 'no_of_individuals_cleaned', 'remarks', 'id_quality', 'flag', 'flag_notes'];

    public function taxa()
    {
        return $this->belongsTo(Taxa::class);
    }

    public function form()
    {
        return $this->belongsTo(CountForm::class, 'count_form_id');
    }

}

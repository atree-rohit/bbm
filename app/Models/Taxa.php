<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Taxa extends Model
{
    use HasFactory;

    public function count_observations()
    {
        return $this->hasMany(FormRow::class);
    }

    public function inat_observations()
    {
        return $this->hasMany(InatObservation::class);
    }

    public function ibp_observations()
    {
        return $this->hasMany(IbpObservation::class);
    }

    public function ifb_observations()
    {
        return $this->hasMany(IfbObservation::class);
    }

    public function observations()
    {
        return [
            'count' => $this->count_observations,
            'inat' => $this->inat_observations,
            'ibp' => $this->ibp_observations,
            'ifb' => $this->ifb_observations,            
        ];
    }




}

<?php

namespace App\Models;

use App\Models\CountForm;
use App\Models\IbpObservation;
use App\Models\IfbObservation;
use App\Models\InatObservation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Model
{
    use HasFactory;

    public function count_observations(){
        return $this->hasMany(CountForm::class);
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
        switch ($this->source) {
            case 'count':
                return $this->count_observations();
                break;
            case 'inat':
                return $this->inat_observations();
                break;
            case 'ibp':
                return $this->ibp_observations();
                break;
            case 'ifb':
                return $this->ifb_observations();
                break;
            default:
                return [];
                break;
        }
    }
}

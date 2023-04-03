<?php

namespace App\Http\Controllers;

use App\Models\CountForm;
use Illuminate\Http\Request;
use App\Models\IbpObservation;
use App\Models\IfbObservation;
use App\Models\InatObservation;

class DataCleanController extends Controller
{
    public function clean()
    {
        ini_set('max_execution_time', 180); //3 minutes
        $data = InatObservation::where("district", null)->get();
        $districts = json_decode(file_get_contents(public_path("data/geojson/districts.json")));
        $unmatched = [];
        foreach($data as $d){
            $set_flag = false;
            foreach($districts->features as $district){
                $p = $district->geometry->coordinates[0];
                $x = $this->isWithinBounds($d->longitude, $d->latitude, $p);
                if($x){
                    $d->state = $district->properties->state;
                    $d->district = $district->properties->district;
                    $d->save();
                    $set_flag = true;
                }
            }
            if(!$set_flag){
                $unmatched[] = $d->id;
            }
        }
        dd("success", $unmatched);
    }

    public function isWithinBounds($x, $y, $polygon)
    {
        $bounds = [
            "lat_min" => $polygon[0][1],
            "lat_max" => $polygon[0][1],
            "lon_min" => $polygon[0][0],
            "lon_max" => $polygon[0][0]
        ];
        foreach($polygon as $p){
            if($p[1] > $bounds["lat_max"]){
                $bounds["lat_max"] = $p[1];
            } else if($p[1] < $bounds["lat_min"]){
                $bounds["lat_min"] = $p[1];
            }
            if($p[0] > $bounds["lon_max"]){
                $bounds["lon_max"] = $p[0];
            } else if($p[0] < $bounds["lon_min"]){
                $bounds["lon_min"] = $p[0];
            }
        }
        return (($x >= $bounds["lon_min"] && $x <= $bounds["lon_max"])
            && ($y >= $bounds["lat_min"] && $y <= $bounds["lat_max"]));
    }
}

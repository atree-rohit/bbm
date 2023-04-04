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
        ini_set('max_execution_time', 600); //3 minutes
        // $data = InatObservation::where("district", null)->get();
        // $data = IbpObservation::where("district", null)->get();
        // $data = IfbObservation::where("district", null)->get();
        $data = CountForm::where("district", null)->get();
        $districts = json_decode(file_get_contents(public_path("data/geojson/districts_2023_clean.json")));
        
        
        $unmatched = [];
        foreach($data as $d){
            $set_flag = false;
            
            foreach($districts->features as $district){
                foreach($district->geometry->coordinates as $p){
                    $lat = round($d->latitude, 4);
                    $lon  = round($d->longitude, 4);
                    $x = $this->isWithinBounds($lon, $lat, $p);
                    if($x){
                        $d->state = $district->properties->state;
                        $d->district = $district->properties->district;
                        dd($d);
                        $d->save();
                        $set_flag = true;
                        break;
                    }
                }
                if($set_flag){
                    break;
                }
            }
            if(!$set_flag){
                $unmatched[] = [$d->id, $d->latitude, $d->longitude];
            }
        }
        dd("success", $unmatched);
    }

    private function isWithinBounds($lon, $lat, $polygon) {
        $intersections = 0;
    
        $vertices = count($polygon);
        for ($i = 0; $i < $vertices; $i++) {
            $j = ($i + 1) % $vertices;
    
            if ($polygon[$i][1] == $polygon[$j][1]) {
                continue;
            }
    
            if ($lat < min($polygon[$i][1], $polygon[$j][1])) {
                continue;
            }
    
            if ($lat >= max($polygon[$i][1], $polygon[$j][1])) {
                continue;
            }
    
            $x = ($lat - $polygon[$i][1]) * ($polygon[$j][0] - $polygon[$i][0]) / ($polygon[$j][1] - $polygon[$i][1]) + $polygon[$i][0];
    
            if ($x > $lon) {
                $intersections++;
            }
        }
    
        return ($intersections % 2) == 1;
    }
    

    public function isWithinBounds_0($x, $y, $polygon)
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

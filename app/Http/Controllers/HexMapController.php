<?php

namespace App\Http\Controllers;

use App\Models\IBP;
use App\Models\iNat;
use App\Models\FormRow;
use App\Models\CountForm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HexMapController extends Controller
{
    public function index()
    {
        ini_set('max_execution_time', 300);

        $forms = CountForm::select("id", "name", "latitude", "longitude", "location as place")
            ->where("coordinates", "<>", null)
            ->where("duplicate", "false")
            ->with("rows_cleaned")
            // ->limit(100)
            ->get();


        // $inats = iNat::select( "user_login as name", "latitude", "longitude", "scientific_name", "common_name", "taxon_family_name as family")
        $inats = iNat::select("user_login as name", "latitude", "longitude", "scientific_name_cleaned as scientific_name", "common_name", "place_guess as place")
            ->where("inat_created_at", "like", "%2020-09%")
            // ->limit(100)
            ->get();
        $ibps = IBP::select("createdBy as name", "locationLat as latitude", "locationLon as longitude", "scientificName as scientific_name", "commonName as common_name", "placeName as place")
            ->where("createdOn", "like", "%/09/2020%")
            // ->limit(100)
            ->get();

        // echo "<table border='0'>";
        // echo "<thead><th>Name</th><th>Latitude</th><th>Longitude</th><th>Place</th><th>Common Name</th><th>Scientific Name</th><th>Individuals</th><th>Source</th>";
        // foreach ($forms as $f) {
        //     foreach ($f->rows_cleaned as $r) {
        //         echo "<tr>";
        //         echo "<td>".$f->name."</td>";
        //         echo "<td>".$f->latitude."</td>";
        //         echo "<td>".$f->longitude."</td>";
        //         echo "<td>".$f->place."</td>";
        //         foreach (["common_name", "scientific_name", "individuals"] as $col) {
        //             echo "<td>".$r->{$col}."</td>";
        //         }
        //         echo "<td>counts</td>";
        //         echo "</tr>";
        //     }
        // }
        // foreach ($inats as $i) {
        //     echo "<tr>";
        //     foreach (["name", "latitude", "longitude", "place", "common_name", "scientific_name"] as $col) {
        //         echo "<td>".$i->{$col}."</td>";
        //     }
        //     echo "<td>1</td>";
        //     echo "<td>inat</td>";
        //     echo "</tr>";
        // }
        // foreach ($ibps as $i) {
        //     echo "<tr>";
        //     foreach (["name", "latitude", "longitude", "place", "common_name", "scientific_name"] as $col) {
        //         echo "<td>".$i->{$col}."</td>";
        //     }
        //     echo "<td>1</td>";
        //     echo "<td>ibp</td>";
        //     echo "</tr>";
        // }
        // echo "</table>";
        // dd();
        // return view("analysis.maps.index", compact("forms", "inats", "ibps"));
        return view("analysis.maps.states");
    }



    public function index_old()
    {
        $forms_raw = CountForm::with("rows")->where("coordinates", "<>", null)->where("duplicate", "false")->get();
        $forms = [];
        // dd($forms_raw->toArray());
        foreach ($forms_raw as $f) {
            $coord = explode(", ", $f["coordinates"]);
            $count = count($f["rows"]);
            $total  = 0;
            foreach ($f["rows"] as $fr) {
                $num =(int) $fr["no_of_individuals_cleaned"];
                if (is_numeric($num)) {
                    $total += $num;
                } else {
                    dd($num);
                }
            }

            $forms[] = [
                "id" => $f["id"],
                "name" => $f["name"],
                "latitude" => $coord[0],
                "longitude" => $coord[1],
                "species" => $count,
                "total" => $total
            ];
        }

        return view("analysis.maps.index", compact("forms"));
    }

    public function inat()
    {
        $inats = iNat::where("inat_created_at", "like", "%2020-09%")->get();
        $forms = [];

        foreach ($inats as $i) {
            $forms[] =[
                "id" => $i->id,
                "name" => $i->user_login,
                "latitude" => $i->latitude,
                "longitude" => $i->longitude,
                "species" => 1,
                "total" => 1,

            ];
        }

        return view("analysis.maps.index", compact("forms"));
    }

    public function ibp()
    {
        $ibps = IBP::where("createdOn", "like", "%/09/2020%")->get();
        $forms = [];

        foreach ($ibps as $i) {
            $forms[] =[
                "id" => $i->id,
                "name" => $i->createdBy,
                "latitude" => $i->locationLat,
                "longitude" => $i->locationLon,
                "species" => 1,
                "total" => 1,

            ];
        }

        return view("analysis.maps.index", compact("forms"));
    }
}

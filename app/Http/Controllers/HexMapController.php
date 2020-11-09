<?php

namespace App\Http\Controllers;

use App\Models\CountForm;
use Illuminate\Http\Request;

class HexMapController extends Controller
{
    public function index()
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
        // dd($forms);

        return view("analysis.maps.index", compact("forms"));
    }
}

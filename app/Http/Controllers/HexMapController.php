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
        $forms = CountForm::where("coordinates", "<>", null)
            ->where("duplicate", "false")
            ->with("rows")
            ->withCount(
                ['rows as total' => function ($query) {
                    $query->select(DB::raw('SUM(no_of_individuals_cleaned)'));
                }]
            )
            ->withCount("rows as species")
            ->limit(5)
            ->get();
        $inats = iNat::where("inat_created_at", "like", "%2020-09%")->get();
        $ibps = IBP::where("createdOn", "like", "%/09/2020%")->get();

        return view("analysis.maps.index", compact("forms"));
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

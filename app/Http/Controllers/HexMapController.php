<?php

namespace App\Http\Controllers;

use App\Models\CountForm;
use Illuminate\Http\Request;

class HexMapController extends Controller
{
    public function index()
    {
        $forms_raw = CountForm::with("rows")->where("coordinates", "<>", null)->get();
        $forms = [];
        // dd($forms_raw->first()->toArray());
        foreach($forms_raw as $f){
        	$coord = explode(", ", $f["coordinates"]);
        	$count = count($f["rows"]);
        	$total  = 0;
        	foreach($f["rows"] as $fr){
        		$num =(int) $fr["no_of_individuals"];
        		if(is_numeric($num))
        			$total += $num;
        		else
        			dd($num);
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
        



        return view("maps.index", compact("forms"));
    }
}

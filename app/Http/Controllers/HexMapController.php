<?php

namespace App\Http\Controllers;

use App\Models\CountForm;
use Illuminate\Http\Request;

class HexMapController extends Controller
{
    public function index()
    {
        $forms = CountForm::where("coordinates", "<>", null)->get();
        return view("maps.index");
    }
}

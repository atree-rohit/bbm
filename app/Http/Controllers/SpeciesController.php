<?php

namespace App\Http\Controllers;

use App\Models\FormRow;
use App\Models\Species;
use Illuminate\Http\Request;

class SpeciesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $species_names = FormRow::select("scientific_name", "common_name")
                            ->where("id_quality", "<>", "flag")
                            ->Where("id_quality", "<>", "not indian")
                            ->get()
                            ->groupBy("common_name");
        $name_count = [];
        foreach ($species_names as $key => $value) {
            $common_names = [];
            foreach($value as $k => $v){
                if(in_array($v->scientific_name, array_keys($common_names)))
                    $common_names[$v->scientific_name]++;
                else
                    $common_names[$v->scientific_name] = 1;
            }
            $name_count[$key] = $common_names;
        }
        echo "<table border=1>";
        foreach($name_count as$k=>$v){
            if(count($v) > 1){

            echo "<tr>";
            echo "<td>". $k ."<td>";
            echo "<td>". count($v) ."<td>";
            echo "<td>". json_encode($v) ."<td>";
            echo "</tr>";
            }
        }
        echo "</table>";
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Species  $species
     * @return \Illuminate\Http\Response
     */
    public function show(Species $species)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Species  $species
     * @return \Illuminate\Http\Response
     */
    public function edit(Species $species)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Species  $species
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Species $species)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Species  $species
     * @return \Illuminate\Http\Response
     */
    public function destroy(Species $species)
    {
        //
    }
}

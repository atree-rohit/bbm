<?php

namespace App\Http\Controllers;

use App\Models\iNat;
use Illuminate\Http\Request;

class INatController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $fields = ["id", "observed_on_string", "observed_on", "time_observed_at", "user_id", "user_login", "inat_created_at", "inat_updated_at", "quality_grade", "license", "image_url", "tag_list", "num_identification_agreements", "num_identification_disagreements", "place_guess", "latitude", "longitude", "coordinates_obscured", "species_guess", "scientific_name", "common_name", "taxon_id", "taxon_family_name", "taxon_subfamily_name", "taxon_tribe_name", "taxon_subtribe_name", "taxon_genus_name", "taxon_species_name"];
        $count = 0;

        $csv_file = public_path("/Data/inat_semi_cleaned.csv");
        $arrayFromCSV =  array_map('str_getcsv', file($csv_file));
        unset($arrayFromCSV[0]);
        echo "<table>";
        foreach ($arrayFromCSV as $row) {
            $inat = new iNat();
            foreach ($fields as $k=>$f) {
                $inat->$f = $row[$k] ?? null;
            }
            echo "<tr><td>".$row[0] . "</td><td>" . json_encode($row) . "</td><td>". count($row)."</td></tr>";
            $inat->save();
            $count++;
        }


        dd("iNat Import from CSV :" . $count);
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
     * @param  \App\Models\iNat  $iNat
     * @return \Illuminate\Http\Response
     */
    public function show(iNat $iNat)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\iNat  $iNat
     * @return \Illuminate\Http\Response
     */
    public function edit(iNat $iNat)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\iNat  $iNat
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, iNat $iNat)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\iNat  $iNat
     * @return \Illuminate\Http\Response
     */
    public function destroy(iNat $iNat)
    {
        //
    }
}

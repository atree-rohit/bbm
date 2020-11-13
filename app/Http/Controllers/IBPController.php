<?php

namespace App\Http\Controllers;

use App\Models\IBP;
use Illuminate\Http\Request;

class IBPController extends Controller
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
        $fields = ["id", "createdBy", "placeName", "flagNotes", "noOfIdentifications", "createdOn", "associatedMedia", "locationLat", "locationLon", "locationScale", "fromDate", "toDate", "rank", "scientificName", "commonName", "family", "genus", "species", "state"];
        $count = 0;

        $csv_file = public_path("/Data/ibp_semi_cleaned.csv");
        $arrayFromCSV =  array_map('str_getcsv', file($csv_file));
        unset($arrayFromCSV[0]);
        foreach ($arrayFromCSV as $row) {
            $ibp = new IBP();
            foreach ($fields as $k=>$f) {
                $ibp->$f = $row[$k];
            }
            $ibp->save();
            $count++;
        }


        dd("IBP Import from CSV :" . $count);
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
     * @param  \App\Models\IBP  $iBP
     * @return \Illuminate\Http\Response
     */
    public function show(IBP $iBP)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\IBP  $iBP
     * @return \Illuminate\Http\Response
     */
    public function edit(IBP $iBP)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\IBP  $iBP
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, IBP $iBP)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\IBP  $iBP
     * @return \Illuminate\Http\Response
     */
    public function destroy(IBP $iBP)
    {
        //
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\FormRow;
use App\Models\Analysis;
use App\Models\CountForm;
use Illuminate\Http\Request;

class AnalysisController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $forms = CountForm::with("rows")->where("duplicate", false)->orderBy("name")->get();

        // dd($forms[0]);

        return view("analysis.index", compact("forms"));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function summary()
    {

        $rows = FormRow::whereHas('form', function($q)
                {
                    $q->where('duplicate', '=', false);

                })
                ->where("id_quality", "species")
                ->get();
        $data = [];
        foreach($rows->groupBy("scientific_name") as $species){
            $no_of_individuals = 0;
            foreach($species as $s){
                $no_of_individuals+= $s->no_of_individuals_cleaned;
            }
            $data[] = [
                "scientific_name" => $species->first()->scientific_name,
                "common_name" => $species->first()->common_name,
                "no_of_lists" => count($species),
                "no_of_individuals" => $no_of_individuals
            ];
        }
        array_multisort( array_column($data, "no_of_lists"), SORT_DESC, $data );
        $main_species["lists"] = array_slice($data, 0, 25);
        array_multisort( array_column($data, "no_of_individuals"), SORT_DESC, $data );
        $main_species["individuals"] = array_slice($data, 0, 25);
        
        // dd();

        $summary["total_species"] = count($rows->groupBy("scientific_name"));
        $summary["total_individuals"] = $rows->sum("no_of_individuals_cleaned");

        return view("analysis.summary", compact("rows", "summary", "data", "main_species"));

    }

    public function create()
    {
        $count = 0;
        $forms = CountForm::with("rows")->get();
        foreach ($forms as $f) {
            $date = date_parse($f->date);
            if (!$date["warning_count"]) {
                $custom = $date["year"] . "-". $date["month"] . "-" . $date["day"];
                $custom_date = date_create($custom);
                $f->date_cleaned = $custom_date;
                // dd($f);
                $f->save();
                $count++;
            }
        }
        // $rows = FormRow::where("no_of_individuals", "!=", null)->where("no_of_individuals_cleaned", null)->get();
        // foreach ($rows as $r) {
        //     $r->no_of_individuals_cleaned = (int) $r->no_of_individuals;
        //     $r->save();
        //     $count++;
        // }
        echo "<h1>$count</h1>";
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
     * @param  \App\Models\Analysis  $analysis
     * @return \Illuminate\Http\Response
     */
    public function show(Analysis $analysis)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Analysis  $analysis
     * @return \Illuminate\Http\Response
     */
    public function edit(Analysis $analysis)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Analysis  $analysis
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Analysis $analysis)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Analysis  $analysis
     * @return \Illuminate\Http\Response
     */
    public function destroy(Analysis $analysis)
    {
        //
    }
}

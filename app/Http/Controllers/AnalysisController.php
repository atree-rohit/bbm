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
        $no_of_results = 25;
        $rows = FormRow::whereHas('form', function ($q) {
            $q->where('duplicate', '=', false);
        })
                ->where("id_quality", "species")
                ->get();
        $data = [];
        foreach ($rows->groupBy("scientific_name") as $species) {
            $no_of_individuals = 0;
            foreach ($species as $s) {
                $no_of_individuals+= $s->no_of_individuals_cleaned;
            }
            $data[] = [
                "scientific_name" => $species->first()->scientific_name,
                "common_name" => $species->first()->common_name,
                "no_of_lists" => count($species),
                "no_of_individuals" => $no_of_individuals
            ];
        }
        array_multisort(array_column($data, "no_of_lists"), SORT_DESC, $data);
        $main_species["lists"] = array_slice($data, 0, $no_of_results);
        array_multisort(array_column($data, "no_of_individuals"), SORT_DESC, $data);
        $main_species["individuals"] = array_slice($data, 0, $no_of_results);

        // dd();

        $summary["total_species"] = count($rows->groupBy("scientific_name"));
        $summary["total_individuals"] = $rows->sum("no_of_individuals_cleaned");

        return view("analysis.summary", compact("rows", "summary", "data", "main_species"));
    }

    public function summary_people()
    {
        $no_of_results = 20;
        $forms = CountForm::where("duplicate", false)->with("rows")->get()->groupBy("name");
        // dd(count($forms));
        $people = [];
        foreach ($forms as $p => $f) {
            $species = [];
            $individuals = 0;
            foreach ($f as $c) {
                foreach ($c->rows as $r) {
                    if (isset($species[$r->scientific_name])) {
                        $species[$r->scientific_name] += $r->no_of_individuals_cleaned;
                    } else {
                        $species[$r->scientific_name] = $r->no_of_individuals_cleaned;
                    }
                    $individuals += $r->no_of_individuals_cleaned;
                }
            }
            $people[] = [
                "name" => ucwords(strtolower($p)),
                "lists" => count($f),
                "species" => count($species),
                "individuals" => $individuals
            ];
        }
        array_multisort(array_column($people, "lists"), SORT_DESC, $people);
        $people = array_slice($people, 0, $no_of_results);


        return view("analysis.summary_people", compact("people"));
    }

    public function families()
    {
        $rows = FormRow::whereHas('form', function ($q) {
            $q->where('duplicate', '=', false);
        })
                ->where("id_quality", "species")
                ->where("family", "!=", null)
                ->get();
        $families = [];

        $x = $rows->groupBy("family");
        foreach ($x as $f => $y) {
            foreach ($y as $z) {
                if (isset($families[$f][$z->scientific_name])) {
                    $families[$f][$z->scientific_name] += $z->no_of_individuals_cleaned;
                } else {
                    $families[$f][$z->scientific_name] = $z->no_of_individuals_cleaned;
                }
            }
        }
        $family_data = [];
        foreach ($families as $k=>$f) {
            $individuals = 0;
            foreach ($f as $g) {
                $individuals += $g;
            }
            $family_data[$k] =[
                "species" => count($f),
                "individuals" => $individuals
            ];
        }

        return view("analysis.families", compact("family_data"));
    }
    public function create()
    {
        $count = 0;
        // $forms = CountForm::with("rows")->get();
        // foreach ($forms as $f) {
        //     $date = date_parse($f->date);
        //     if (!$date["warning_count"]) {
        //         $custom = $date["year"] . "-". $date["month"] . "-" . $date["day"];
        //         $custom_date = date_create($custom);
        //         $f->date_cleaned = $custom_date;
        //         // dd($f);
        //         $f->save();
        //         $count++;
        //     }
        // }
        // $rows = FormRow::where("no_of_individuals", "!=", null)->where("no_of_individuals_cleaned", null)->get();
        // foreach ($rows as $r) {
        //     $r->no_of_individuals_cleaned = (int) $r->no_of_individuals;
        //     $r->save();
        //     $count++;
        // }
        // $rows = FormRow::where("family", "!=", null)->andWhere("family_enum", null)->get();
        // foreach ($rows as $r) {
        //     $r->family_enum = $r->family;
        //     $r->save();
        //     $count++;
        // }
        // echo "<h1>$count</h1>";
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

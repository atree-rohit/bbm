<?php

namespace App\Http\Controllers;

use App\Models\FormRow;
use Illuminate\Http\Request;

class FormRowController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $rows = FormRow::with("form")->whereNotNull("common_name" )->get();
        $rows = FormRow::with("form")->get();

        foreach ($rows as $k=>$v) {
            if ($v->form->duplicate) {
                unset($rows[$k]);
            }
        }





        return view('species.index', compact('rows'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
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
     * @param  \App\Models\FormRow  $formRow
     * @return \Illuminate\Http\Response
     */
    public function show(FormRow $formRow)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\FormRow  $formRow
     * @return \Illuminate\Http\Response
     */
    public function edit(FormRow $formRow)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\FormRow  $formRow
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $row = FormRow::find($id);
        $fields = ['sl_no', 'common_name', 'scientific_name', 'no_of_individuals', 'remarks', 'id_quality'];
        foreach ($fields as $f) {
            if (isset($request->$f)) {
                $row->$f = $request->$f;
            }
        }
        $row->save();

        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\FormRow  $formRow
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $row = FormRow::find($id);
        $row->delete();

        return redirect()->back();
    }

    public function id_quality()
    {
        $col = $_GET["col"] ?? "scientific_name";
        $col_inv = ($col == "scientific_name") ? "common_name" : "scientific_name";

        $quality = $_GET["quality"] ?? "flag";
        $rows = FormRow::where($col, "<>", null)
                        // ->where("id_quality", "=", $quality)
                        ->where("id_quality", "=", $quality)
                        ->orderBy($col, "ASC")
                        ->get()
                        ->groupBy($col);
        $data = [];
        foreach ($rows as $k => $v) {
            $data[] = [$k, $v->count()];
        }
        return view('species.id_quality', compact('data'));
    }

    public function id_quality_update(Request $request)
    {
        $names = json_decode($request->names);
        $quality = $request->id_quality;
        $count = 0;

        foreach ($names as $n) {
            $rows = FormRow::where($request->col, "=", "$n")->get();
            $myRequest = new \Illuminate\Http\Request();
            $myRequest->setMethod('POST');
            $myRequest->request->add(['id_quality' => $quality]);
            foreach ($rows as $r) {
                $this->update($myRequest, $r->id);
                $count++;
            }
        }


        return redirect()->back()->with("success", $count);
    }

    public function correct()
    {
        $col = $_GET["col"] ?? "scientific_name";
        $col_inv = ($col == "scientific_name") ? "common_name" : "scientific_name";

        $quality = $_GET["quality"] ?? "flag";
        // $rows = FormRow::orderBy($col, "asc")
        //             // ->where("id_quality", "=", $quality)
        //             ->where($col_inv, "=", null)
        //             ->where("id_quality", "<>", "flag")
        //             ->get()
        //             ->groupBy($col);
        $rows = FormRow::orderBy($col, "asc")
                        ->where($col, "<>", null) 
                        // ->where("id_quality", "=", $quality)
                        ->where("id_quality", "=", $quality)
                        ->get()
                        ->groupBy($col);
        // dd($rows);

        $data = [];
        foreach ($rows as $k => $v) {
            $list = [];
            foreach($v as $w){
                if(!in_array($w->{$col_inv}, array_keys($list))) {
                    $list[$w->{$col_inv}] = 1;
                } else {
                    $list[$w->{$col_inv}]++;

                }
            }
            $data[] = [$k, json_encode($list), $v->count()];
        }

        return view('species.correct', compact('data'));
    }

    public function correct_update(Request $request)
    {
        $count = 0;
        if (strlen($request->corrected)) {
            $rows = FormRow::where($request->col, "=", $request->names)->get();
            $myRequest = new \Illuminate\Http\Request();
            $myRequest->setMethod('PUT');
            $myRequest->request->add([$request->col => $request->corrected]);
            foreach ($rows as $row) {
                $this->update($myRequest, $row->id);
                $count++;
            }
        }
        else
            dd($request->all());

        return redirect()->back()->with("success", $count);
    }

    public function common2sci(){
        $a = FormRow::where("id_quality", "=", "species")->get()->groupBy("common_name");
        $sci_name = "";
        $ele_without_sci_name = [];
        foreach($a as $b=>$c){
            foreach($c as $d){
                if(isset($d->scientific_name)){
                    // if()
                }
            }
        }

    }
}

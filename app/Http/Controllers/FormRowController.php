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
        echo $rows->count();
        foreach ($rows as $k=>$v) {
            if ($v->form->duplicate) {
                unset($rows[$k]);
            }
        }


        $common_names = $rows->unique("common_name")->pluck("common_name");
        $scientific_names = $rows->unique("species")->pluck("species");


        return view('species.index', compact('rows', 'common_names', 'scientific_names'));
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
        $fields = ['sl_no', 'common_name', 'scientific_name', 'no_of_individuals', 'remarks'];
        foreach ($fields as $f) {
            $row->$f = $request->$f;
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
}

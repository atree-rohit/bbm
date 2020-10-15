<?php

namespace App\Http\Controllers;

use App\Models\FormRow;
use App\Models\CountForm;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class CountFormController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $forms = CountForm::all();
        dd($forms);
    }
    public function import()
    {
        $uploaded_forms = CountForm::all()->pluck("filename")->toArray();
        // dd($uploaded_forms);
        $folder = storage_path("app/public/count_sheets");
        $files = scandir($folder);

        $files_array = $file_names = [];
        $start = $_GET["start"] ?? 0;
        if (isset($_GET["limit"])) {
            $count_end = $_GET["limit"]-1;
        } else {
            $count_end = 1000;
        }
        $count =0;
        $colors = ["gray", "steel", "secondary",  "dark"];


        foreach ($files as $f) {
            if ((substr($f, -4) == "xlsx") || (substr($f, -3) == "xls")) {
                if (!in_array($f, $uploaded_forms)) {
                    if ($count > $start) {
                        $name = substr($f, 0, strpos($f, ".xls"));
                        $raw_file = Excel::toArray(new CountForm, $folder."/".$f);
                        $x = $raw_file;
                        $files_array[$name] = $raw_file;
                        $this->newFile($f, $raw_file);
                    }
                } else {
                    echo "$f<br>";
                }
                if ($count > $start + $count_end) {
                    break;
                }
                $count++;
            }
        }

        return view('butterfly_count.index', compact('files_array', 'file_names', 'colors'));
    }

    public function newFile($filename, $spreadsheet)
    {
        // dd($spreadsheet);
        $formFields = [
                ['name', 'Name of the Person taking the count'],
                ['affilation', 'Affiliation (NGO/school, etc.)'],
                ['phone', 'Contact Number'],
                ['email', 'E Mail ID'],
                ['team_members', 'Name and/or number of team members, if any'],
                ['photo_link', 'Link to photo records uploaded'],
                ['location', 'Location (Location name, nearest village/town, state)'],
                ['coordinates', 'GPS Coordinates (from phone)'],
                ['date', 'Date, start time and end time'],
                ['altitude', 'Altitude, m'],
                ['distance', 'Total distance covered on trail, km (approx. estimated)'],
                ['weather', 'Weather (sunny, clouded, windy, etc.)'],
                ['comments', 'Comments'],
        ];
        $rowFields = [
                [ 'sl_no', 'Sl #'],
                [ 'common_name', 'Common Name'],
                [ 'scientific_name', 'Binomial Name (if known)'],
                [ 'no_of_individuals', 'No. of individuals'],
                [ 'remarks', 'Remarks (Male/Female/seasonal form, etc.)']
        ];
        foreach ($spreadsheet as $sheet) {
            // echo "$filename<br>";
            //Check if sheet is format 1;

            if (isset($sheet[11][2])) {
                $form = new CountForm();
                $form->filename = $filename;
                $count_row = -1;
                foreach ($sheet as $k => $f) {
                    foreach ($formFields as $ff) {
                        if (str_replace("\n", " ", $sheet[$k][0]) == $ff[1]) {
                            $form->{$ff[0]} = $sheet[$k][2];
                        }
                    }
                    if (str_replace("\n", " ", $sheet[$k][0]) == $rowFields[0][1]) {
                        $count_row = $k;
                    }
                }
                $form->save();
                for ($i = $count_row +1 ; $i < count($sheet); $i++) {
                    $nullFlag = false;
                    foreach ($sheet[$i] as $k=>$ele) {
                        if ($ele != null and $k>0) {
                            $nullFlag = true;
                            break;
                        }
                    }
                    if ($nullFlag) {
                        $form_row = new FormRow();
                        $form_row->count_form_id = $form->id;
                        foreach ($rowFields as $k => $rf) {
                            if($k == 1)
                                $form_row->{$rf[0]} = trim(ucwords(strtolower($sheet[$i][$k])));
                            elseif($k == 2)
                                $form_row->{$rf[0]} = trim(ucfirst(strtolower($sheet[$i][$k])));
                            else
                                $form_row->{$rf[0]} = trim($sheet[$i][$k]);
                        }
                        $form_row->save();
                    }
                }
            }
        }
    }


    public function newFile_0($filename, $spreadsheet)
    {
        // dd($spreadsheet);
        $formFields[1] = [
                11 => ['name', 'Name of the Person taking the count'],
                12 => ['affilation', 'Affiliation (NGO/school, etc.)'],
                13 => ['phone', 'Contact Number'],
                14 => ['email', 'E Mail ID'],
                15 => ['team_members', 'Name and/or number of team members, if any'],
                16 => ['photo_link', 'Link to photo records uploaded'],
                17 => ['location', 'Location (Location name, nearest village/town, state)'],
                20 => ['coordinates', 'GPS Coordinates (from phone)'],
                21 => ['date', 'Date, start time and end time'],
                22 => ['altitude', 'Altitude, m'],
                23 => ['distance', 'Total distance covered on trail, km (approx. estimated)'],
                24 => ['weather', 'Weather (sunny, clouded, windy, etc.)'],
                25 => ['comments', 'Comments'],
        ];
        $formFields[0] = [
                11 => ['name', 'Name of the Person taking the count'],
                12 => ['affilation', 'Affiliation (NGO/school, etc.)'],
                13 => ['phone', 'Contact Number'],
                14 => ['email', 'E Mail ID'],
                15 => ['team_members', 'Name and/or number of team members, if any'],
                16 => ['photo_link', 'Link to photo records uploaded'],
                17 => ['location', 'Location (Location name, nearest village/town, state)'],
                18 => ['coordinates', 'GPS Coordinates (from phone)'],
                19 => ['date', 'Date, start time and end time'],
                20 => ['altitude', 'Altitude, m'],
                21 => ['distance', 'Total distance covered on trail, km (approx. estimated)'],
                22 => ['weather', 'Weather (sunny, clouded, windy, etc.)'],
                23 => ['comments', 'Comments'],
        ];
        $rowFields = [
                [ 'sl_no', 'Sl #'],
                [ 'common_name', 'Common Name'],
                [ 'scientific_name', 'Binomial Name (if known)'],
                [ 'no_of_individuals', 'No. of individuals'],
                [ 'remarks', 'Remarks (Male/Female/seasonal form, etc.)']
        ];
        foreach ($spreadsheet as $sheet) {
            echo "$filename<br>";
            //Check if sheet is format 1;
            $form_type = null;
            foreach ([0, 1] as $type) {
                $correctFlag = true;
                foreach ($sheet as $k => $f) {
                    if (isset($sheet[0])) {
                        if ($k > 10 and $k < 26) {
                            if (isset($formFields[$type][$k])) {
                                if (str_replace("\n", " ", $f[0]) != $formFields[$type][$k][1]) {
                                    $correctFlag = false;
                                    echo "$type) - $k - " .str_replace("\n", " ", $f[0]) . " - " . $formFields[$type][$k][1] . "<br>";
                                }
                            }
                        }
                    } else {
                        $correctFlag = false;
                    }
                }
                if ($correctFlag) {
                    $form_type = $type;
                    break;
                }
            }
            if ($correctFlag) {
                $form = new CountForm();
                $form->filename = $filename;
                foreach ($formFields[$form_type] as $k => $v) {
                    $form->{$v[0]} = $sheet[$k][2];
                }

                echo $form_type . "<br>";

                if ($form_type == 1) {
                    dd($sheet[28]);
                } elseif ($form_type == 0) {
                    dd($sheet[26]);
                }

                dd($form); // have to replace this with save once rows are done
            }
            dd("++".$correctFlag);
        }
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
     * @param  \App\Models\CountForm  $countForm
     * @return \Illuminate\Http\Response
     */
    public function show(CountForm $countForm)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\CountForm  $countForm
     * @return \Illuminate\Http\Response
     */
    public function edit(CountForm $countForm)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\CountForm  $countForm
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CountForm $countForm)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\CountForm  $countForm
     * @return \Illuminate\Http\Response
     */
    public function destroy(CountForm $countForm)
    {
        //
    }
}

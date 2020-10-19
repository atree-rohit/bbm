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
        // $forms = CountForm::where("duplicate", "=", false)->with("rows")->withCount("rows")->get();
        // $forms_raw = CountForm::with("rows")->withCount("rows")->get();

        $forms = CountForm::with("rows")
                    ->where("duplicate", "=", false)
                    // ->where("coordinates", "<>", null)
                    // ->where("coordinates", "like", "%'%")
                    ->withCount("rows")
                    ->get()
                    ->toArray();

        $formFields = [
            ['id', 'ID'],
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
            ['filename', 'File Name'],
            ['duplicate', 'Duplicate'],
        ];

        $row_types = ["num" => 0, "gt" => 0, "plus" => 0, "range" => 0,"non" => 0];

        foreach ($forms as $k => $f) {
            $sum = 0;
            $non_int_rows = 0;
            foreach ($f["rows"] as $r) {
                if ($r["id_quality"] != "flag") {
                    $no = trim($r["no_of_individuals"]);
                    if (is_numeric($no)) {
                        $sum += $no;
                        $row_types["num"]++;
                    } elseif (substr($no, 0, 1) == ">") {
                        if (is_numeric(trim(substr($no, 1)))) {
                            $sum += trim(substr($no, 1));
                        } else {
                            dd(trim(substr($no, 0, -1)));
                        }
                        $row_types["gt"]++;
                    } elseif (substr($no, -1) == "+") {
                        if (is_numeric(trim(substr($no, 0, -1)))) {
                            $sum += trim(substr($no, 0, -1));
                        } else {
                            dd(trim(substr($no, 0, -1)));
                        }
                        $row_types["plus"]++;
                    } elseif (strpos($no, "-")) {
                        $range = explode("-", $no);
                        // dd($range);
                        if ((isset($range[1])) and (is_numeric(trim($range[0]))) and (is_numeric(trim($range[1])))) {
                            $sum += trim($range[0]) + trim($range[1]);
                            $row_types["range"]++;
                        } else {
                            $non_int_rows++;
                            $row_types["non"]++;
                        }
                    } else {
                        if (strlen($no) > 0) {
                            $sum += 1;
                            $row_types["num"]++;
                        } else {
                            $non_int_rows++;
                            $row_types["non"]++;
                        }
                    }
                }
            }
            $forms[$k]["total_butterflies"] = $sum;
            $forms[$k]["non_int"] = $non_int_rows;
        }

        print_r($row_types);

        return view('butterfly_count.index', compact('forms', 'formFields'));
    }
    public function import()
    {
        $uploaded_forms = CountForm::all()->pluck("filename")->toArray();

        $folder = storage_path("app/public/count_sheets");
        $files = scandir($folder);

        $addedCount = [
            "forms" => ["added" => 0, "skipped" => 0],
            "rows" => ["added" => 0, "skipped" => 0]
        ];

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
                        $added = $this->newFile($f, $raw_file);
                        $addedCount["forms"]["added"] += $added["forms"]["added"];
                        $addedCount["forms"]["skipped"] += $added["forms"]["skipped"];
                        $addedCount["rows"]["added"] += $added["rows"]["added"];
                        $addedCount["rows"]["skipped"] += $added["rows"]["skipped"];
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

        dd($addedCount);
    }

    public function newFile($filename, $spreadsheet)
    {
        $count = [
            "forms" => ["added" => 0, "skipped" => 0],
            "rows" => ["added" => 0, "skipped" => 0]
        ];
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
                $count["forms"]["added"]++;
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
                            if ($k == 1) {
                                $form_row->{$rf[0]} = trim(ucwords(strtolower($sheet[$i][$k])));
                            } elseif ($k == 2) {
                                $form_row->{$rf[0]} = trim(ucfirst(strtolower($sheet[$i][$k])));
                            } else {
                                $form_row->{$rf[0]} = trim($sheet[$i][$k]);
                            }
                        }
                        $form_row->save();
                        $count["rows"]["added"]++;
                    } else {
                        $count["rows"]["skipped"]++;
                    }
                }
            } else {
                $count["forms"]["skipped"]++;
            }
        }

        return $count;
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
    public function update(Request $request, $id)
    {
        $form = CountForm::find($id);
        $fields = ["name", "affilation", "phone", "email", "team_members", "photo_link", "location", "coordinates", "date", "altitude", "distance", "weather", "comments", "filename"];
        foreach ($fields as $f) {
            $form->$f = $request->$f;
        }
        if ($request->duplicate == "on") {
            $form->duplicate = true;
        }

        $form->save();

        return redirect()->back();
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

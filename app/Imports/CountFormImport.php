<?php

namespace App\Imports;

use App\CountForm;
use Maatwebsite\Excel\Concerns\ToModel;

class CountFormImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new CountForm([
            //
        ]);
    }
}

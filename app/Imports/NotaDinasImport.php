<?php

namespace App\Imports;

use App\Models\NotaDinas;
use Maatwebsite\Excel\Concerns\ToModel;

class NotaDinasImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new NotaDinas([
            //
        ]);
    }
}

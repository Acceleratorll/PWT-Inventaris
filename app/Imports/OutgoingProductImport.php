<?php

namespace App\Imports;

use App\Models\OutgoingProduct;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class OutgoingProductImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return new OutgoingProduct([
            //
        ]);
    }
}

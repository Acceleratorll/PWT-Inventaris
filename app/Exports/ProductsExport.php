<?php

namespace App\Exports;

use App\Models\Product;
use Illuminate\Contracts\Support\Responsable;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Excel;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProductsExport implements FromCollection, Responsable, WithHeadings, WithColumnWidths, ShouldAutoSize, WithStyles
{
    use Exportable;

    private $fileName = 'products.xlsx';

    private $writerType = Excel::XLSX;

    private $headers = [
        'Content-Type' => 'text/csv',
    ];

    public function collection()
    {
        $products = Product::with('material', 'product_type', 'qualifier', 'category_product')
            ->select(
                'id',
                'name',
                'product_code',
                'amount',
                'max_amount',
                'note',
                'material_id', // Include the foreign key columns
                'product_type_id',
                'qualifier_id',
                'category_product_id'
            )
            ->get();

        // Now you can iterate through the products and include related data
        $data = [];

        foreach ($products as $product) {
            $data[] = [
                'ID' => $product->id,
                'Name' => $product->name,
                'Product Code' => $product->product_code,
                'Amount' => $product->amount,
                'Max Amount' => $product->max_amount,
                'Note' => $product->note,
                'Material' => $product->material->name,
                'Type' => $product->product_type->name,
                'Qualifier' => $product->qualifier->name,
                'Category' => $product->category_product->name,
            ];
        }

        return collect($data);
    }

    public function headings(): array
    {
        return ['ID', 'Name', 'Product Code', 'Amount', 'Max Amount', 'Note', 'Material', 'Type', 'Qualifier', 'Category'];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1    => [
                'font' => ['bold' => true],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                ],
            ],
            'A'  => [
                'font' => ['bold' => true],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                ],
            ],
            'C'  => [
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                ],
            ],
            'D'  => [
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                ],
            ],
            'E'  => [
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                ],
            ],
            'G'  => [
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                ],
            ],
            'H'  => [
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                ],
            ],
            'I'  => [
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                ],
            ],
            'J'  => [
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                ],
            ],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5.0,
            'B' => 33.0,
            'C' => 13.0,
            'D' => 13.0,
            'E' => 13.0,
            'F' => 60.0,
            'G' => 13.0,
            'H' => 13.0,
            'I' => 13.0,
            'J' => 13.0,
        ];
    }
}

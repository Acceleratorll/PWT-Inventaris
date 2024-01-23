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
        $products = Product::with(
            'material',
            'product_type',
            'qualifier',
            'category_product',
            'outgoing_products',
            'product_locations',
            'product_transactions',
            'product_plannings',
        )->get();

        $data = [];

        foreach ($products as $product) {
            $data[] = [
                'ID' => $product->id,
                'Nama' => $product->name,
                'Bahan' => $product->material->name,
                'Tipe Barang' => $product->product_type->name,
                'Kategori' => $product->category_product->name,
                'Kode' => $product->product_code,
                'Satuan' => $product->qualifier->name,
                'Stock' => $product->total_amount,
                'Stock Minimal' => $product->minimal_amount,
                'Diubah' => $product->updated_at->format('D, d-m-y, G:i'),
                'Dibuat' => $product->created_at->format('D, d-m-y, G:i'),
                'keterangan' => $product->note,
            ];
        }

        return collect($data);
    }

    public function headings(): array
    {
        return ['ID', 'Nama', 'Bahan', 'Tipe Barang', 'Kategori', 'Kode', 'Satuan', 'Stock', 'Stock Minimal', 'Diubah', 'Dibuat', 'Keterangan'];
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

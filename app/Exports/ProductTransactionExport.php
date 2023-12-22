<?php

namespace App\Exports;

use Illuminate\Contracts\Support\Responsable;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProductTransactionExport implements FromCollection, Responsable, WithHeadings, WithColumnWidths, ShouldAutoSize, WithStyles
{
    use Exportable;

    protected $productTransaction;

    public function __construct(Collection $productTransaction)
    {
        $this->productTransaction = $productTransaction;
    }

    public function collection()
    {
        return $this->productTransaction->map(function ($productTransaction) {
            return [
                'Supplier' => $productTransaction->supplier->name,
                'Code' => $productTransaction->code,
                'Purchase Date' => $productTransaction->purchase_date,
                'Incoming Products' => $this->formatIncomingProducts($productTransaction->product_transactions),
                'Description' => $productTransaction->desc,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Supplier',
            'Code',
            'Purchase Date',
            'Incoming Products',
            'Description',
        ];
    }

    protected function formatIncomingProducts($incomingProducts)
    {
        $formattedIncomingProducts = $incomingProducts->map(function ($incomingProduct) {
            $productName = $incomingProduct->product->name;
            $qualifierAbb = $incomingProduct->product->qualifier->abbreviation;
            $qty = $incomingProduct->qty;

            return "{$productName} [Qty: {$qty} {$qualifierAbb}]";
        })->toArray();

        return implode(', ', $formattedIncomingProducts);
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
            'B'  => [
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
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 33.0,
            'B' => 33.0,
            'C' => 35.0,
            'D' => 130.0,
            'E' => 95.0,
        ];
    }
}

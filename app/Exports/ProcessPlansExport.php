<?php

namespace App\Exports;

use App\Models\ProcessPlan;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProcessPlansExport implements FromCollection, Responsable, WithHeadings, WithColumnWidths, ShouldAutoSize, WithStyles
{
    use Exportable;

    protected $processPlans;

    public function __construct(Collection $processPlans)
    {
        $this->processPlans = $processPlans;
    }

    public function collection()
    {
        return $this->processPlans->map(function ($processPlan) {
            return [
                'Customer' => $processPlan->customer,
                'Code' => $processPlan->code,
                'Order Type' => $processPlan->order_type,
                'Outgoing Products' => $this->formatOutgoingProducts($processPlan->outgoing_products),
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Customer',
            'Code',
            'Order Type',
            'Outgoing Products',
        ];
    }

    protected function formatOutgoingProducts($outgoingProducts)
    {
        // Convert the collection of outgoing products to an array of strings
        $formattedOutgoingProducts = $outgoingProducts->map(function ($outgoingProduct) {
            // Assuming 'product' is the relationship to the Product model
            $productName = $outgoingProduct->product->name;
            $qualifierAbb = $outgoingProduct->product->qualifier->abbreviation;
            $qty = $outgoingProduct->qty;

            return "{$productName} [Qty: {$qty} {$qualifierAbb}]";
        })->toArray();

        // Implode the array into a comma-separated string
        return implode(', ', $formattedOutgoingProducts);
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
            'C' => 55.0,
            'D' => 130.0,
        ];
    }
}

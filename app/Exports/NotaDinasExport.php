<?php

namespace App\Exports;

use App\Models\NotaDinas;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class NotaDinasExport implements FromCollection, Responsable, WithHeadings, WithColumnWidths, ShouldAutoSize, WithStyles
{
    use Exportable;

    protected $notaDinas;

    public function __construct(Collection $notaDinas)
    {
        $this->notaDinas = $notaDinas;
    }

    public function collection()
    {
        return $this->notaDinas->map(function ($item) {
            return [
                'Code' => $item->code,
                'Authorized' => strval($item->authorized),
                'From' => $item->from_date,
                'To' => $item->to_date,
                'Product Planned' => $this->formatProductPlannings($item->product_plannings),
                'Description' => $item->desc,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Code',
            'Authorized',
            'From',
            'To',
            'Product Planned',
            'Description',
        ];
    }

    protected function formatProductPlannings($productPlannings)
    {
        $formattedProductPlannings = $productPlannings->map(function ($productPlanning) {
            $productName = $productPlanning->product->name;
            $qualifierAbb = $productPlanning->product->qualifier->abbreviation;
            $qty = $productPlanning->requirement_amount;
            $product_amount = $productPlanning->product_amount;
            $procurement = $productPlanning->procurement_plan_amount;

            return "{$productName} [Requirement: {$qty} {$qualifierAbb}] [Saldo: {$product_amount} {$qualifierAbb}] [Procurement: {$procurement}]";
        })->toArray();

        return implode(', ', $formattedProductPlannings);
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
            'C' => 33.0,
            'D' => 33.0,
            'E' => 33.0,
        ];
    }
}

<?php

namespace App\Exports;

use App\Models\NotaDinas;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class NotaDinasExport implements FromCollection
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
                'Customer' => $item->customer->name,
                'Code' => $item->code,
                'Order Type' => $item->order_type->name,
                'Outgoing Products' => $this->formatOutgoingProducts($item->outgoing_products),
                'Description' => $item->desc,
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
            'Description',
        ];
    }

    protected function formatOutgoingProducts($outgoingProducts)
    {
        $formattedOutgoingProducts = $outgoingProducts->map(function ($outgoingProduct) {
            $productName = $outgoingProduct->product->name;
            $qualifierAbb = $outgoingProduct->product->qualifier->abbreviation;
            $qty = $outgoingProduct->amount;
            $product_amount = $outgoingProduct->product_amount;
            $expired = $outgoingProduct->expired;

            return "{$productName} [Amount: {$qty} {$qualifierAbb}] [Saldo Awal: {$product_amount} {$qualifierAbb}] [Expired: {$expired}]";
        })->toArray();

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
            'C' => 33.0,
            'D' => 130.0,
            'E' => 95.0,
        ];
    }
}

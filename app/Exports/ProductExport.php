<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ProductExport implements FromCollection, WithHeadings
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
// dd($this->data);
return collect($this->data)->map(function ($item) {
            // dd($item['stock_management'] );
            // dump($item->stockManagement['our_stock']);
            return [
                'SKU' => $item['sku'] ?? null,
                'Our Stock' => isset($item->stockManagement['our_stock']) ? (int)$item->stockManagement['our_stock'] : null,
                'Available Stock' => isset($item->stockManagement['available_stock']) ? (int)$item->stockManagement['available_stock'] : null,
                'Minimum Stock' => isset($item->stockManagement['minimum_stock']) ? (int)$item->stockManagement['minimum_stock'] : null,
                'Maximum Stock' => isset($item->stockManagement['maximum_stock']) ? (int)$item->stockManagement['maximum_stock'] : null,

                'Profit Multiplication Our Stock' => isset($item['profit_multiplication']['our_stock']) ? $item['profit_multiplication']['our_stock'] : null,
                'Buying Multiplication Our Stock' => isset($item['buying_multiplication']['our_stock']) ? $item['buying_multiplication']['our_stock'] : null,
                'Selling Multiplication Our Stock' => isset($item['selling_multiplication']['our_stock']) ? $item['selling_multiplication']['our_stock'] : null,
                // 'Profit Multiplication Our Stock' => isset($item['profit_multiplication']['our_stock']) ? $item['profit_multiplication']['our_stock'] : null,
            ];
        });
    }
    public function headings(): array
    {
        return [
            'SKU',
            'Our Stock',
            'Available Stock',
            'Minimum Stock',
            'Maximum Stock',
            'Profit Multiplication Our Stock',
            'Buying Multiplication Our Stock',
            'Selling Multiplication Our Stock',

        ];
    }
}

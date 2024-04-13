<?php

namespace App\Imports;

use App\Models\PartNumber;
use App\Models\PriceCalculation;
use App\Models\Product;
use App\Models\StockManagement;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ProductImport implements ToCollection, WithValidation, WithHeadingRow
{
    
    /**
     * @param Collection $collection
     */
    public function collection(Collection $rows)
    {
       
        foreach ($rows as $row) {
            // Find or create the product
            $product = Product::updateOrCreate(
                ['sku' => $row['sku']], 
                ['title' => $row['title']]
            );
        
            // Create or update PartNumber
            $partNumbers = explode(',', $row['part_numbers']);

            // Loop through each part number and associate it with the product
            foreach ($partNumbers as $partNumber) {
                // Create or update PartNumber
                PartNumber::updateOrCreate(
                    [
                        'product_id' => $product->id,
                        'part_numbers' => $partNumber
                    ],
                   
                );
            }
            $maximum_stock = $row['minimum_stock'] * $row['spare_stock'];
            $minStockRequired = max(0, $row['minimum_stock'] - $row['available_stock']);
            $maxStockRequired = max(0, $maximum_stock - $row['available_stock']);
            $ourStock = max(0, $row['available_stock'] - $maximum_stock);
            // Create or update StockManagement
            $stockManagement = StockManagement::updateOrCreate(
                ['product_id' => $product->id],
                [
                    'available_stock' => $row['available_stock'],
                    'dropdown' => $row['dropdown'],
                    'minimum_stock' => $row['minimum_stock'],
                    'spare_stock' => $row['spare_stock'],
                    'maximum_stock' => $maximum_stock,
                    'our_stock' => max(0, $ourStock),
                    'minimum_stock_required' => max(0, $minStockRequired),
                    'maximum_stock_required' => max(0, $maxStockRequired)
                ]
            );
        
            // Create or update PriceCalculation

              // Perform price calculation
              $data = [
                'buying' => $row['buying'],
                'selling' => $row['selling'],
                'add_rate' => 5.5,
                'catigory_rate' => 5.5,
                'value_fee' => 0.3,
                'postage' => $row['postage'],
            ];

            // Calculate add_rate_ans based on selling and add_rate
            $addRatePercentage = $data['add_rate'] / 100;
            $addRateAns = $data['selling'] * $addRatePercentage;
            $data['add_rate_ans'] = $addRateAns;

            // Calculate add_rate_gst based on add_rate_ans
            $data['add_rate_gst'] = $data['add_rate_ans'] / 10;

            // Calculate total_add_rate based on add_rate_ans and selling
            $data['total_add_rate'] = $data['add_rate_ans'] + $data['add_rate_gst'];

            // Calculate catigory_rate_ans based on selling and catigory_rate
            $catigoryAddRatePercentage = $data['catigory_rate'] / 100;
            $catigoryAddRateAns = $data['selling'] * $catigoryAddRatePercentage;
            $data['catigory_rate_ans'] = $catigoryAddRateAns;

            // Calculate catigory_rate_gst based on catigory_rate_ans and value_fee
            $data['catigory_rate_gst'] = ($data['catigory_rate_ans'] + $data['value_fee']) / 10;

            // Calculate catigory_add_rate based on catigory_rate_ans, catigory_rate_gst, and value_fee
            $data['catigory_add_rate'] = $data['catigory_rate_ans'] + $data['catigory_rate_gst'] + $data['value_fee'];

            // Calculate ebay_expenses based on catigory_add_rate and total_add_rate
            $data['ebay_expenses'] = $data['catigory_add_rate'] + $data['total_add_rate'];

            // Calculate earning_from_ebay based on selling and ebay_expenses
            $data['earning_from_ebay'] = $data['selling'] - $data['ebay_expenses'];

            // Calculate gst_on_earning based on earning_from_ebay
            $data['gst_on_earning'] = $data['earning_from_ebay'] / 10;

            // Calculate earning_in_hand based on earning_from_ebay and gst_on_earning
            $data['earning_in_hand'] = $data['earning_from_ebay'] - $data['gst_on_earning'];

            // Calculate total_cost based on buying, postage, ebay_expenses, and gst_on_earning
            $data['total_cost'] = $data['buying'] + $data['postage'] + $data['ebay_expenses'] + $data['gst_on_earning'];

            // Calculate profit based on selling and total_cost
            $data['profit'] = $data['selling'] - $data['total_cost'];

            // Calculate profit_margin based on profit and buying
            $data['profit_margin'] = ($data['profit'] / $data['buying']) * 100;

            // Update or create price calculation
            // Assuming $priceCalculation is your model instance
            $priceCalculation = PriceCalculation::updateOrCreate(
                ['product_id' => $product->id], // Assuming you have product_id in $row array
                $data
            // $priceCalculation = PriceCalculation::updateOrCreate(
            //     ['product_id' => $product->id],
            //     [
            //         'buying' => $row['buying'],
            //         'selling' => $row['selling'],
            //         'add_rate' => $row['add_rate'],
            //         'catigory_rate' => $row['catigory_rate'],
            //         'value_fee' => $row['value_fee'],
            //         'postage' => $row['postage'],
            //     ]
             );
        }
        
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            // Define validation rules for your import
        ];
    }
}

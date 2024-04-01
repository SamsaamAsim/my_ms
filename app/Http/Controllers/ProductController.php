<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\PartNumber;
use App\Models\Competitor;
use Illuminate\Support\Facades\Log;

use App\Models\Image;
use App\Models\PriceCalculation;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::all();
        return response()->json([
            'data' => $products, 
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }
    public function searchByPartNumber(Request $request)
    {
        $partNumber = $request->input('part_numbers');
    
        // Search for products by part number
        $products = Product::with('partNumber')->whereHas('partNumber', function ($query) use ($partNumber) {
            $query->where('part_numbers', 'like', "%$partNumber%");
        })->get();
    
        return response()->json([
            'data' => $products,
        ]);
    }
    public function searchBySKU(Request $request)
{
    $sku = $request->input('sku');

    // Search for products by SKU
    $products = Product::where('sku', 'like', "%$sku%")->get();

    return response()->json([
        'data' => $products,
    ]);
}
    public function searchByOurStock(Request $request)
    {
        $ourStock = $request->input('our_stock');
    
        // Search for products by our_stock
        $products = Product::with('stockManagement')
            ->whereHas('stockManagement', function ($query) use ($ourStock) {
                $query->whereNotNull('our_stock')
                    ->where('our_stock', $ourStock);
            })
            ->get(); // Execute the query and fetch the results
    
        return response()->json([
            'data' => $products,
        ]);
    }
    
    
    public function forallShowStockManagement()
    {
        $products = Product::whereHas('stockManagement', function ($query) {
            $query->where('our_stock', '!=', 0);
        })->with(['stockManagement', 'priceCalculation'])->get();
        
        // Check if price calculation exists for any product
        if ($products->isEmpty() || $products->contains(function ($product) {
            return $product->priceCalculation === null;
        })) {
            return response()->json([
                'error' => 'Price calculation is missing for one or more products.'
            ], 404);
        }
        
        // Initialize the modified data array
        $modifiedData = [];
    
        foreach ($products as $product) {
            // Initialize arrays for storing the multiplication results
            $buyingMultiplication = [];
            $sellingMultiplication = [];
            $profitMultiplication = [];
            
            // Define the fields to be multiplied by the selling, buying, and profit prices
            $fieldsToMultiply = [
                'available_stock',
                'minimum_stock',
                'maximum_stock',
                'spare_stock',
                'minimum_stock_required',
                'maximum_stock_required',
                'our_stock'
            ];
            
            foreach ($fieldsToMultiply as $field) {
                if (isset($product->stockManagement->$field)) {
                    // Perform multiplication and format to 2 decimal points
                    $buyingMultiplication[$field] = number_format($product->stockManagement->$field * $product->priceCalculation->buying, 2);
                    $sellingMultiplication[$field] = number_format($product->stockManagement->$field * $product->priceCalculation->selling, 2);
                    $profitMultiplication[$field] = number_format($product->stockManagement->$field * $product->priceCalculation->profit, 2);
                }
            }
    
            // Add the multiplication results to the modified data array
            $modifiedData[$product->id] = [
                'product' => $product,
                'buying_multiplication' => $buyingMultiplication,
                'selling_multiplication' => $sellingMultiplication,
                'profit_multiplication' => $profitMultiplication,
            ];
        }
    
        return response()->json([
            'data' => $products,
            'modified_data' => $modifiedData,
        ]);
    }
    
    
    

    public function showStockManagement(Product $product, int $id)
    {
        // Retrieve the product with its associated stock management and price calculation
        $product = Product::with(['stockManagement', 'priceCalculation'])->findOrFail($id);
        
        // Get the price calculation for the product
        $priceCalculation = $product->priceCalculation;
    
        // Check if price calculation exists
        if ($priceCalculation) {
            // Initialize the modified data array
            $modifiedData = $product->toArray();
            
            // Initialize arrays for storing the multiplication results
            $buyingMultiplication = [];
            $sellingMultiplication = [];
            $profitMultiplication = [];
            
            // Define the fields to be multiplied by the selling, buying, and profit prices
            $fieldsToMultiply = [
                'available_stock',
                'minimum_stock',
                'maximum_stock',
                'spare_stock',
                'minimum_stock_required',
                'maximum_stock_required',
                'our_stock'
            ];
            
            // Multiply the specified fields by the selling, buying, and profit prices
            foreach ($fieldsToMultiply as $field) {
                if (isset($modifiedData['stock_management'][$field])) {
                    $buyingMultiplication[$field] = number_format($modifiedData['stock_management'][$field] * $priceCalculation->buying, 2);
                    $sellingMultiplication[$field] = number_format($modifiedData['stock_management'][$field] * $priceCalculation->selling, 2);
                    $profitMultiplication[$field] = number_format($modifiedData['stock_management'][$field] * $priceCalculation->profit, 2);
                }
            }
            
            // Add the multiplication results to the modified data array
            $modifiedData['buying_multiplication'] = $buyingMultiplication;
            $modifiedData['selling_multiplication'] = $sellingMultiplication;
            $modifiedData['profit_multiplication'] = $profitMultiplication;
            
            // Return the original and modified data in a JSON response
            return response()->json([
                'original_data' => $product,
                'modified_data' => $modifiedData,
            ]);
        } else {
            // Handle the case where price calculation is null
            return response()->json([
                'error' => 'Price calculation is missing for the product.'
            ], 404);
        }
    }
    
    
    
    

    
    public function showStockManagementAdditional(Product $product, int $id)
    {
        // Retrieve the product and related price calculation and stock management
        $product = Product::with(['priceCalculation', 'stockManagement'])->findOrFail($id);
    
        // Get the price calculation for the product
        $priceCalculation = $product->priceCalculation;
        
        // Get the stock management data for the product
        $stockManagement = $product->stockManagement;
    
        // Initialize the modified data array
        $modifiedData = [];
    
        // Check if both price calculation and stock management data exist
        if ($priceCalculation && $stockManagement) {
            // Multiply buying price by the corresponding stock management value
            $modifiedData['modified_buying_price'] = $priceCalculation->buying * $stockManagement->value;
    
            // Multiply selling price by the corresponding stock management value
            $modifiedData['modified_selling_price'] = $priceCalculation->selling * $stockManagement->value;
    
            // Multiply profit by the corresponding stock management value
            $modifiedData['modified_profit'] = $priceCalculation->profit * $stockManagement->value;
        }
    
        // Include stock management data in the modified data
        $modifiedData['stock_management'] = $stockManagement;
    
        // Return the modified data in JSON response
        return response()->json([
            'modified_data' => $modifiedData,
        ]);
    }
    
    
    public function storeOrUpdateStockManagement(Request $request, int $productId)
{
    try {
        // Find the associated product
        $product = Product::findOrFail($productId);

        // Retrieve data from the request
        $data = $request->only([
            'available_stock',
            'minimum_stock',
            'dropdown',
            'spare_stock',
            'maximum_stock', // Include 'maximum_stock' in the list of fields
        ]);

        // Calculate additional fields
        $data['maximum_stock']= $data['minimum_stock'] * $data['spare_stock'];
        $minStockRequired = max(0, $data['minimum_stock'] - $data['available_stock']);
        $maxStockRequired = max(0, $data['maximum_stock'] - $data['available_stock']);
        $ourStock = max(0, $data['available_stock'] - $data['maximum_stock']);

        // Assign calculated values to data array
        $data['our_stock'] =max(0, $ourStock);
        $data['minimum_stock_required'] = max(0, $minStockRequired); // Ensure minimum_stock_required is not negative
        $data['maximum_stock_required'] = max(0, $maxStockRequired); // Ensure maximum_stock_required is not negative

        // Check if stock management entry already exists
        $stockManagement = $product->stockManagement;

        if ($stockManagement) {
            // Update the existing stock management entry
            $stockManagement->update($data);
            $message = 'Stock management updated successfully';
        } else {
            // Create a new stock management entry
            $stockManagement = $product->stockManagement()->create($data);
            $message = 'Stock management created successfully';
        }

        return response()->json(['message' => $message, 'data' => $stockManagement], 200);
    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        return response()->json(['error' => 'Product not found'], 404);
    } catch (\Exception $e) {
        Log::error('Error storing or updating stock management: ' . $e->getMessage());
        return response()->json(['error' => 'Error storing or updating stock management', 'message' => $e->getMessage()], 500);
    }
}
    
    



    public function showPriceCalculation(Product $product,int $id)
    {
        {
            $products = Product::with(['priceCalculation'])->find($id);
            return response()->json([
                'data' => $products,
            ]);
        }
    
        
    }

    public function storeOrUpdate(Request $request, int $productId)
    {
        try {
            // Find the associated product
            $product = Product::findOrFail($productId);

            // Check if a price calculation already exists for the product
            $priceCalculation = $product->priceCalculation;

            if ($priceCalculation) {
                // Update the existing price calculation

                $data = $request->only([
                    
                    'buying',
                    'selling',
                    'add_rate',
                    'catigory_rate',
                    'value_fee',
                    'postage',
                                  
                ]);
             
$addRatePercentage = $data['add_rate'] / 100;
$addRateAns = $data['selling'] * $addRatePercentage;
$data['add_rate_ans'] = $addRateAns;

// Calculate add_rate_gst based on add_rate_ans
$data['add_rate_gst'] = $data['add_rate_ans'] / 10;

// Calculate total_add_rate based on add_rate_ans and selling
// $data['total_add_rate'] = $addRatePercentage * $data['selling'] + $data['add_rate_ans'] / 10;
 $data['total_add_rate'] = $data['add_rate_ans']+$data['add_rate_gst'] ;

// Calculate catigory_add_rate based on selling and catigory_rate_ans
$catigoryAddRatePercentage = $data['catigory_rate'] / 100;
$catigoryAddRateAns = $data['selling'] * $catigoryAddRatePercentage;
$data['catigory_rate_ans'] = $catigoryAddRateAns;

// Calculate catigory_rate_gst based on catigory_rate_ans
$data['catigory_rate_gst'] = ($data['catigory_rate_ans']+$data['value_fee']) / 10;

// Calculate catigory_add_rate based on selling and catigory_rate_ans
// $data['catigory_add_rate'] = $catigoryAddRatePercentage * $data['selling'] + $data['catigory_rate_ans'] / 10;
$data['catigory_add_rate'] = $data['catigory_rate_ans'] +$data['catigory_rate_gst']+$data['value_fee'];

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
$data['profit_margin'] = ($data['profit'] / $data['buying'] )* 100;

                $priceCalculation->update($data);
                $message = 'Price calculation updated successfully';
            } else {
                $data = $request->only([
                    
                    'buying',
                    'selling',
                    'add_rate',
                    'catigory_rate',
                    'value_fee',
                    'postage',
                                  
                ]);
               // Calculate add_rate_ans based on selling and category_rate
               $addRatePercentage = $data['add_rate'] / 100;
               $addRateAns = $data['selling'] * $addRatePercentage;
               $data['add_rate_ans'] = $addRateAns;
               
               // Calculate add_rate_gst based on add_rate_ans
               $data['add_rate_gst'] = $data['add_rate_ans'] / 10;
               
               // Calculate total_add_rate based on add_rate_ans and selling
               // $data['total_add_rate'] = $addRatePercentage * $data['selling'] + $data['add_rate_ans'] / 10;
                $data['total_add_rate'] = $data['add_rate_ans']+$data['add_rate_gst'] ;
               
               // Calculate catigory_add_rate based on selling and catigory_rate_ans
               $catigoryAddRatePercentage = $data['catigory_rate'] / 100;
               $catigoryAddRateAns = $data['selling'] * $catigoryAddRatePercentage;
               $data['catigory_rate_ans'] = $catigoryAddRateAns;
               
               // Calculate catigory_rate_gst based on catigory_rate_ans
               $data['catigory_rate_gst'] = ($data['catigory_rate_ans']+$data['value_fee']) / 10;
               
               // Calculate catigory_add_rate based on selling and catigory_rate_ans
               // $data['catigory_add_rate'] = $catigoryAddRatePercentage * $data['selling'] + $data['catigory_rate_ans'] / 10;
               $data['catigory_add_rate'] = $data['catigory_rate_ans'] +$data['catigory_rate_gst']+$data['value_fee'];
               
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
               $data['profit_margin'] = ($data['profit'] / $data['buying'] )* 100;

                // Create a new price calculation
                $priceCalculation = $product->priceCalculation()->create($data);
                $message = 'Price calculation created successfully';
            }

            return response()->json(['message' => $message, 'data' => $priceCalculation], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => 'Product not found'], 404);
        } catch (\Exception $e) {
            Log::error('Error storing or updating price calculation: ' . $e->getMessage());
            return response()->json(['error' => 'Error storing or updating price calculation'], 500);
        }
    }
    

    




    public function showCompetitors(Product $product,int $id)
{
    {
         // Retrieve the product along with its competitors
    $productWithCompetitors = Product::with(['competitor'])->find($id);
    
    // Count the total number of competitors
    $totalCompetitors = $productWithCompetitors->competitor->count();
    
    // Add the total number of competitors to the product data
    $productData = $productWithCompetitors->toArray();
    $productData['total_competitors'] = $totalCompetitors;
    
    return response()->json([
        'data' => $productData,
    ]);
    }

    
}

public function storeCompetitor(Request $request, int $id)
{
    try {
        // Find the associated product
        $product = Product::with(['Competitor', 'PriceCalculation'])->find($id);

        // Fetch our pricing details
        $ourPricing = $product->priceCalculation;

        // Ensure our pricing exists
        if (!$ourPricing) {
            return response()->json(['error' => 'Our pricing not found'], 404);
        }

        // Calculate total cost and profit margin for our pricing
        

        // Extract competitor data from the request
        $postage = $request->input('postage');
        $postage = ($postage !== null) ? $postage : 0;
        
        $data = [
            'competitors_name' => $request->input('competitors_name'),
            'dropdown' => $request->input('dropdown'),
            'competitor_selling_price' => $request->input('competitor_selling_price'),
            'postage' => $postage, // Assign the conditioned value of $postage
            'product_id' => $id, // Use the product's ID from the URL parameter
            '30_day_sale' => $request->input('30_day_sale'),
            '90_day_sale' => $request->input('90_day_sale'),
            '6_month_sale' => $request->input('6_month_sale'),
            '1_year_sale' => $request->input('1_year_sale'),
            '3_year_sale' => $request->input('3_year_sale'),
        ];
        

        // Calculate total cost and profit margin for the competitor based on our pricing
        $data['total'] = $data['competitor_selling_price'] + $data['postage'];
        $data['ourless'] = $data['total']  - $ourPricing->selling;


        
        $addRatePercentageCom = $ourPricing->add_rate / 100;
        $addRateAnsCompetitor = $data['total'] * $addRatePercentageCom;
        $addRateGestOnConpetitor= $addRateAnsCompetitor/10;
        $totalAddRateCom=$addRateAnsCompetitor+$addRateGestOnConpetitor;

        
        // Calculate total_add_rate based on add_rate_ans and selling
        // $data['total_add_rate'] = $addRatePercentage * $data['selling'] + $data['add_rate_ans'] / 10;
       
        
        // Calculate catigory_add_rate based on selling and catigory_rate_ans
        $catigoryRatePercentageCom =  $ourPricing->catigory_rate/ 100;
        $catigoryRateAnsCometitor = $data['total'] * $catigoryRatePercentageCom;
        
        // Calculate catigory_rate_gst based on catigory_rate_ans
        $catigoryRateGstCometitor = ($catigoryRateAnsCometitor+$ourPricing->value_fee) / 10;
        
      
        $totalCatigorayCompetitor = $catigoryRateAnsCometitor +$catigoryRateGstCometitor+$ourPricing->value_fee;
        
        // Calculate ebay_expenses based on catigory_add_rate and total_add_rate
        $ebayExpensesCom=$totalCatigorayCompetitor+$totalAddRateCom;
        
        // Calculate earning_from_ebay based on selling and ebay_expenses
        $earningOnEbayComp=$data['total']-$ebayExpensesCom;
        
        
        // Calculate gst_on_earning based on earning_from_ebay
        $gstONEaringCom=$earningOnEbayComp/10;
        
        // Calculate earning_in_hand based on earning_from_ebay and gst_on_earning
        $earninginhandComp=$earningOnEbayComp-$gstONEaringCom;
        
        
        // Calculate total_cost based on buying, postage, ebay_expenses, and gst_on_earning
        $totalCostComp = $ourPricing->buying +$ourPricing->postage + $ebayExpensesCom + $gstONEaringCom;







        $data['profit_competitors'] = $data['total']- $totalCostComp;
        $data['profit_margin_competitors'] = $data['profit_competitors'] / $ourPricing->buying * 100;

        // Create a new competitor associated with the product
        $competitor = $product->competitor()->create($data);

        // Return a JSON response with the success message and the created competitor data
        return response()->json([
            'message' => 'Competitor created successfully',
            'competitor' => $competitor,
        ]);
    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        return response()->json(['error' => 'Product not found'], 404);
    } catch (\Exception $e) {
        Log::error('Error storing competitor: ' . $e->getMessage());
        return response()->json(['error' => 'Error storing competitor'], 500);
    }
}

public function updateCompetitor(Request $request, int $productId, int $competitorId)
{
    try {
        // Find the associated competitor
        $competitor = Competitor::find($competitorId);
       

        // If the competitor exists and belongs to the specified product
        if ($competitor && $competitor->product_id === $productId) {
            // Fetch our pricing details
            $ourPricing = PriceCalculation::where('product_id', $productId)->first();
            // Ensure our pricing details exist
            if (!$ourPricing) {
                return response()->json(['error' => 'Our pricing not found for the specified product'], 404);
            }
            
           
            // Extract data from the request
            $data = [
                'competitors_name' => $request->input('competitors_name'),
                'dropdown' => $request->input('dropdown'),
                'competitor_selling_price' => $request->input('competitor_selling_price'),
                'postage' => $request->input('postage'),
                'product_id' => $productId, // Use the product's ID from the URL parameter
                '30_day_sale' => $request->input('30_day_sale'),
                '90_day_sale' => $request->input('90_day_sale'),
                '6_month_sale' => $request->input('6_month_sale'),
                '1_year_sale' => $request->input('1_year_sale'),
                '3_year_sale' => $request->input('3_year_sale'),
            ];
            // Debugging: Check if data is correctly extracted from the request
            // dd($data);
            // Retain existing values from the competitor model for fields with null values
$data['competitor_selling_price'] = $data['competitor_selling_price'] ?? $competitor->competitor_selling_price;
$data['postage'] = $data['postage'] ?? $competitor->postage;
$data['30_day_sale'] = $data['30_day_sale'] ?? $competitor->{'30_day_sale'};
$data['90_day_sale'] = $data['90_day_sale'] ?? $competitor->{'90_day_sale'};
$data['6_month_sale'] = $data['6_month_sale'] ?? $competitor->{'6_month_sale'};
$data['1_year_sale'] = $data['1_year_sale'] ?? $competitor->{'1_year_sale'};


            // Calculate total cost and profit margin for the competitor based on our pricing
            $data['total'] = $data['competitor_selling_price'] + $data['postage'];
        $data['ourless'] = $data['total']  - $ourPricing->selling;


        
        $addRatePercentageCom = $ourPricing->add_rate / 100;
        $addRateAnsCompetitor = $data['total'] * $addRatePercentageCom;
        $addRateGestOnConpetitor= $addRateAnsCompetitor/10;
        $totalAddRateCom=$addRateAnsCompetitor+$addRateGestOnConpetitor;

        
        // Calculate total_add_rate based on add_rate_ans and selling
        // $data['total_add_rate'] = $addRatePercentage * $data['selling'] + $data['add_rate_ans'] / 10;
       
        
        // Calculate catigory_add_rate based on selling and catigory_rate_ans
        $catigoryRatePercentageCom =  $ourPricing->catigory_rate/ 100;
        $catigoryRateAnsCometitor = $data['total'] * $catigoryRatePercentageCom;
        
        // Calculate catigory_rate_gst based on catigory_rate_ans
        $catigoryRateGstCometitor = ($catigoryRateAnsCometitor+$ourPricing->value_fee) / 10;
        
      
        $totalCatigorayCompetitor = $catigoryRateAnsCometitor +$catigoryRateGstCometitor+$ourPricing->value_fee;
        
        // Calculate ebay_expenses based on catigory_add_rate and total_add_rate
        $ebayExpensesCom=$totalCatigorayCompetitor+$totalAddRateCom;
        
        // Calculate earning_from_ebay based on selling and ebay_expenses
        $earningOnEbayComp=$data['total']-$ebayExpensesCom;
        
        
        // Calculate gst_on_earning based on earning_from_ebay
        $gstONEaringCom=$earningOnEbayComp/10;
        
        // Calculate earning_in_hand based on earning_from_ebay and gst_on_earning
        $earninginhandComp=$earningOnEbayComp-$gstONEaringCom;
        
        
        // Calculate total_cost based on buying, postage, ebay_expenses, and gst_on_earning
        $totalCostComp = $ourPricing->buying +$ourPricing->postage + $ebayExpensesCom + $gstONEaringCom;







        $data['profit_competitors'] = $data['total']- $totalCostComp;
        $data['profit_margin_competitors'] = $data['profit_competitors'] / $ourPricing->buying * 100;


            // Debugging: Check if data calculations are correct
            // dd($data);
            // dd($competitor,$data);

            // Update the competitor with the new data
            $competitor->update($data);

            // Debugging: Check the updated competitor data
            // dd($competitor);

            return response()->json(['message' => 'Competitor updated successfully', 'competitor' => $competitor], 200);
        } else {
            return response()->json(['error' => 'Competitor not found or does not belong to the specified product'], 404);
        }
    } catch (\Exception $e) {
        Log::error('Error updating competitor: ' . $e->getMessage());
        return response()->json(['error' => 'Error updating competitor'], 500);
    }
}



    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
{
    $data = [
        'compatible_machine' => $request->input('compatible_machine'),
        'ready_to_sell' => $request->input('ready_to_sell'),
        'sku' => $request->input('sku'),
        'title' => $request->input('title'),
        'additional_info' => $request->input('additional_info'),
        // 'part_numbers' => $request->input('part_numbers'),
        // 'images' => $request->input('images')
    ];

    $newProduct = Product::create($data);

    if ($request->hasFile('images')) {
        foreach ($request->file('images') as $image) {
            $filename = Carbon::now()->timestamp . '_' . $image->getClientOriginalName();
            $path = $image->storeAs('public/images', $filename); // Store the image in storage/app/public/images

            // Create a new image record in the database with the file name
            $newProduct->images()->create([
                'images' => $filename,
            ]);
        }
    }
    if ($request->has('part_numbers')) {
        // dd($request->input('part_numbers'));
        try {
            $partNumbers = explode('||', $request->input('part_numbers'));
            foreach ($partNumbers as $partNumber) {
                // dd( $newProduct->id);
                // dd( $partNumber);

                PartNumber::create([
                    
                    'product_id' => $newProduct->id,
                    'part_numbers' => trim($partNumber), // Corrected the field name
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error inserting part numbers: ' . $e->getMessage());
            return response()->json(['error' => 'Error inserting part numbers'], 500);
        }
    }
    return response()->json(['message' => 'Product created successfully'], 201);
}

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        $products = Product::with(['partNumber'])->find($id);
        return response()->json([
            'data' => $products,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(product $product)
    {
        return view('products.edit', compact('product'));

    }

    public function addpartNmber(Request $request, int $id) {
        try {
            $product = Product::findOrFail($id);
            $partNumber = $request->input('part_numbers');
    
            // Check if part_number is provided and not empty
            if (!isset($partNumber) || empty($partNumber)) {
                return response()->json(['message' => 'Part number is required'], 400);
            }
    
            PartNumber::create([
                'product_id' => $product->id,
                'part_numbers' => $partNumber,
            ]);
    
            return response()->json(['message' => 'Part number added successfully'], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Product not found'], 404);
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json(['message' => 'Failed to add part number'], 500);
        }
    }

   public function update(Request $request, int $id)
{
    // Retrieve the product instance based on the given product ID
    $product = Product::findOrFail($id);

    // Update the product's attributes
    $product->compatible_machine = $request->input('compatible_machine');
    $product->ready_to_sell = $request->input('ready_to_sell');
    $product->sku = $request->input('sku');
    $product->title = $request->input('title');
    $product->additional_info = $request->input('additional_info');

    // Save the changes to the product
    $product->save();

    // Update the specific part number associated with the product
    if ($request->has('part_number')) {
        try {
            $partNumber = $request->input('part_number');
            $newPartNumber = $request->input('new_part_number');
            
            // Find the associated part number
            $associatedPartNumber = $product->partNumber()->where('part_numbers', $partNumber)->first();
            if ($associatedPartNumber) {
                // Update the part number if found
                $associatedPartNumber->part_numbers = $newPartNumber;
                $associatedPartNumber->save();
            } else {
                // Create a new part number if not found
                PartNumber::create([
                    'product_id' => $product->id,
                    'part_numbers' => $newPartNumber,
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error updating part number: ' . $e->getMessage());
            return response()->json(['error' => 'Error updating part number'], 500);
        }
    }

    return response()->json(['message' => 'Product updated successfully'], 200);
}

public function destroy(int $id)
{
    $product = Product::findOrFail($id);
    $product->delete();

    return response()->json([
        'message' => 'Product deleted successfully',
    ]);
}

   
}

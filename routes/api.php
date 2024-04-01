<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\PartNumberController;
use App\Http\Controllers\ProductController;
use App\Http\Middleware\CorsMiddleware;
use App\Models\Competitor;
use App\Models\PriceCalculation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

// Route::controller(AuthController::class)->group(function(){
//     Route::post('login','login');
//     Route::post('register','register');
// });
Route::post('register',[AuthController::class,'register']);
Route::post('login',[AuthController::class,'login']);
Route::resource('products', ProductController::class);
Route::post('products/{productId}/competitors/{competitorId}', [ProductController::class, 'updateCompetitor']);

Route::post('products/{id}', [ProductController::class, 'update']);
Route::post('product/{id}', [ProductController::class, 'addpartNmber']);
Route::post('products/updateCompetitor/{id}',[ProductController::class, 'updateCompetitor']);
Route::post('products/{id}/competitor', [ProductController::class, 'storeCompetitor']);

Route::get('products/cometitor/{id}', [ProductController::class, 'showCompetitors']);


Route::get('product/search', [ProductController::class, 'searchByPartNumber']);
Route::get('product/our_stock/search', [ProductController::class, 'searchByOurStock']);
Route::get('product/searchsku', [ProductController::class, 'searchBySKU']);


Route::delete('part_numbers/{id}', [PartNumberController::class, 'destroy']);
Route::delete('competitor/{id}', [Competitor::class, 'destroy']);
Route::delete('products/delete/{id}', [ProductController::class, 'destroy']);



Route::get('products/price-calculation/{productid}', [ProductController::class, 'showPriceCalculation']);
Route::post('products/price-calculation/{productid}', [ProductController::class, 'storeOrUpdate']);
Route::delete('price-calculation/{id}', [PriceCalculation::class, 'destroy']);

// Route::get('products/search', 'ProductController@searchByPartNumber');
Route::get('all_stock_management', [ProductController::class, 'forallShowStockManagement']);
Route::get('products/stock_management/{productid}', [ProductController::class, 'showStockManagement']);

Route::get('products/stock_management_additional_info/{productid}', [ProductController::class, 'showStockManagementAdditional']);

Route::post('products/stock_managements/{productid}', [ProductController::class, 'storeOrUpdateStockManagement']);
Route::delete('stock_management/{id}', [PriceCalculation::class, 'destroy']);

// Route::prefix('products')->group(function () {
//     Route::post('{id}', [ProductController::class, 'update']);
// });



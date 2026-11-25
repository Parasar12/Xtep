<?php

namespace App\Http\Controllers\Api;

use App\Models\Product;
use App\Models\ProductMaster;
use App\Models\ProductGallary;
use App\ApiResponse\ApiResponse;

use Illuminate\Support\Facades\DB;

use App\Models\OrderDetail;

use Illuminate\Support\Facades\Validator;

use stdClass;

use Illuminate\Http\Request;

use App\Models\FlashSale;
use App\Models\FlashSaleProducts;
use Carbon\Carbon;

class FlashSaleApiController extends Controller{
	
	public function getFlashSales(){
		$flashSales = FlashSale::select('id', 'name', 'start_date', 'end_date')
		->where('is_active', 1)
		->where('end_date', '>=', date('Y-m-d'))
		->get();

		$data = [];
		if(!$flashSales->isEmpty()){

			foreach ($flashSales as $flashSale) {

				$productInfo = [];

				if($flashSale->flashSaleProducts){

					foreach($flashSale->flashSaleProducts as $fp){

						$salesCount = OrderDetail::where('product_id', $fp->product_id)
						->whereIn('delivery_status', [1, 2, 3])
						->whereBetween('created_at', [$flashSale->start_date. ' 00:00:00', $flashSale->end_date.' 23:59:59'])->sum('quantity');
						

						$master = ProductMaster::where('product_id', $fp->product_id)
						->where('is_active', 1)->first();

						$productInfo[] = array(
							'product_info' => getProductDetail($fp->product_id),
							'actual_price' => $master->price,
							'discounted_price' => $fp->price,
							'remaining_qty' => $fp->quantity - $salesCount,
						);
					}
				}
				else{
					return ApiResponse::sendNoDataErrorResponse('No Products found.');
				}

				$data = array(
					'name' => $flashSale->name,
					'start_date' => $flashSale->start_date,
					'end_date' => $flashSale->end_date,
					'detail' => $productInfo,
				);
			}

			return ApiResponse::sendSuccessResponse('Success',$data);
		}
		else{
			return ApiResponse::sendSuccessResponse('Success');
		}
	}
	
}
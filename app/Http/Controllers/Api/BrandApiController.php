<?php

namespace App\Http\Controllers\Api;
use App\Models\Brand;
use App\Models\Product;

use Illuminate\Http\Request;
use App\ApiResponse\ApiResponse;

use Illuminate\Support\Facades\Validator;

class BrandApiController extends Controller{
    
    public function getAllBrands(){
        $brands = Brand::where('is_active',1)->get();
        if(!$brands->isEmpty()){
            $path = url('uploads/brands/');

            foreach($brands as $brand){
                $brand->image = $path.'/'.$brand->image;
            }
            return ApiResponse::sendSuccessResponse('Success',$brands,$path);

        }else{
            return ApiResponse::sendNoDataErrorResponse('No Data found.');
        }
    }

    public function getBrandProducts(Request $request){
         $rules = array(
            'brand_id' => 'required|numeric',
        );

        $messages = array(
            'brand_id.required' => 'brand_id is required.',
        );

        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            $messages = $validator->messages();
            $errors = $messages->all();
            return ApiResponse::sendValidationErrorResponse($errors);
        }

        $brandCheck = Brand::where('id',$request->brand_id)->first();

        if($brandCheck){
            $brandProducts = Brand::find($request->brand_id)->products;
            if($brandProducts->isEmpty()){
                return ApiResponse::sendNoDataErrorResponse('No Data found.');
                
            }else{
                $path = url('uploads/products/');
                foreach ($brandProducts as $brandProduct) {
                    $brandProduct->image = $path.'/'.$brandProduct->image;
                    $brandProduct->price = getProductPrice($brandProduct->id);
                }
                return ApiResponse::sendSuccessResponse('Success',$brandProducts,$path);
            }
        }else{
            return ApiResponse::sendNoDataErrorResponse('No Data found.');
        }
    }

    
}
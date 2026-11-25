<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ShippingMethod;
use Illuminate\Support\Facades\Validator;
use App\ApiResponse\ApiResponse;

class ShippingMethodsApiController extends Controller{

    public function index(){
        $shippingMethods = ShippingMethod::select('id', 'duration', 'cost', 'title')->where('is_active',1)->get();
        if(!$shippingMethods->isEmpty()){
            return ApiResponse::sendSuccessResponse('Success',$shippingMethods);
        
        }else{
            return ApiResponse::sendNoDataErrorResponse('No Data is Available');
        }
    }
}

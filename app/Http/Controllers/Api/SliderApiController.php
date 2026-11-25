<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\ApiResponse\ApiResponse;

use Illuminate\Support\Facades\Validator;

use App\Models\Slider;
use Illuminate\Support\Facades\DB;

class SliderApiController extends Controller{

    public function index(){

        $sliders = Slider::select('main_title', 'title', 'category_id', 'mobile_image')
        ->where('is_active', 1)->get();

        if(!$sliders->isEmpty()){
            $path = url('uploads/sliders/');
            foreach($sliders as $slider){
                if($slider->mobile_image){
                    $slider->image = $path.'/'.$slider->mobile_image;
                }
            }
            return ApiResponse::sendSuccessResponse('Success',$sliders,$path);

        }else{
            return ApiResponse::sendNoDataErrorResponse('No Data found.');
        }
    }
}

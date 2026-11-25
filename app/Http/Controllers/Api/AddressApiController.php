<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Validator;
use App\ApiResponse\ApiResponse;

use Illuminate\Support\Facades\DB;

use App\Models\Address;
use App\Models\Province;
use App\Models\District;

use App\Models\Order;

class AddressApiController extends Controller{

    public function index(){
        $addresses = DB::table('addresses')
        ->join('zone_state_master', 'zone_state_master.zone_state_id', '=', 'addresses.province_id', 'LEFT')
        ->join('district_master', 'district_master.district_id', '=', 'addresses.district_id', 'LEFT')
        ->orderBy('addresses.id', 'asc')
        ->where('addresses.user_id', auth()->user()->id)
        ->select('addresses.id', 'zone_state_master.zone_state_name', 'addresses.province_id', 'addresses.district_id', 'district_master.district_name', 'addresses.street')
        ->get();

        if(!$addresses->isEmpty()){
            return ApiResponse::sendSuccessResponse('Success', $addresses);

        }else{
            return ApiResponse::sendNoDataErrorResponse('No Data is Available');
        }
    }

    public function store(Request $request){
        $rules=array(
            'province_id' => 'required|numeric',
            'district_id' => 'required|numeric',
            'street' => 'required',
        );

        $messages=array(
            'province_id.required' => 'province_id is required.',
            'district_id.required' => 'district_id is required.',
            'street.required' => 'street is required.',
        );

        $validator=Validator::make($request->all(),$rules,$messages);

        if($validator->fails()){
            $messages=$validator->messages();
            $errors=$messages->all();
            return ApiResponse::sendValidationErrorResponse($errors);
        }

        $userId = auth()->user()->id;

        $address = new Address();
        $address->user_id = $userId;
        $address->province_id = $request->province_id;
        $address->district_id = $request->district_id;
        $address->street = $request->street;
        $address->save();

        return ApiResponse::sendSuccessResponse('Address Created Successfully');
    }

    public function update(Request $request){
        $rules=array(
            'province_id' => 'required|numeric',
            'district_id' => 'required|numeric',
            'street' => 'required',
        );

        $messages=array(
            'province_id.required' => 'province_id is required.',
            'district_id.required' => 'district_id is required.',
            'street.required' => 'street is required.',
        );

        $validator=Validator::make($request->all(),$rules,$messages);

        if($validator->fails()){
            $messages=$validator->messages();
            $errors=$messages->all();

            return ApiResponse::sendValidationErrorResponse($errors);
        }
        else{
            $address = Address::find($request->id);

            $address->user_id = auth()->user()->id;
            $address->province_id = $request->province_id;
            $address->district_id = $request->district_id;
            $address->street = $request->street;

            $address->save();

            return ApiResponse::sendSuccessResponse('Address Updated Successfully');
        }
    }

    public function getAllProvinces(){
        $provinces = Province::select('zone_state_id', 'zone_state_name')
        ->where('is_deleted_flag', 'n')->get();

        if(!$provinces->isEmpty()){
            return ApiResponse::sendSuccessResponse('Success', $provinces);

        }else{
            return ApiResponse::sendNoDataErrorResponse('No Data is Available');
        }
    }

    public function getProvinceDistricts(Request $request){
        $rules = array(
            'province_id' => 'required|numeric',
        );

        $messages = array(
            'province_id.required' => 'province_id is required.',
        );

        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            $messages = $validator->messages();
            $errors = $messages->all();
            return ApiResponse::sendValidationErrorResponse($errors);
        }

        $districts = District::select('district_id', 'district_name')
        ->where('is_deleted_flag', 'n')
        ->where('zone_state_id',$request->province_id)->get();

        if(!$districts->isEmpty()){
            return ApiResponse::sendSuccessResponse('Success',$districts);

        }else{
            return ApiResponse::sendNoDataErrorResponse('No Data is Available');
        }
    }

    public function delete(Request $request){
        $rules=array(
            'address_id' => 'required|numeric',
        );

        $messages=array(
            'address_id.required' => 'Address is required.'
        );

        $validator=Validator::make($request->all(),$rules,$messages);

        if($validator->fails()){
            $messages=$validator->messages();
            $errors=$messages->all();

            return ApiResponse::sendValidationErrorResponse($errors);
        }
        else{

            $checkAddress = Address::where('user_id', auth()->user()->id)
            ->where('id', $request->address_id)->get();

            if(count($checkAddress) > 0){

                $checkAddress = Order::where('billing_address_id', $request->address_id)
                ->orWhere('shipping_address_id', $request->address_id)
                ->where('user_id', auth()->user()->id)->get();

                if(count($checkAddress) > 0){
                    return ApiResponse::sendNoDataErrorResponse('This Address Cannot Be Deleted.');
                }
                else{

                    Address::where('user_id', auth()->user()->id)->where('id', $request->address_id)->delete();

                    return ApiResponse::sendSuccessResponse('Address Deleted Successfully');
                }
            }
            else{
                return ApiResponse::sendNoDataErrorResponse('Specified Address Not Found.');
            }
        }
    }

    public function getAllDistricts(){

        $districts = District::select('district_id', 'district_name')
            ->where('is_deleted_flag', 'n')->get();

        if(!$districts->isEmpty()){
            return ApiResponse::sendSuccessResponse('Success', $districts);

        }else{
            return ApiResponse::sendNoDataErrorResponse('No Data is Available');
        }



    }
}

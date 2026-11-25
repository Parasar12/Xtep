<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

use App\Response\ApiResponse;

class BrandCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        // return parent::toArray($request);
        if($this->collection){

            return ApiResponse::sendResponse(200,null,'Success',$this->collection);
            
        }
        
    }

    public function with($request){
        return [
          'status'=>'success'
        ];
    }
}

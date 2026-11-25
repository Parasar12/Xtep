<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProductAttribute;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Validator;
use App\ApiResponse\ApiResponse;
use Illuminate\Support\Facades\DB;

use App\Models\Cart;
use App\Models\Product;

class CartApiController extends Controller{

  public function index(){
    $carts = Cart::select('id', 'product_id', 'price', 'quantity', 'attribute')
    ->where('user_id',auth()->user()->id)->get();


    $data = array();
    foreach ($carts as $key => $value) {

      $product = Product::select('id', 'name', 'brand_id')
      ->where('id', $value->product_id)->first();

        $attributeData = [];
      if(isset($value->attribute)){
//        $attributeList = json_decode($value->attribute, TRUE);

//        foreach ($attributeList as $key => $val) {
//          $attributeData[] = array(
//            'name' => \App\Models\Attribute::find($attributeList)->name,
//            'value' => $val['value'],
//          );
//        }
          $product_attribute =  ProductAttribute::find($value->attribute);
          $attributeData []= [
              'name' => \App\Models\Attribute::find($product_attribute->attribute_id)->name,
              'value' => $product_attribute->value,
              'attribute_id'=>$value->attribute
          ];
      }

      $data[] = array(
        'cart_id' => $value->id,
        'product_id' => $product->id,
        'product_price' => getProductPrice($product->id),
        'quantity' => $value->quantity,
        'total_price' => getProductPrice($product->id) * $value->quantity,
        'name' => $product->name,
        'brand_name' => ($product->brand) ? $product->brand->name : null,
        'image' => getProductGalleryImage($product->id),
        'attribute_flag' => ($product->productAttributes->isEmpty()) ? 0 : 1,
        'attribute' => $attributeData,
      );
    }

    return ApiResponse::sendSuccessResponse('Success', $data);
  }

  public function store(Request $request){
    $rules=array(
      'product_id' => 'required|numeric',
      'quantity' => 'required|numeric|min:1',
    );

    $messages=array(
      'product_id.required' => 'product_id is required.',
      'quantity.required' => 'quantity is required.',
    );

    $validator=Validator::make($request->all(),$rules,$messages);

    if($validator->fails()){
      $messages=$validator->messages();
      $errors=$messages->all();

      return ApiResponse::sendValidationErrorResponse($errors);
    }
    else{
        DB::beginTransaction();
      $checkCart = Cart::where('user_id', auth()->user()->id)
      ->where('product_id', $request->product_id)->first();


      if(!empty($checkCart) && empty($request->attribute)){
        if(($request->quantity) > getProductQuantity($request->product_id)){
          return ApiResponse::sendExceptionResponse('Quantity exceeds maximum available quantity');
        }else{

          Cart::where('user_id', auth()->user()->id)->where('product_id', $request->product_id)->update([
            'quantity' => $checkCart->quantity + $request->quantity
          ]);
        }
      }
      else{

        if(! empty($request->attribute)) {
              $cart = new Cart();
              $cart->user_id = auth()->user()->id;
              $cart->product_id = $request->product_id;
              $cart->price = getProductPrice($request->product_id);
              $cart->quantity = $request->quantity;
              $cart->attribute = $request->attribute;
              $cart->save();
        }
        else{

            $cart = new Cart();
            $cart->user_id = auth()->user()->id;
            $cart->product_id = $request->product_id;
            $cart->price = getProductPrice($request->product_id);
            $cart->quantity = $request->quantity;
            $cart->save();
        }

      }
      DB::commit();
      return ApiResponse::sendSuccessResponse('Cart Created Successfully');
    }
  }

  public function updateCart(Request $request){
    $rules=array(
      'cart_id' => 'required|numeric',
      'quantity' => 'required|numeric|min:1',
    );

    $messages=array(
      'cart_id.required' => 'Cart is required.',
      'quantity.required' => 'Quantity is required.',
    );

    $validator=Validator::make($request->all(),$rules,$messages);

    if($validator->fails()){
      $messages=$validator->messages();
      $errors=$messages->all();

      return ApiResponse::sendValidationErrorResponse($errors);
    }
    else{

      $checkCart = Cart::where('user_id', auth()->user()->id)
      ->where('id', $request->cart_id)->first();

      if(! empty($checkCart)){

        if(($request->quantity) > getProductQuantity($checkCart->product_id)){
          return ApiResponse::sendExceptionResponse('Quantity exceeds maximum available quantity');
        }else{
          Cart::where('user_id', auth()->user()->id)
          ->where('id', $request->cart_id)
          ->update([
            'quantity' => $request->quantity
          ]);

          return ApiResponse::sendSuccessResponse('Cart Item Updated Successfully');
        }
      }
      else{
        return ApiResponse::sendNoDataErrorResponse('No Products in Cart');
      }
    }
  }

  public function removeProductFromCart(Request $request){
    $rules=array(
      'cart_id' => 'required|numeric',
    );

    $messages=array(
      'cart_id.required' => 'Cart is required.',
    );

    $validator=Validator::make($request->all(),$rules,$messages);

    if($validator->fails()){
      $messages=$validator->messages();
      $errors=$messages->all();

      return ApiResponse::sendValidationErrorResponse($errors);
    }
    else{

      $checkCart = Cart::where('user_id', auth()->user()->id)
      ->where('id', $request->cart_id)->get();

      if(!empty($checkCart)){
        Cart::where('user_id', auth()->user()->id)
        ->where('id', $request->cart_id)->delete();

        return ApiResponse::sendSuccessResponse('Product Removed From Cart Successfully.');
      }
      else{
        return ApiResponse::sendNoDataErrorResponse('No Products in Cart');
      }
    }
  }

  public function clearCartProducts(){
    $checkCart = Cart::where('user_id', auth()->user()->id)->get();

    if(count($checkCart) > 0){
      Cart::where('user_id', auth()->user()->id)->delete();

      return ApiResponse::sendSuccessResponse('All Products Removed From Cart Successfully.');
    }
    else{
      return ApiResponse::sendNoDataErrorResponse('No Products in Cart');
    }
  }



}

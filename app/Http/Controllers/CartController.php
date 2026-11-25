<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Coupon;
use App\Models\District;
use App\Models\Province;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

use App\Models\Cart;
use App\Models\ShippingMethod;
use App\Models\ProductAttribute;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use stdClass;
use Illuminate\Support\Facades\Response;


use Illuminate\Support\Facades\Validator;

class CartController extends Controller
{

    public function cartList()
    {
        $this->clearQuickOrderData();
        $cartList = null;
        if (auth()->user()) {
            $cartList = Cart::where('user_id', auth()->user()->id)->get();
        }
        if (session()->get('cart')) {
            $cartList = session()->get('cart');
            // dd($cartList);
        }
        return view('frontend.cart.cart', compact('cartList'));
    }

    public function addToCart(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'quantity' => 'required|numeric|min:1',
            'product_id' => 'required|numeric',
            'attribute' => 'nullable|numeric'
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 0, 'error' => $validator->errors()->toArray()]);
        } else {
            $attribute = $request->attribute ? $request->attribute : null;
            $price = getProductVariantPrice($request->product_id, $attribute);
            // if ($request->quantity > getProductQuantity($request->product_id)) {
            //     return response()->json(['status' => 0, 'error' => ["quantity" => ["Quantity exceeds maximum available quantity."]]]);
            // }
            if (auth()->user()) {
                $id = auth()->id();
                $existUserProductCart = Cart::where('user_id', auth()->id())
                    ->where('product_id', $request->product_id)
                    ->orderBy('id', 'ASC')->get();

                if ($existUserProductCart->count() > 0) {

                    for ($i = 0; $i < $existUserProductCart->count(); $i++) {

                        // if (($existUserProductCart[$i]->quantity + $request->quantity) > getProductQuantity($request->product_id)) {
                        //     return response()->json(['status' => 0, 'error' => ["quantity" => ["Quantity exceeds maximum available quantity."]]]);
                        // } else {

                        if (!empty($existUserProductCart[$i]->attribute)) {
                            $oldAttribute = $existUserProductCart[$i]->attribute;
                            if (($existUserProductCart->count() - 1) == $i) {
                                if ($attribute == $oldAttribute) {
                                    Cart::where('user_id', auth()->id())
                                        ->where('product_id', $request->product_id)
                                        ->where('id', $existUserProductCart[$i]->id)
                                        ->update([
                                            'quantity' => $existUserProductCart[$i]->quantity + $request->quantity
                                        ]);
                                } else {
                                    $cart = new Cart();
                                    $cart->user_id = $id;
                                    $cart->product_id = $request->product_id;
                                    $cart->price = $price;
                                    $cart->quantity = $request->quantity;
                                    $cart->attribute = $attribute;
                                    $cart->save();
                                }
                            } else {
                                if ($attribute == $oldAttribute) {
                                    Cart::where('user_id', auth()->id())
                                        ->where('product_id', $request->product_id)
                                        ->where('id', $existUserProductCart[$i]->id)
                                        ->update([
                                            'quantity' => $existUserProductCart[$i]->quantity + $request->quantity
                                        ]);

                                    break;
                                }
                            }
                        } else {

                            Cart::where('user_id', auth()->id())
                                ->where('product_id', $request->product_id)
                                ->where('id', $existUserProductCart[$i]->id)
                                ->update([
                                    'quantity' => $existUserProductCart[$i]->quantity + $request->quantity
                                ]);
                        }
                        // }
                    }

                    return response()->json(['status' => 1, 'msg' => getProductName($request->product_id) . "Updated to Cart Successfully."]);
                } else {
                    $cart = new Cart();
                    $cart->user_id = $id;
                    $cart->product_id = $request->product_id;
                    $cart->price = $price;
                    $cart->quantity = $request->quantity;
                    $cart->attribute = $attribute;
                    $cart->save();

                    return response()->json(['status' => 1, 'msg' => getProductName($request->product_id) . " Added to Cart Successfully."]);
                }
            } else {
                $data = new stdClass;
                $data->quantity = $request->quantity;
                $data->price = $price;
                $data->product_id = $request->product_id;
                $data->attribute = $attribute;

                if (session()->has('cart')) {
                    $foundInCart = false;
                    $cart = collect();

                    foreach (session()->get('cart') as $key => $cartItem) {
                        if ($cartItem->product_id == $request->product_id) {
                            if ($cartItem->attribute != null) {
                                $oldSessionAttribute = $cartItem->attribute;

                                if ($attribute == $oldSessionAttribute) {
                                    $foundInCart = true;
                                    $cartItem->quantity = $cartItem->quantity + $request->quantity;
                                    $cart->push($cartItem);
                                } else {
                                    $cart->push($cartItem);
                                }
                            } else {
                                $foundInCart = true;
                                $cartItem->quantity = $cartItem->quantity + $request->quantity;
                                $cart->push($cartItem);
                            }
                        } else {
                            $cart->push($cartItem);
                        }
                        // dd(session()->get('cart'));
                    }

                    if (!$foundInCart) {
                        $cart->push($data);
                    }

                    session()->put('cart', $cart);
                } else {
                    $cart = collect([$data]);
                    $request->session()->put('cart', $cart);
                }

                return response()->json(['status' => 1, 'msg' => getProductName($request->product_id) . " Added to Cart Successfully."]);
            }
        }
    }

    public function selectShipping(Request $request) {}

    /*Update Product Quantity from View Cart Page*/
    public function updateCart(Request $request)
    {
        if (auth()->user()) {

            $validator = Validator::make($request->all(), [
                'quantity' => 'required|numeric|min:1',
            ]);

            if ($validator->fails()) {
                return response()->json(['status' => 0, 'error' => $validator->errors()->toArray()]);
            }

            // if (($request->quantity) > getProductQuantity($request->product_id)) {
            //     return response()->json(['status' => 0, 'error' => ["quantity" => ["Quantity exceeds maximum available quantity."]]]);
            // } else {
            Cart::where('user_id', auth()->user()->id)->where('product_id', $request->product_id)->update([
                'quantity' => $request->quantity
            ]);

            return response()->json(['status' => 1, 'msg' => "Cart Item Updated Successfully."]);
            // }
        } else {
            return response()->json(['status' => 0, 'error' => "Login to Update Item Quantity."]);
        }
    }

    /*Removing Single Product From Cart*/
    public function removeCart(Request $request)
    {
        if (auth()->user()) {
            Cart::where('user_id', auth()->user()->id)->where('product_id', $request->id)->delete();

            return response()->json(['status' => 1, 'msg' => "Cart Item Removed Successfully."]);
        } else {
            if (session()->has('cart')) {
                $cart = collect(session()->get('cart'));
                $cart = $cart->reject(function ($item) use ($request) {
                    return $item->product_id == $request->id;
                });

                // Update the session with the modified cart
                session()->put('cart', $cart);

                return response()->json(['status' => 1, 'msg' => "Cart Item Removed Successfully."]);
            } else {
                return response()->json(['status' => 0, 'error' => "No items in the cart."]);
            }
        }
    }

    public function clearAllCartItems(Request $request)
    {
        if (auth()->user()) {
            Cart::where('user_id', auth()->user()->id)->delete();
            return response()->json(['status' => 1, 'msg' => "Cart Cleared Successfully."]);
        }

        if (session()->get('cart')) {
            session()->flush();
            return response()->json(['status' => 1, 'msg' => "Cart Cleared Successfully."]);
        }
    }

    public function addShippingAddress()
    {

        $coupon = [];
        $response = false;

        $entered_coupon_code = '';

        $entered_coupon_code = \request()->coupon;
        Session::forget('message');

        //coupon check
        if (\request()->coupon) {
            $today = Carbon::now()->toDateString();

            $coupon_code = Coupon::where([
                ['code', \request()->coupon],
            ])->first();


            if (!($coupon_code)) {

                return  redirect()->route('cart.shipping')->with([
                    'error' => 'you have entered wrong coupon number',
                    'coupon' => $entered_coupon_code
                ]);
            } else {
                $coupon = Coupon::where([
                    ['code', \request()->coupon],
                    ['start_date', '<=', $today],
                    ['end_date', '>=', $today],
                    ['is_active', true]
                ])->first();

                if ($coupon) {

                    //check if product exist in that coupon

                    if (auth()->user()) {
                        if (Session::has('quick_order')) {
                            $carts = DB::table('quick_orders')->where('user_id', \auth()->user()->id)->get();
                        } else {
                            $carts = Cart::where('user_id', auth()->id())->get();
                        }
                    } else {
                        $carts = session()->get('cart');
                    }

                    foreach ($carts as $d) {
                        if (in_array($d->product_id, getCouponProducts($coupon ? $coupon->id : '')) || in_array($d->product_id, getCouponCategories($coupon ? $coupon->id : ''))) {
                            $response = true;
                            break;
                        } else {
                            $response = false;
                        }
                    }
                    if ($response == false) {
                        $coupon = [];
                        return  redirect()->route('cart.shipping')->with([
                            'error' => 'no offer in this product',
                            'coupon' => $entered_coupon_code
                        ]);
                    }
                    $totalCouponUses =  DB::table('order_coupon')->where('coupon_id', $coupon->id)->get()->count();

                    if ($totalCouponUses < $coupon->usage_limit_of_coupon) {
                        if ($coupon->minimum_spend) {
                            if ($coupon->minimum_spend > \request()->total) {
                                $amount = $coupon->minimum_spend;
                                $coupon = [];
                                return  redirect()->route('cart.shipping')->with([
                                    'error' => 'you have to buy more product than Rs. ' . $amount,
                                    'coupon' => $entered_coupon_code
                                ]);
                            }
                        }

                        if ($coupon->maximum_spend) {
                            if ($coupon->maximum_spend < \request()->total) {
                                $amount = $coupon->maximum_spend;
                                $coupon = [];
                                return  redirect()->route('cart.shipping')->with([
                                    'error' => 'you bought more product than Rs. ' . $amount . ' can not applied coupon',
                                    'coupon' => $entered_coupon_code
                                ]);
                            }
                        }

                        $couponOrderByUser = DB::table('order_coupon')->where([
                            ['user_id', \auth()->user()->id],
                            ['coupon_id', $coupon->id]
                        ])->get()->count();

                        if ($couponOrderByUser >= $coupon->usage_limit_of_person) {
                            return redirect()->route('cart.shipping')->with([
                                'error' => 'you have exceeded your coupon limit',
                                'coupon' => $entered_coupon_code
                            ]);
                        }
                    } else {
                        return redirect()->route('cart.shipping')->with([
                            'error' => 'coupon limit exceeded',
                            'coupon' => $entered_coupon_code
                        ]);
                    }
                } else {
                    return redirect()->route('cart.shipping')->with([
                        'error' => 'coupon has been expired',
                        'coupon' => $entered_coupon_code
                    ]);
                }
            }

            Session::put('message', 'coupon applied successfully');
        }

        $provinces = Province::all();
        $disctricts = District::all();
        $shippingMethods = ShippingMethod::where('is_active', 1)->get();
        $paymentOption = DB::table('payment_mode')
            ->orderBy('payment_mode_id', 'asc')
            ->where('is_active', 1)
            ->select('payment_mode_id', 'name')
            ->get();

        if (auth()->user()) {

            $addresses = Address::where('user_id', auth()->id())->orderBy('created_at', 'desc')->first();

            if (Session::has('quick_order')) {
                $cartList = DB::table('quick_orders')->where('user_id', \auth()->user()->id)->get();
            } else {
                $cartList = Cart::where('user_id', auth()->id())->get();
            }
        } else {
            $cartList = session()->get('cart');
            $addresses = null;
        }
        if (sizeof($cartList) == 0) {
            return redirect()->route('front.index');
        }


        return view('frontend.cart.checkout', compact('cartList', 'shippingMethods', 'paymentOption', 'addresses', 'provinces', 'disctricts', 'coupon'));
    }

    public function getCart()
    {
        if (auth()->user()) {
            $cartList = Cart::where('user_id', auth()->user()->id)->get();
        }

        if (session()->get('cart')) {
            $cartList = session()->get('cart');
        }

        return view('frontend.cart.side_cart', compact('cartList'));
    }

    public function cartCount()
    {
        $count = countCartItems();
        return response()->json([
            'status' => 1,
            'count' => $count,
        ]);
    }

    public function singleProductPrice(Request $request)
    {
        $cart = Cart::where([
            ['product_id', $request->product_id],
            ['user_id', Auth::user()->id]
        ])->first();
        $total = $cart->quantity * getProductPrice($request->product_id);
        return $total;
    }

    public function totalPrice()
    {
        $carts = Cart::where('user_id', Auth::user()->id)->get();

        $total = 0;
        foreach ($carts as $item) {
            $total += $item->quantity * getProductPrice($item->product_id);
        }

        return $total;
    }

    public function getHeaderCart(Request $request): JsonResponse
    {
        $output = array(
            'response' => false,
            'msg' => "Something went wrong. Please try again later.",
            'result' => "",
        );

        try {
            $view = view('frontend.layouts.side_cart')->render();
            $output = array(
                'response' => true,
                'msg' => "",
                'result' => $view,
            );
        } catch (Exception $e) {
            $output['msg'] = $e->getMessage();
        }
        return Response::json($output);
    }

    public function apply_coupon()
    {

        $coupon = [];

        $entered_coupon_code = '';

        $entered_coupon_code = \request()->coupon;

        if (\request()->coupon) {
            $today = Carbon::now()->toDateString();

            $coupon_code = Coupon::where([
                ['code', \request()->coupon],
            ])->first();

            if (!($coupon_code)) {

                return  redirect()->back()->with([
                    'error' => 'you have entered wrong coupon number',
                    'coupon' => $entered_coupon_code
                ]);
            } else {
                $coupon = Coupon::where([
                    ['code', \request()->coupon],
                    ['start_date', '<=', $today],
                    ['end_date', '>=', $today],
                    ['is_active', true]
                ])->first();

                $totalCouponUses =  DB::table('order_coupon')->where('coupon_id', $coupon->id)->get()->count();
                if ($totalCouponUses <= $coupon->usage_limit_of_coupon) {
                    if ($coupon->minimum_spend) {
                        if ($coupon->minimum_spend > \request()->total) {
                            $amount = $coupon->minimum_spend;
                            $coupon = [];
                            return  redirect()->back()->with([
                                'error' => 'you have to buy more product than Rs. ' . $amount,
                                'coupon' => $entered_coupon_code
                            ]);
                        }
                    }
                    $couponOrderByUser = DB::table('order_coupon')->where([
                        ['user_id', \auth()->user()->id],
                        ['coupon_id', $coupon->id]
                    ])->get()->count();

                    if ($couponOrderByUser >= $coupon->usage_limit_of_person) {
                        return redirect()->back()->with([
                            'error' => 'you have exceeded your coupon limit',
                            'coupon' => $entered_coupon_code
                        ]);
                    }
                } else {
                    return redirect()->back()->with([
                        'error' => 'coupon limit exceeded',
                        'coupon' => $entered_coupon_code
                    ]);
                }
            }
        }

        $provinces = Province::all();
        $disctricts = District::all();
        $shippingMethods = ShippingMethod::where('is_active', 1)->get();
        $paymentOption = DB::table('payment_mode')
            ->orderBy('payment_mode_id', 'asc')
            ->where('is_active', 1)
            ->select('payment_mode_id', 'name')
            ->get();

        if (auth()->user()) {

            $addresses = Address::where('user_id', auth()->id())->get();

            if (Session::has('quick_order')) {
                $cartList = Session::get('quickProducts');
            } else {
                $data = Cart::where('user_id', auth()->id())->get();
                $cartList = array($data);
            }
        } else {
            $cartList = session()->get('cart');
            $addresses = null;
        }
        if (sizeof($cartList) == 0) {
            return redirect()->route('front.index');
        }


        return view('frontend.cart.checkout', compact('cartList', 'shippingMethods', 'paymentOption', 'addresses', 'provinces', 'disctricts', 'coupon'));
    }
}

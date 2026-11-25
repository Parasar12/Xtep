<?php

namespace App\Http\Controllers;

use App\Models\District;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use App\Models\Address;
use App\Models\Order;
use App\Models\Province;
use App\Models\User;
use App\Models\Cart;
use App\Models\Ticket;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class UserProfileController extends Controller
{

    public function index()
    {
        $orders = Order::where('user_id', auth()->id())->groupBy('created_at')->get();
        $addresses = Address::where('user_id', auth()->user()->id)->get();
        $provinces = Province::all();
        $disctricts = District::all();

        return view('frontend.user.my-account', compact('addresses', 'orders', 'provinces', 'disctricts'));
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'email' => 'required|max:255|unique:users,email,' . auth()->user()->id,
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 0, 'error' => $validator->errors()->toArray()]);
        } else {
            if ($request->password) {
                if (Hash::check($request->password, auth()->user()->password)) {
                    $passwordValidator = Validator::make($request->all(), [
                        'new_password' => ['required', 'confirmed'],
                    ]);

                    if ($passwordValidator->fails()) {
                        return response()->json(['status' => 0, 'error' => ["new_password_confirmation" => ["Password Confirmation Error."]]]);
                    } else {
                        $user = User::find(auth()->user()->id);
                        $user->name = $request->name;
                        $user->email = $request->email;
                        $user->password = bcrypt($request->new_password);
                        $user->save();
                        return response()->json(['status' => 1, 'msg' => "User Profile Updated Successfully."]);
                    }
                } else {
                    return response()->json(['status' => 0, 'error' => ["password" => ["Incorrect Current Password."]]]);
                }
            } else {
                $user = User::find(auth()->user()->id);
                $user->name = $request->name;
                $user->email = $request->email;
                $user->save();
                return response()->json(['status' => 1, 'msg' => 'User Profile Updated Successfully.']);
            }
        }
    }

    public function adminLogin(Request $request)
    {
        $this->validate($request, [
            'email' => 'required',
            'password' => 'required',
        ]);

        $credentials = [
            "email" => $request->email,
            "password" => $request->password,
            "is_active" => 1,
            "is_verified" => 1,
            "is_admin" => 1
        ];

        if (Auth::attempt($credentials)) {
//            dd($credentials);
            $ids = getUserCartProductIds();
            if (session()->has('cart')) {
                foreach (session()->get('cart') as $key => $sessionCart) {
                    if (in_array($sessionCart->product_id, $ids)) {
                        Cart::where('user_id', auth()->user()->id)->where('product_id', $sessionCart->product_id)->increment('quantity', $sessionCart->quantity);
                    } else {
                        $cart = new Cart();
                        $cart->user_id = auth()->user()->id;
                        $cart->product_id = $sessionCart->product_id;
                        $cart->quantity = $sessionCart->quantity;
                        $cart->price = $sessionCart->price;
                        $cart->attribute = $sessionCart->attribute;
                        $cart->save();
                    }
                }

                session()->forget('cart');
            }

            return redirect('admin/dashboard')->with('success', 'Login Successful');
        } else {
            return redirect('/admin')->with('error', "Invalid User Credentials");
        }
    }

    public function userLogin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required|min:8|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 0, 'error' => $validator->errors()->toArray()]);
        } else {

            $credentials = [
                "email" => $request->email,
                "password" => $request->password,
                "is_active" => 1,
                "is_verified" => 1,
            ];

            $user = User::where('contact', $request->email)
                ->orWhere('email', $request->email)
                ->first();
            if ($user) {
                if (Hash::check($request->password, $user->password)) {
                    Auth::login($user);
                    self::userCartData();

                    return response()->json(["status" => 1, "redirect_location" => url("/account")]);
                } else {
                    return response()->json(['status' => 0, 'error' => ["password" => ["Invalid User Credentials."]]]);
                }
            } else if (Auth::attempt($credentials)) {
                self::userCartData();
                return response()->json(["status" => 1, "redirect_location" => url("/account")]);
            } else {
                return response()->json(['status' => 0, 'error' => ["password" => ["Invalid User Credentials."]]]);
            }
        }
    }

    public function userSignup(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'signup_email' => 'required|unique:users,email|max:255',
            'signup_contact' => 'required|unique:users,contact|digits:10',
            'signup_password' => 'required|max:255|min:8',
            'signup_name' => 'required|max:255',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 0, 'error' => $validator->errors()->toArray()]);
        } else {
            DB::beginTransaction();

            $user = new User();
            $user->name = $request->signup_name;
            $user->email = $request->signup_email;
            $user->contact = $request->signup_contact;
            $user->password = bcrypt($request->signup_password);
            $user->is_active = 1;
            $user->is_verified = 1;
            $user->save();

            Auth::login($user);
            self::userCartData();


            DB::commit();
            return response()->json(["status" => 1, "msg" => "User Registered Successfully. Kindly login to Proceed"]);
        }
    }


    public function orderRequest(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'signup_contact' => 'required|unique:users,contact|digits:10',
            'signup_name' => 'required|max:255',
            'province_id' => 'required|numeric',
            'district_id' => 'required|numeric',
            'street' => 'required|max:255',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 0, 'error' => $validator->errors()->toArray()]);
        } else {
            DB::beginTransaction();

            $user = new User();
            $user->name = $request->signup_name;
            // $user->email = $request->signup_email;
            $user->contact = $request->signup_contact;
            $user->password = bcrypt($request->signup_contact);
            $user->is_active = 1;
            $user->is_verified = 1;
            $user->save();

            Auth::login($user);
            self::userCartData();

            $address = new Address();
            $address->user_id = auth()->user()->id;
            $address->province_id = $request->province_id;
            $address->district_id = $request->district_id;
            $address->street = $request->street;
            $address->save();

            DB::commit();
            return response()->json([
                "status" => 1,
                "msg" => "User Registered Successfully. Your password is the contact number you provided: " . $request->signup_contact
            ]);
        }
    }


    public function adminLogout(Request $request)
    {
        $request->session()->flush();
        return redirect('/admin')->with('success', "Logout Sucessfully");
    }

    public function userLogout(Request $request)
    {
        $request->session()->flush();
        return redirect('/user/login')->with('success', "Logout Sucessfully");
    }

    public function createGuestUser(Request $request)
    {
        $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|unique:users'
        ]);
        $phone = $request->session()->get('PHONE');

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->contact = $phone;
        $user->is_active = 1;
        $user->is_verified = 0;
        $user->save();

        Auth::login($user);

        $ids = getUserCartProductIds();
        if (session()->has('cart')) {
            foreach (session()->get('cart') as $key => $sessionCart) {
                if (in_array($sessionCart->product_id, $ids)) {
                    Cart::where('user_id', auth()->user()->id)->where('product_id', $sessionCart->product_id)->increment('quantity', $sessionCart->quantity);
                } else {
                    $attributeArray = json_decode($sessionCart->attribute, TRUE);

                    $cart = new Cart();
                    $cart->user_id = auth()->user()->id;
                    $cart->product_id = $sessionCart->product_id;
                    $cart->quantity = $sessionCart->quantity;
                    $cart->price = $sessionCart->price;
                    $cart->attribute = json_encode($attributeArray);

                    $cart->save();
                }
            }
            Session::forget('cart');
            Session::forget('PHONE');
        }
        return response()->json(['status' => true, 'message' => 'Success']);
    }

    public function userCartData()
    {
        $ids = getUserCartProductIds();

        if (session()->has('cart')) {
            foreach (session()->get('cart') as $key => $sessionCart) {
                if (in_array($sessionCart->product_id, $ids)) {
                    Cart::where('user_id', auth()->user()->id)->where('product_id', $sessionCart->product_id)->increment('quantity', $sessionCart->quantity);
                } else {
                    $attributeArray = json_decode($sessionCart->attribute, TRUE);

                    $cart = new Cart();
                    $cart->user_id = auth()->user()->id;
                    $cart->product_id = $sessionCart->product_id;
                    $cart->quantity = $sessionCart->quantity;
                    $cart->price = $sessionCart->price;
                    $cart->attribute = json_encode($attributeArray);

                    $cart->save();
                }
            }

            session()->forget('cart');
        }
    }
}

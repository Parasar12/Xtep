<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{


    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $user = Socialite::driver('google')->user();
            $google_user = User::updateOrcreate([
                'google_id' => $user->getId(),
                'email' => $user->getEmail(),
            ], [
                'name' => $user->getName(),
                'profile_image' => $user->getAvatar(),
                'remember_token' => $user->token,
                'refreshToken' => $user->refreshToken,
            ]);

            Auth::login($google_user);

            $cartItems = Cart::where('user_id', auth()->id())->get();

            $productArray = [];
            foreach ($cartItems as $item) {
                $productArray[] = $item->product_id;
            }
            if (session()->has('cart')) {

                foreach (session()->get('cart') as $key => $sessionCart) {
                    if (in_array($sessionCart->product_id, $productArray)) {
                        Cart::where('user_id', auth()->user()->id)
                            ->where('product_id', $sessionCart->product_id)
                            ->increment('quantity', $sessionCart->quantity);
                    } else {
                        Cart::create([
                            'user_id' => auth()->user()->id,
                            'product_id' => $sessionCart->product_id,
                            'quantity' => $sessionCart->quantity,
                        ]);
                    }

                }

                session()->forget('cart');
            }
            return redirect('/');
        } catch (Exception $e) {
            dd($e->getMessage());
        }
    }

    public function logout()
    {

        Auth::logout();

        return redirect('/');
    }
}

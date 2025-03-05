<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Models\SavedItem;
use App\Models\Cart;
use App\Models\CartItem;

class AuthenticatedSessionController extends Controller
{

    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        Auth::login($user);

        $message = "Welcome {$user->name}, You have successfully logged in. \nGrab the latest Carton Box Guru offers now!";

        return redirect()->route('home')->with('status', $message);
    }


    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        // Validate the request
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        // Attempt to authenticate the user
        if (Auth::attempt($request->only('email', 'password'))) {
            $user = Auth::user();

            $ip_address = $request->ip();

            SavedItem::whereNull('user_id')
                ->where('ip_address', $ip_address)
                ->update(['user_id' => $user->id]);

            $cartnumber = $request->input('cartnumber');
            if ($cartnumber == null) {
                $cartnumber = session()->get('cartnumber');
            }

            $customer_cart = Cart::where('customer_id', $user->id)->first();
            $guest_cart = Cart::where('cart_number', $cartnumber)->whereNull('customer_id')->first();

            if ($guest_cart && $customer_cart) {
                foreach ($guest_cart->items as $item) {
                    $existing_item = CartItem::where('cart_id', $customer_cart->id)
                        ->where('product_id', $item->product_id)
                        ->first();
                    if ($existing_item) {
                        $existing_item->quantity += $item->quantity;
                        $existing_item->save();
                    } else {
                        $item->cart_id = $customer_cart->id;
                        $item->save();
                    }
                }

                // Update totals in customer cart
                $customer_cart->item_count += $guest_cart->item_count;
                $customer_cart->quantity += $guest_cart->quantity;
                $customer_cart->total += $guest_cart->total;
                $customer_cart->discount += $guest_cart->discount;
                $customer_cart->shipping += $guest_cart->shipping;
                $customer_cart->packaging += $guest_cart->packaging;
                $customer_cart->handling += $guest_cart->handling;
                $customer_cart->taxes += $guest_cart->taxes;
                $customer_cart->grand_total += $guest_cart->grand_total;
                $customer_cart->shipping_weight += $guest_cart->shipping_weight;
                $customer_cart->save();

                // Delete the guest cart after merging
                $guest_cart->delete();
            } elseif ($guest_cart) {
                // If no customer cart exists, assign the guest cart to the user
                $guest_cart->customer_id = $user->id;
                $guest_cart->save();
                $customer_cart = $guest_cart;
            }

            session(['cartnumber' => $customer_cart->cart_number ?? $cartnumber]);
            $merged_cart_number = session('cartnumber');

            $message = "Welcome {$user->name}, You have successfully logged in. \nGrab the latest Carton Box Guru offers now!";

            return redirect()->intended(route('home', ['cartnumber' => $merged_cart_number], false))
                ->with('status', $message);
        }

        // If authentication fails, redirect back with an error message
        return redirect()->back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    public function logout(Request $request)
    {
        if ($request->wantsJson()) {
            $request->user()->token()->revoke();
            return response()->json(['message' => 'Logged out successfully!']);
        }

        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('status', 'Logged out successfully!');
    }

    public function showForgotPage()
    {
        return view('auth.forgot_password');
    }
}

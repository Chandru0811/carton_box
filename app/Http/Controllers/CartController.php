<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Addresses;
use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use App\Models\Cart;
use App\Models\CartItem;


class CartController extends Controller
{
    public function index(Request $request)
    {
        $carts = Cart::where('ip_address', $request->ip());

        if (Auth::guard()->check()) {
            $carts = $carts->orWhere('customer_id', Auth::guard()->user()->id);
        }

        $cart = $carts->first();

        $bookmarkedProducts = collect();

        if ($cart) {
            $cart->load(['items.product.shop', 'items.product.productMedia:id,resize_path,order,type,imageable_id']);
        }


        $user_id = Auth::check() ? Auth::user()->id : null;

        return view('cart', compact('cart', 'bookmarkedProducts'));
    }

    public function addToCart(Request $request, $slug)
    {
        $product = Product::where('slug', $slug)->first();

        if (!$product) {
            return response()->json(['error' => 'Deal not found!'], 404);
        }

        $customer_id = Auth::check() ? Auth::user()->id : null;

        if ($customer_id) {
            $old_cart = Cart::where(function ($query) use ($customer_id) {
                $query->where('customer_id', $customer_id)
                    ->orWhere(function ($q) {
                        $q->whereNull('customer_id')->where('ip_address', request()->ip());
                    });
            })->first();
        } else {
            $old_cart = Cart::where('ip_address', $request->ip())->first();
        }

        // Check if the item is already in the cart
        if ($old_cart) {
            $item_in_cart = CartItem::where('cart_id', $old_cart->id)->where('product_id', $product->id)->first();
            if ($item_in_cart && $request->saveoption == "buy now") {
                return redirect()->route('checkout.summary', $product->id);
            } elseif ($item_in_cart) {
                return response()->json(['error' => 'Deal already in cart!'], 400);
            }
        }

        $qtt = $request->quantity;
        if ($qtt == null) {
            $qtt = 1;
        }
        $payment_status = $request->payment_status;
        if ($payment_status == null) {
            $payment_status = 1;
        }

        $grand_total = $product->discounted_price * $qtt + $request->shipping + $request->packaging + $request->handling + $request->taxes;
        $discount = ($product->original_price - $product->discounted_price) * $qtt;

        $cart = $old_cart ?? new Cart;
        $cart->customer_id = $customer_id;
        $cart->ip_address = $request->ip();
        $cart->item_count = $old_cart ? ($old_cart->item_count + 1) : 1;
        $cart->quantity = $old_cart ? ($old_cart->quantity + $qtt) : $qtt;
        $cart->total = $old_cart ? ($old_cart->total + ($product->original_price * $qtt)) : ($product->original_price * $qtt);
        $cart->discount = $old_cart ? ($old_cart->discount + $discount) : $discount;
        $cart->shipping = $old_cart ? ($old_cart->shipping + $request->shipping) : $request->shipping;
        $cart->packaging = $old_cart ? ($old_cart->packaging + $request->packaging) : $request->packaging;
        $cart->handling = $old_cart ? ($old_cart->handling + $request->handling) : $request->handling;
        $cart->taxes = $old_cart ? ($old_cart->taxes + $request->taxes) : $request->taxes;
        $cart->grand_total = $old_cart ? ($old_cart->grand_total + $grand_total) : $grand_total;
        $cart->shipping_weight = $old_cart ? ($old_cart->shipping_weight + $request->shipping_weight) : $request->shipping_weight;
        $cart->save();

        $cart_item = new CartItem;
        $cart_item->cart_id = $cart->id;
        $cart_item->product_id = $product->id;
        $cart_item->item_description = $product->name;
        $cart_item->quantity = $qtt;
        $cart_item->unit_price = $product->original_price;
        $cart_item->delivery_date = $request->delivery_date;
        $cart_item->coupon_code = $product->coupon_code;
        $cart_item->discount = $product->discounted_price;
        $cart_item->discount_percent = $product->discount_percentage;
        $cart_item->seller_id = $product->shop_id;
        $cart_item->deal_type = $product->deal_type;
        $cart_item->service_date = $request->service_date;
        $cart_item->service_time = $request->service_time;
        $cart_item->shipping = $request->shipping;
        $cart_item->packaging = $request->packaging;
        $cart_item->handling = $request->handling;
        $cart_item->taxes = $request->taxes;
        $cart_item->shipping_weight = $request->shipping_weight;
        $cart_item->save();

        $cartItemCount = CartItem::where('cart_id', $cart->id)->count();

        if ($request->saveoption == "buy now") {
            return redirect()->route('checkout.summary', ['id' => $product->id, 'quantity' => $qtt]);
        } else {
            return response()->json([
                'status' => 'Deal added to cart!',
                'cartItemCount' => $cartItemCount
            ]);
        }
    }

    public function updateCart(Request $request)
    {
        $cart = Cart::find($request->cart_id);

        if (!$cart) {
            return response()->json([
                'status' => 'error',
                'message' => 'Cart not found!',
                'redirect' => url()->previous(),
            ], 404);
        }

        $cart_item = CartItem::where('cart_id', $cart->id)->where('product_id', $request->product_id)->first();

        if (!$cart_item) {
            return response()->json([
                'status' => 'error',
                'message' => 'Deal not found in cart!',
                'redirect' => url()->previous(),
            ], 404);
        }

        $qtt = $request->quantity;

        if ($qtt == null) {
            $qtt = 1;
        }

        $grand_total = $cart_item->discount * $qtt + $cart_item->shipping + $cart_item->packaging + $cart_item->handling + $cart_item->taxes;

        $cart->quantity = $cart->quantity - $cart_item->quantity + $qtt;
        $cart->total = $cart->total - ($cart_item->unit_price * $cart_item->quantity) + ($cart_item->unit_price * $qtt);
        $cart->discount = $cart->discount - (($cart_item->unit_price - $cart_item->discount) * $cart_item->quantity) + (($cart_item->unit_price - $cart_item->discount) * $qtt);
        $cart->shipping = $cart->shipping - $cart_item->shipping + $cart_item->shipping;
        $cart->packaging = $cart->packaging - $cart_item->packaging + $cart_item->packaging;
        $cart->handling = $cart->handling - $cart_item->handling + $cart_item->handling;
        $cart->taxes = $cart->taxes - $cart_item->taxes + $cart_item->taxes;
        $cart->grand_total = $cart->grand_total - (($cart_item->discount * $cart_item->quantity) + $cart_item->shipping + $cart_item->packaging + $cart_item->handling + $cart_item->taxes) + $grand_total;
        $cart->shipping_weight = $cart->shipping_weight - $cart_item->shipping_weight + $cart_item->shipping_weight;
        $cart->save();

        $cart_item->quantity = $qtt;
        if ($request->service_date) {
            $cart_item->service_date = $request->service_date;
        }
        if ($request->service_time) {
            $cart_item->service_time = $request->service_time;
        }
        $cart_item->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Cart Updated Successfully!',
            'redirect' => url()->previous(),
            'updatedCart' => [
                'quantity' => $cart->quantity,
                'subtotal' => $cart->total,
                'discount' => $cart->discount,
                'grand_total' => $cart->grand_total,
            ]
        ]);
    }

    public function removeItem(Request $request)
    {
        // dd($request->all());
        $cart = Cart::find($request->cart_id);

        if (!$cart) {
            return response()->json([
                'error' => 'Cart not found!',
            ], 401);
        }

        $cart_item = CartItem::where('cart_id', $cart->id)->where('product_id', $request->product_id)->first();

        $cart->item_count = $cart->item_count - 1;
        $cart->quantity = $cart->quantity - $cart_item->quantity;
        $cart->total = $cart->total - ($cart_item->unit_price * $cart_item->quantity);
        $cart->discount = $cart->discount - (($cart_item->unit_price - $cart_item->discount) * $cart_item->quantity);
        $cart->shipping = $cart->shipping - $cart_item->shipping;
        $cart->packaging = $cart->packaging - $cart_item->packaging;
        $cart->handling = $cart->handling - $cart_item->handling;
        $cart->taxes = $cart->taxes - $cart_item->taxes;
        $cart->grand_total = $cart->grand_total - (($cart_item->discount * $cart_item->quantity) + $cart_item->shipping + $cart_item->packaging + $cart_item->handling + $cart_item->taxes);
        $cart->shipping_weight = $cart->shipping_weight - $cart_item->shipping_weight;
        $cart->save();

        $cart_item->delete();

        return response()->json([
            'status' => 'Deal Removed from Cart!',
            'cartItemCount' => $cart->item_count,
            'updatedCart' => [
                'quantity' => $cart->quantity,
                'subtotal' => $cart->total,
                'discount' => $cart->discount,
                'grand_total' => $cart->grand_total
            ]
        ]);
    }

    public function getCartDropdown()
    {
        $carts = Cart::whereNull('customer_id')
            ->where('ip_address', request()->ip());

        if (Auth::check()) {
            $carts = $carts->orWhere('customer_id', Auth::id());
        }

        $carts = $carts->with(['items.product.productMedia:id,resize_path,order,type,imageable_id'])->first();

        // // Cleanup invalid items for each cart
        // if ($carts) {
        //     $this->cleanUpCart($carts);
        // }

        $html = view('nav.cartdropdown', compact('carts'))->render();

        return response()->json([
            'html' => $html
        ]);
    }


    public function cartSummary($cart_id, Request $request)
    {
        if (!Auth::check()) {
            session(['url.intended' => route('cart.address', ['cart_id' => $cart_id])]);
            return redirect()->route("login");
        }

        $user = Auth::user();
        $carts = Cart::where('id', $cart_id)->with(['items.product'])->first();

        if (!$carts) {
            return redirect()->route('cart.index')->with('error', 'Cart not found.');
        }

        $minServiceDate = now()->addDays(2)->format('Y-m-d');

        foreach ($carts->items as $item) {
            if ($item->product->deal_type == 2) {
                if (empty($item->service_date) || empty($item->service_time)) {
                    return redirect()->route('cart.index')->with('error', 'Please select a service date and time for all service-type products.');
                }

                if ($item->service_date < $minServiceDate) {
                    return redirect()->route('cart.index')->with('error', 'Service date must be at least 2 days from today.');
                }
            }
        }

        $addresses = Addresses::where('user_id', $user->id)->get();

        return view('cartsummary', compact('carts', 'user', 'addresses'));
    }


    public function getCartItem(Request $request)
    {
        $product_ids = $request->input('product_ids');

        if (!$product_ids) {
            return response()->json([
                'status' => 'error',
                'message' => 'No product IDs provided.',
            ]);
        }

        $products = Product::whereIn('id', $product_ids)
            ->with(['shop', 'productMedia'])
            ->get();

        $cartQuery = Cart::whereNull('customer_id')->where('ip_address', $request->ip());

        if (Auth::guard()->check()) {
            $cartQuery = $cartQuery->orWhere('customer_id', Auth::guard()->user()->id);
        }

        $cart = $cartQuery->first();

        $products = $products->map(function ($product) use ($cart) {
            $image = $product->productMedia->isNotEmpty() ? $product->productMedia->first() : null;
            $product->image = $image ? asset($image->resize_path) : asset('assets/images/home/noImage.webp');

            $product->quantity = 1;

            if ($cart) {
                $cartItem = $cart->items()->where('product_id', $product->id)->first();
                if ($cartItem) {
                    $product->quantity = $cartItem->quantity;
                }
            }

            return $product;
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Cart Items Fetched Successfully!',
            'data' => $products,
        ]);
    }

    // private function cleanUpCart($cart)
    // {
    //     CartHelper::cleanUpCart($cart);
    // }
}

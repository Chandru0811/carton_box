<?php

namespace App\Http\Controllers\Api;

use App\Helpers\CartHelper;
use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\Addresses;
use App\Models\Bookmark;
use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\SavedItem;


class CartController extends Controller
{
    public function index(Request $request)
    {
        $carts = Cart::where('ip_address', $request->ip());

        if (Auth::guard()->check()) {
            $carts = $carts->orWhere('customer_id', Auth::guard()->user()->id);
        }

        $cart = $carts->first();

        // // Cleanup invalid items for the cart
        // if ($cart) {
        //     $this->cleanUpCart($cart);
        // }

        $bookmarkedProducts = collect();

        if ($cart) {
            $cart->load(['items.product.shop', 'items.product.productMedia:id,resize_path,order,type,imageable_id']);
        }

        $savedItems = collect();

        $user_id = Auth::check() ? Auth::user()->id : null;

        if (!$user_id) {
            $savedItems = SavedItem::where('ip_address', request()->ip())
                ->whereHas('deal', function ($query) {
                    $query->where('active', 1)->whereNull('deleted_at');
                })
                ->with('deal.productMedia:id,resize_path,order,type,imageable_id', 'deal.shop')
                ->get();
        } else {
            $savedItems = SavedItem::where('user_id', $user_id)
                ->whereHas('deal', function ($query) {
                    $query->where('active', 1)->whereNull('deleted_at');
                })
                ->with('deal.productMedia:id,resize_path,order,type,imageable_id', 'deal.shop')
                ->get();
        }

        return view('cart', compact('cart', 'bookmarkedProducts', 'savedItems'));
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

        $savedItem = null;
        if ($customer_id) {
            $savedItem = SavedItem::where('user_id', $customer_id)
                ->where('deal_id', $product->id)
                ->first();
        } else {
            $savedItem = SavedItem::where('ip_address', $request->ip())
                ->where('deal_id', $product->id)
                ->first();
        }

        if ($savedItem) {
            $savedItem->delete();
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

    public function saveForLater(Request $request)
    {
        $cartnumber = $request->input('cartnumber');
        if ($cartnumber == null) {
            $cartnumber = session()->get('cartnumber');
        }
        $customer_id = Auth::check() ? Auth::user()->id : null;

        $cart = Cart::where('cart_number', $cartnumber);

        if (Auth::guard()->check()) {
            $cart = $cart->orWhere('customer_id', Auth::guard()->user()->id);
        }
        $cart = $cart->first();



        $cartItem = null;

        if ($cart) {
            $cartItem = CartItem::where('cart_id', $cart->id)
                ->where('product_id', $request->product_id)
                ->first();
        }

        if ($cartItem) {
            // Add to Save for Later
            SavedItem::create([
                'user_id' => $customer_id,
                'ip_address' => $request->ip(),
                'deal_id' => $cartItem->product_id,
                'cart_number' => $cartnumber,
            ]);

            $deal = Product::with(['productMedia:id,resize_path,order,type,imageable_id', 'shop'])->find($cartItem->product_id);

            // Remove from Cart
            $cartItem->delete();

            //update cart
            $cart->item_count = $cart->item_count - 1;
            $cart->quantity = $cart->quantity - $cartItem->quantity;
            $cart->total = $cart->total - ($cartItem->unit_price * $cartItem->quantity);
            $cart->discount = $cart->discount - (($cartItem->unit_price - $cartItem->discount) * $cartItem->quantity);
            $cart->shipping = $cart->shipping - $cartItem->shipping;
            $cart->packaging = $cart->packaging - $cartItem->packaging;
            $cart->handling = $cart->handling - $cartItem->handling;
            $cart->taxes = $cart->taxes - $cartItem->taxes;
            $cart->grand_total = $cart->grand_total - (($cartItem->discount * $cartItem->quantity) + $cartItem->shipping + $cartItem->packaging + $cartItem->handling + $cartItem->taxes);
            $cart->shipping_weight = $cart->shipping_weight - $cartItem->shipping_weight;
            $cart->save();

            return response()->json([
                'status' => 'Item moved to Buy Later',
                'cartItemCount' => $cart->item_count,
                'deal' => $deal,
                'updatedCart' => [
                    'quantity' => $cart->quantity,
                    'subtotal' => $cart->total,
                    'discount' => $cart->discount,
                    'grand_total' => $cart->grand_total
                ]
            ]);
        }

        return response()->json([
            'error' => 'Item not found in cart',
        ], 401);
    }

    public function moveToCart(Request $request)
    {
        $product_id = $request->input('product_id');

        $product = Product::where('id', $product_id)->first();
        if (!$product) {
            return response()->json(['error' => 'Deal not found!'], 404);
        }
        $qtt = $request->quantity;
        if ($qtt == null) {
            $qtt = 1;
        }
        $user_id = Auth::check() ? Auth::user()->id : null;

        if (!$user_id) {
            $savedItem = SavedItem::where('ip_address', $request->ip())
                ->where('deal_id', $request->product_id)
                ->first();

            $cart = Cart::whereNull('customer_id')->where('ip_address', $request->ip())->first();
        } else {
            $savedItem = SavedItem::where('user_id', $user_id)
                ->where('deal_id', $request->product_id)
                ->first();

            $cart = Cart::where('customer_id', $user_id)->first();
        }

        if (!$cart) {
            $grand_total = $product->discounted_price * $qtt + $request->shipping + $request->packaging + $request->handling + $request->taxes;
            $discount = ($product->original_price - $product->discounted_price) * $qtt;

            $cart = new Cart();
            $cart->customer_id  = Auth::user()->id;
            $cart->ip_address = $request->ip();
            $cart->item_count = 1;
            $cart->quantity = $qtt;
            $cart->total = $product->original_price * $qtt;
            $cart->discount =  $discount;
            $cart->shipping = $request->shipping;
            $cart->packaging = $request->packaging;
            $cart->handling = $request->handling;
            $cart->taxes = $request->taxes;
            $cart->grand_total = $grand_total;
            $cart->shipping_weight = $request->shipping_weight;
            $cart->save();

            $item = new CartItem;
            $item->cart_id = $cart->id;
            $item->product_id = $product->id;
            $item->item_description = $product->name;
            $item->quantity = $qtt;
            $item->unit_price = $product->original_price;
            $item->delivery_date = $request->delivery_date;
            $item->coupon_code = $product->coupon_code;
            $item->discount = $product->discounted_price;
            $item->discount_percent = $product->discount_percentage;
            $item->seller_id = $product->shop_id;
            $item->deal_type = $product->deal_type;
            $item->service_date = $request->service_date;
            $item->service_time = $request->service_time;
            $item->shipping = $request->shipping;
            $item->packaging = $request->packaging;
            $item->handling = $request->handling;
            $item->taxes = $request->taxes;
            $item->shipping_weight = $request->shipping_weight;
            $item->save();

            $savedItem->delete();

            $item->load(['product.productMedia:id,resize_path,order,type,imageable_id', 'product.shop']);
        } else {
            $item = CartItem::create([
                'cart_id' => $cart->id,
                'item_description' => $savedItem->deal->name,
                'quantity' => 1, // Default quantity
                'unit_price' => $savedItem->deal->original_price,
                'coupon_code' => $savedItem->deal->coupon_code,
                'discount' => $savedItem->deal->discounted_price,
                'discount_percent' => $savedItem->deal->discount_percentage,
                'seller_id' => $savedItem->deal->shop_id,
                'product_id' => $savedItem->deal_id,
                'deal_type' => $savedItem->deal->deal_type,
            ]);

            $savedItem->delete();

            //update cart
            $cart->item_count = $cart->item_count + 1;
            $cart->quantity = $cart->quantity + 1;
            $cart->total = $cart->total + $savedItem->deal->original_price;
            $cart->discount = $cart->discount + ($savedItem->deal->original_price - $savedItem->deal->discounted_price);
            $cart->shipping = $cart->shipping + 0;
            $cart->packaging = $cart->packaging + 0;
            $cart->handling = $cart->handling + 0;
            $cart->taxes = $cart->taxes + 0;
            $cart->grand_total = $cart->grand_total + $savedItem->deal->discounted_price;
            $cart->shipping_weight = $cart->shipping_weight + 0;
            $cart->save();

            $item->load(['product.productMedia:id,resize_path,order,type,imageable_id', 'product.shop']);
        }

        return response()->json([
            'status' => 'Item moved to Cart',
            'cartItemCount' => $cart->item_count,
            'item' => $item,
            'updatedCart' => [
                'quantity' => $cart->quantity,
                'subtotal' => $cart->total,
                'discount' => $cart->discount,
                'grand_total' => $cart->grand_total
            ]
        ]);
    }

    public function getsaveforlater(Request $request)
    {
        $user_id = Auth::check() ? Auth::user()->id : null;

        if (!$user_id) {
            $savedItems = SavedItem::where('ip_address', request()->ip())
                ->whereHas('deal', function ($query) {
                    $query->where('active', 1)->whereNull('deleted_at');
                })
                ->with('deal.productMedia:id,resize_path,order,type,imageable_id', 'deal.shop')
                ->get();
        } else {
            $savedItems = SavedItem::where('user_id', $user_id)
                ->whereHas('deal', function ($query) {
                    $query->where('active', 1)->whereNull('deleted_at');
                })
                ->with('deal.productMedia:id,resize_path,order,type,imageable_id', 'deal.shop')
                ->get();
        }
        if ($request->ajax()) {
            return response()->json(['html' => view('savelater', compact('savedItems'))->render()]);
        }

        return view('savelater', compact('savedItems'));
    }

    public function removeFromSaveLater(Request $request)
    {
        $user_id = Auth::check() ? Auth::user()->id : null;

        if (!$user_id) {
            $savedItem = SavedItem::where('ip_address', $request->ip())
                ->where('deal_id', $request->product_id)
                ->first();
        } else {
            $savedItem = SavedItem::where('user_id', $user_id)
                ->where('deal_id', $request->product_id)
                ->first();
        }

        if (!$savedItem) {
            return response()->json([
                'error' => 'Item not found in Save for Later',
            ], 404);
        }

        $savedItem->delete();

        return response()->json([
            'status' => 'Item removed from Buy Later',
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

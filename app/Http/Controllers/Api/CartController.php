<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\ApiResponses;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\Addresses;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CartController extends Controller
{
    use ApiResponses;

    public function addtoCart(Request $request, $slug)
    {
        $cartnumber = $request->input("cartnumber");

        if ($cartnumber == null) {
            $cartnumber = session()->get('cartnumber');
        }
        $product = Product::where('slug', $slug)->first();

        if (!$product) {
            return $this->error('Deal not found!', [], 404);
        }

        $customer_id = Auth::guard('api')->check() ? Auth::guard('api')->id() : null;

        if ($customer_id == null) {
            if ($cartnumber == null) {
                $old_cart = null;
                $cartnumber = Str::uuid();
            } else {
                $old_cart = Cart::where('cart_number', $cartnumber)->first();
            }
        } else {
            $existing_cart = Cart::where('customer_id', $customer_id)->first();
            if ($existing_cart) {
                if ($existing_cart->cart_number !== $cartnumber) {

                    $new_cart = Cart::where('cart_number', $cartnumber)->whereNull('customer_id')->first();

                    foreach ($new_cart->items as $item) {
                        $existing_cart_item = CartItem::where('cart_id', $existing_cart->id)->where('product_id', $item->product_id)->first();

                        if ($existing_cart_item) {
                            // If the item exists in both carts, increase the quantity
                            $existing_cart_item->quantity += $item->quantity;
                            $existing_cart_item->save();
                        } else {
                            // Assign new cart items to the existing cart
                            $item->cart_id = $existing_cart->id;
                            $item->save();
                        }
                    }

                    // Update cart totals
                    $existing_cart->item_count += $new_cart->item_count;
                    $existing_cart->quantity += $new_cart->quantity;
                    $existing_cart->total += $new_cart->total;
                    $existing_cart->discount += $new_cart->discount;
                    $existing_cart->shipping += $new_cart->shipping;
                    $existing_cart->packaging += $new_cart->packaging;
                    $existing_cart->handling += $new_cart->handling;
                    $existing_cart->taxes += $new_cart->taxes;
                    $existing_cart->grand_total += $new_cart->grand_total;
                    $existing_cart->shipping_weight += $new_cart->shipping_weight;

                    $existing_cart->save();

                    $new_cart->delete();

                    $old_cart = Cart::where('customer_id', $customer_id)->first();
                } else {
                    $old_cart = Cart::where('customer_id', $customer_id)->first();
                }
            } else {
                if ($cartnumber == null) {
                    $old_cart = null;
                    $cartnumber = Str::uuid();
                } else {
                    $old_cart = Cart::where('customer_id', $customer_id)
                        ->orWhere(function ($q) use ($cartnumber) {
                            $q->whereNull('customer_id')
                                ->where('cart_number', $cartnumber);
                        })->first();
                }
            }
        }

        // Check if the item is already in the cart
        if ($old_cart) {
            $item_in_cart = CartItem::where('cart_id', $old_cart->id)->where('product_id', $product->id)->first();
            if ($item_in_cart) {
                return $this->error('Deal already in cart!', [], 400);
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
        $cart->cart_number = $cartnumber;
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

        session()->put('cartnumber', $cartnumber);

        return $this->success('Deal Added to Cart Successfully!', [
            'cart_number' => $cart->cart_number,
            'cart_item' => $cart_item
        ]);
    }

    public function getCart(Request $request)
    {
        $cartnumber = $request->input('cbg');

        if ($cartnumber == null) {
            $cartnumber = session()->get('cartnumber');
        }

        $customer_id = Auth::guard('api')->check() ? Auth::guard('api')->id() : null;

        if ($customer_id == null) {
            $cart = Cart::where('cart_number', $cartnumber)->first();
        } else {
            $cart = Cart::where('customer_id', $customer_id)->first();
            if ($cart == null) {
                $cart = Cart::where('cart_number', $cartnumber)->first();
            }
        }

        if ($cart) {
            $cart->load(['items.product.shop', 'items.product.productMedia:id,resize_path,order,type,imageable_id']);
        } else {
            $cart = [];
        }

        return $this->success('Cart Items Retrieved Successfully!', [
            'cart' => $cart,
        ]);
    }

    public function updateCart(Request $request)
    {
        $cartnumber = $request->input("cartnumber");

        if (!$cartnumber) {
            return $this->error('Cart number is required!', [], 400);
        }

        $cart = Cart::where('cart_number', $cartnumber)->first();

        if (!$cart) {
            return $this->error('Cart not found!', [], 404);
        }

        $cart_item = CartItem::where('cart_id', $cart->id)->where('product_id', $request->product_id)->first();

        if (!$cart_item) {
            return $this->error('Deal not found in cart!', [], 404);
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

        return $this->success('Cart Updated Successfully!', $cart_item);
    }

    public function removeItem(Request $request)
    {
        $cartnumber = $request->input("cartnumber");

        if (!$cartnumber) {
            return $this->error('Cart number is required!', [], 400);
        }

        $cart = Cart::where('cart_number', $cartnumber)->first();

        if (!$cart) {
            return $this->error('Cart not found!', [], 404);
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

        return $this->ok('Deal Removed from Cart Successfully!');
    }

    public function totalItems(Request $request)
    {
        $cartnumber = $request->input("dmc");

        if ($cartnumber == null) {
            $cartnumber = session()->get('cartnumber');
        }

        $customer_id = Auth::guard('api')->check() ? Auth::guard('api')->id() : null;

        if ($customer_id == null) {
            $cart = Cart::where('cart_number', $cartnumber)->first();
        } else {
            $cart = Cart::where('customer_id', $customer_id)->first();
            if ($cart == null) {
                $cart = Cart::where('cart_number', $cartnumber)->first();
            }
        }

        $itemCount = $cart ? $cart->item_count : 0;

        return $this->success('Total Items in Cart Retrieved Successfully!', $itemCount);
    }


    public function cartSummary($cart_id, Request $request)
    {
        if (!Auth::guard('api')->check()) {
            return $this->error('User is not authenticated. Redirecting to login.', null, 401);
        } else {
            $user = Auth::guard('api');

            $carts = Cart::where('id', $cart_id)->with(['items.product.productMedia:id,resize_path,order,type,imageable_id'])->first();

            $addresses = Addresses::where('user_id', $user->id)->get();

            return $this->success('Cart Summary Details Retrived Successfully', [
                'user' => $user,
                'carts' => $carts,
                'addresses' => $addresses,
            ]);
        }
    }
}

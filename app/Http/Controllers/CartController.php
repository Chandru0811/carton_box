<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Address;
use Illuminate\Http\Request;
use App\Traits\ApiResponses;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use App\Models\SavedItem;
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
        $cartnumber = $request->input('dmc');

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

        $savedItems = collect([]);

        if ($customer_id) {
            $savedItems = SavedItem::where('user_id', $customer_id)
                ->whereHas('deal', function ($query) {
                    $query->where('active', 1)->whereNull('deleted_at');
                })
                ->with('deal.productMedia:id,resize_path,order,type,imageable_id', 'deal.shop')
                ->get();
        } else {
            $savedItems = SavedItem::where('cart_number', $cartnumber)
                ->whereHas('deal', function ($query) {
                    $query->where('active', 1)->whereNull('deleted_at');
                })
                ->with('deal.productMedia:id,resize_path,order,type,imageable_id', 'deal.shop')
                ->get();
        }

        return $this->success('Cart Items Retrieved Successfully!', [
            'cart' => $cart,
            'savedItems' => $savedItems
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

    public function saveForLater(Request $request)
    {
        $cartnumber = $request->input('cartnumber');

        if ($cartnumber == null) {
            $cartnumber = session()->get('cartnumber');
        }

        $customer_id = Auth::guard('api')->check() ? Auth::guard('api')->id() : null;

        $cart = Cart::where('cart_number', $cartnumber);

        if (Auth::guard('api')->check()) {
            $cart = $cart->orWhere('customer_id', Auth::guard('api')->id);
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

            return $this->success('Deal Removed from Cart Successfully!', $deal);
        }

        return $this->error('Item not found in cart!', [], 401);
    }

    public function moveToCart(Request $request)
    {
        $cartnumber = $request->input('cartnumber');

        if ($cartnumber == null) {
            $cartnumber = session()->get('cartnumber');
        }

        $product_id = $request->input('product_id');

        $product = Product::where('id', $product_id)->first();

        if (!$product) {
            return $this->error('Deal not found!', [], 404);
        }

        $qtt = $request->quantity;

        if ($qtt == null) {
            $qtt = 1;
        }
        
        $user_id = Auth::guard('api')->check() ? Auth::guard('api')->id() : null;

        if (!$user_id) {
            $savedItem = SavedItem::where('cart_number', $cartnumber)
                ->where('deal_id', $request->product_id)
                ->first();

            $cart = Cart::whereNull('customer_id')->where('cart_number', $cartnumber)->first();
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
            $cart->customer_id  = Auth::guard('api')->id();
            $cart->ip_address = $request->ip();
            $cart->cart_number = $cartnumber;
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

        return $this->success('Item moved to Cart', $item);
    }

    public function getsaveforlater(Request $request)
    {
        $cartnumber = $request->input('cartnumber');

        if ($cartnumber == null) {
            $cartnumber = session()->get('cartnumber');
        }

        $user_id = Auth::guard('api')->check() ? Auth::guard('api')->id() : null;

        if (!$user_id) {
            $savedItems = SavedItem::where('cart_number', $cartnumber)
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

        return $this->success('Saved items Retrieved Successfully!', $savedItems);
    }

    public function removeFromSaveLater(Request $request)
    {
        $cartnumber = $request->input('cartnumber');

        if ($cartnumber == null) {
            $cartnumber = session()->get('cartnumber');
        }

        $user_id = Auth::guard('api')->check() ? Auth::guard('api')->id() : null;

        if (!$user_id) {
            $savedItem = SavedItem::where('cart_number', $cartnumber)
                ->where('deal_id', $request->product_id)
                ->first();
        } else {
            $savedItem = SavedItem::where('user_id', $user_id)
                ->where('deal_id', $request->product_id)
                ->first();
        }

        if (!$savedItem) {
            return $this->error('Item not found in Save for Later', [], 404);
        }

        $savedItem->delete();
        
        return $this->ok('Item removed from Buy for Later');
    }

    public function cartSummary($cart_id, Request $request)
    {
        if (!Auth::guard('api')->check()) {
            return $this->error('User is not authenticated. Redirecting to login.', null, 401);
        } else {
            $user = Auth::guard('api');

            $carts = Cart::where('id', $cart_id)->with(['items.product.productMedia:id,resize_path,order,type,imageable_id'])->first();

            $addresses = Address::where('user_id', $user->id)->get();

            return $this->success('Cart Summary Details Retrived Successfully', [
                'user' => $user,
                'carts' => $carts,
                'addresses' => $addresses,
            ]);
        }
    }
}

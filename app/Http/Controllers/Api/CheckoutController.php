<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Addresses;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItems;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Traits\ApiResponses;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    use ApiResponses;

    public function checkoutsummary($id, Request $request)
    {
        if (!Auth::guard('api')->check()) {
            return $this->error('User is not authenticated. Redirecting to login.', null, 401);
        } else {
            $user = Auth::guard('api')->user();
            $products = Product::with(['productMedia:id,resize_path,order,type,imageable_id', 'shop'])->where('id', $id)->where('active', 1)->get();
            if (!$products) {
                return $this->error('Product not found or inactive.', null, 404);
            }

            $carts = Cart::whereNull('customer_id')->where('ip_address', $request->ip());
            if (Auth::guard('api')->check()) {
                $carts = $carts->orWhere('customer_id', Auth::guard('api')->user()->id);
            }

            $carts = $carts->first();

            if ($carts) {
                $carts->load(['items' => function ($query) use ($id) {
                    $query->where('product_id', '!=', $id);
                }, 'items.product.productMedia:id,resize_path,order,type,imageable_id', 'items.product.shop']);
            }


            $addresses = Addresses::where('user_id', $user->id)->get();

            return $this->success('Summary Details Retrived Successfully', [
                'products' => $products,
                'user' => $user,
                'carts' => $carts,
                'addresses' => $addresses,
            ]);
        }
    }

    public function directCheckout(Request $request)
    {
        $product_ids = $request->input('product_ids');
        $ids = json_decode($product_ids);
        $address_id = $request->input('address_id');
        $cart_id = $request->input('cart_id');
        $address = Addresses::where('id', $address_id)->first();
        $orderoption = "buy now";
        $cart = Cart::where('id', $cart_id)->with('items')->first();
        if ($cart) {
            $cart->load(['items' => function ($query) use ($ids) {
                $query->whereIn('product_id', $ids);
            }]);
        }

        if (!Auth::guard('api')->check()) {
            return $this->error('User is not authenticated. Redirecting to login.', null, 401);
        } else {
            $user = Auth::guard('api')->user();
            $order = Order::where('customer_id', $user->id)->orderBy('id', 'desc')->first();
            $orderoption = 'buynow';
            return $this->success('Direct Checkout Data Retrieved Successfully!', [
                'cart' => $cart,
                'product_ids' => $product_ids,
                'user' => $user,
                'order' => $order,
                'orderoption' => $orderoption,
                'address' => $address
            ]);
        }
    }

    public function cartcheckout($cart_id, Request $request)
    {
        $address_id = $request->address_id;

        $address = Addresses::where('id', $address_id)->first();

        $cart = Cart::where('id', $cart_id)->with('items')->first();
        if (!$cart) {
            return $this->error('Cart not found.', [], 400);
        }

        if (!Auth::guard('api')->check()) {
            return $this->error('User is not authenticated. Redirecting to login.', null, 401);
        } else {
            $user = Auth::guard('api')->user();
            $order = Order::where('customer_id', $user->id)->orderBy('id', 'desc')->first();
            $orderoption = 'cart';

            return $this->success('Cart Checkout Data Retrieved Successfully!', [
                'cart' => $cart,
                'user' => $user,
                'order' => $order,
                'orderoption' => $orderoption,
                'address' => $address
            ]);
        }
    }

    public function createorder(Request $request)
    {
        $user_id = Auth::guard('api')->check() ? Auth::guard('api')->id() : null;;
        $cart_id = $request->input('cart_id');
        $product_ids = $request->input('product_ids');
        if ($product_ids != null && $cart_id != null) {
            // Handle direct order
            $ids = json_decode($product_ids);
            $cart = Cart::where('id', $cart_id)->with('items')->first();
            if ($cart) {
                $cart->load(['items' => function ($query) use ($ids) {
                    $query->whereIn('product_id', $ids);
                }]);
            }

            if (!$cart) {
                return $this->error('Cart not found.', [], 400);
            }

            // Create order for the cart items
            $latestOrder = Order::orderBy('id', 'desc')->first();
            $customOrderId = $latestOrder ? intval(Str::after($latestOrder->id, '-')) + 1 : 1;
            $orderNumber = 'DEALSLAH_O' . $customOrderId;

            $itemCount = $cart->items->whereIn('product_id', $ids)->sum('quantity');
            $total = $cart->items->whereIn('product_id', $ids)->sum('total');
            $discount = $cart->items->whereIn('product_id', $ids)->sum('discount');
            $shipping = $cart->items->whereIn('product_id', $ids)->sum('shipping');
            $packaging = $cart->items->whereIn('product_id', $ids)->sum('packaging');
            $handling = $cart->items->whereIn('product_id', $ids)->sum('handling');
            $taxes = $cart->items->whereIn('product_id', $ids)->sum('taxes');
            $grandTotal = $cart->items->whereIn('product_id', $ids)->sum('grand_total');
            $shippingWeight = $cart->items->whereIn('product_id', $ids)->sum('shipping_weight');

            $addressId = $request->input('address_id');
            $address = Addresses::find($addressId);

            if (!$address) {
                return $this->error('Cart not found.', [], 400);
            }

            $deliveryAddress = [
                'first_name' => $address->first_name,
                'last_name' => $address->last_name,
                'email' => $address->email,
                'phone' => $address->phone,
                'postalcode' => $address->postalcode,
                'address' => $address->address,
                'city' => $address->city,
                'state' => $address->state,
                'unit' => $address->unit
            ];

            $order = Order::create([
                'order_number'     => $orderNumber,
                'customer_id'      => $user_id,
                'item_count'       => $itemCount,
                'quantity'         => $itemCount,
                'total'            => $total,
                'discount'         => $discount,
                'shipping'         => $shipping,
                'packaging'        => $packaging,
                'handling'         => $handling,
                'taxes'            => $taxes,
                'grand_total'      => $grandTotal,
                'shipping_weight'  => $shippingWeight,
                'status'           => 1, //created
                'payment_type'     => $request->input('payment_type'),
                'payment_status'   => 1,
                'delivery_address' => json_encode($deliveryAddress)
            ]);

            foreach ($cart->items->whereIn('product_id', $ids) as $item) {
                $itemNumber = 'DL0' . $order->id . '-V' . $item->product->shop_id . 'P' . $item->product_id;

                OrderItems::create([
                    'order_id' => $order->id,
                    'item_number' => $itemNumber,
                    'product_id' => $item->product_id,
                    'seller_id' => $item->product->shop_id,
                    'item_description' => $item->item_description,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'delivery_date' => $item->delivery_date,
                    'coupon_code' => $item->coupon_code,
                    'discount' => $item->discount,
                    'discount_percent' => $item->discount_percent,
                    'deal_type' => $item->deal_type,
                    'service_date' => $item->service_date,
                    'service_time' => $item->service_time,
                    'shipping' => $item->shipping ?? 0,
                    'packaging' => $item->packaging ?? 0,
                    'handling' => $item->handling ?? 0,
                    'taxes' => $item->taxes ?? 0,
                    'shipping_weight' => $item->shipping_weight ?? 0,
                ]);
            }

            // Delete cart and cart items
            $cart->items()->whereIn('product_id', $ids)->delete();

            if ($cart->items->count() == 0) {
                $cart->delete();
            }
        } elseif ($cart_id != null && $product_ids == null) {
            // Handle cart order
            $cart = Cart::with('items')->where('id', $cart_id)->first();
            if (!$cart) {
                return $this->error('Cart not found.', [], 400);
            }

            // Create order for the cart items
            $latestOrder = Order::orderBy('id', 'desc')->first();
            $customOrderId = $latestOrder ? intval(Str::after($latestOrder->id, '-')) + 1 : 1;
            $orderNumber = 'DEALSLAH_O' . $customOrderId;
            $addressId = $request->input('address_id');
            $address = Addresses::find($addressId);

            if (!$address) {
                return $this->error('Cart not found.', [], 400);
            }

            $deliveryAddress = [
                'first_name' => $address->first_name,
                'last_name' => $address->last_name,
                'email' => $address->email,
                'phone' => $address->phone,
                'postalcode' => $address->postalcode,
                'address' => $address->address,
                'unit' => $address->unit
            ];

            $order = Order::create([
                'order_number'     => $orderNumber,
                'customer_id'      => $user_id,
                'item_count'       => $cart->item_count,
                'quantity'         => $cart->quantity,
                'total'            => $cart->total,
                'discount'         => $cart->discount,
                'shipping'         => $cart->shipping,
                'packaging'        => $cart->packaging,
                'handling'         => $cart->handling,
                'taxes'            => $cart->taxes,
                'grand_total'      => $cart->grand_total,
                'shipping_weight'  => $cart->shipping_weight,
                'status'           => 1, //created
                'payment_type'     => $request->input('payment_type'),
                'payment_status'   => 1,
                'delivery_address' => json_encode($deliveryAddress)
            ]);

            foreach ($cart->items as $item) {
                $itemNumber = 'DL0' . $order->id . '-V' . $item->product->shop_id . 'P' . $item->product_id;

                OrderItems::create([
                    'order_id' => $order->id,
                    'item_number' => $itemNumber,
                    'product_id' => $item->product_id,
                    'seller_id' => $item->product->shop_id,
                    'item_description' => $item->item_description,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'delivery_date' => $item->delivery_date,
                    'coupon_code' => $item->coupon_code,
                    'discount' => $item->discount,
                    'discount_percent' => $item->discount_percent,
                    'deal_type' => $item->deal_type,
                    'service_date' => $item->service_date,
                    'service_time' => $item->service_time,
                    'shipping' => $item->shipping ?? 0,
                    'packaging' => $item->packaging ?? 0,
                    'handling' => $item->handling ?? 0,
                    'taxes' => $item->taxes ?? 0,
                    'shipping_weight' => $item->shipping_weight ?? 0,
                ]);
            }
            // Delete cart and cart items
            $cart->items()->delete();
            $cart->delete();
        } else {
            return $this->error('Invalid request.', [], 400);
        }

        return $this->ok('Order Placed Successfully!');
    }

    public function getAllOrdersByCustomer()
    {
        $customerId = Auth::guard('api')->check() ? Auth::guard('api')->id() : null;;

        $orders = Order::where('customer_id', $customerId)
            ->with([
                'items.product' => function ($query) {
                    $query->select('id', 'name', 'description', 'original_price', 'discounted_price', 'discount_percentage', 'delivery_days')->with('productMedia:id,resize_path,order,type,imageable_id');
                },
                'items.shop' => function ($query) {
                    $query->select('id', 'name')->withTrashed();
                }
            ])
            ->orderBy('created_at', 'desc')
            ->get();

        return $this->success('Orders Retrieved Successfully!', $orders);
    }

    public function showOrderByCustomerId($id, $product_id)
    {
        $order = Order::with([
            'items' => function ($query) use ($product_id) {
                $query->where('product_id', $product_id);
            },
            'items.product.productMedia:id,resize_path,order,type,imageable_id',
            'items.shop' => function ($query) {
                $query->select('id', 'name', 'email', 'mobile', 'description', 'street', 'street2', 'city', 'zip_code', 'deleted_at')->withTrashed();
            }
        ])->find($id);

        if (!$order || Auth::guard('api')->id() !== $order->customer_id) {
            return $this->error('Invalid request.', [], 400);
        }


        return $this->success('Order Retrieved Successfully!', $order);
    }
}

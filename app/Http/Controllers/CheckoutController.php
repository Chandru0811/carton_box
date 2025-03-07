<?php

namespace App\Http\Controllers;

use App\Helpers\CartHelper;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItems;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderCreated;
use App\Models\Addresses;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Shop;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use DB;

class CheckoutController extends Controller
{

    public function checkoutsummary($id, Request $request)
    {
        if (!Auth::check()) {
            session(['url.intended' => route('checkout.summary')]);

            return redirect()->route("login");
        } else {
            $user = Auth::user();

            $products = Product::with(['productMedia:id,resize_path,order,type,imageable_id', 'shop'])->where('id', $id)->where('active', 1)->get();

            if (!$products) {
                return redirect()->route('home')->with('error', 'Product not found or inactive.');
            }

            $carts = Cart::whereNull('customer_id')->where('ip_address', $request->ip());

            if (Auth::guard()->check()) {
                $carts = $carts->orWhere('customer_id', Auth::guard()->user()->id);
            }

            $carts = $carts->first();



            $addresses = Addresses::where('user_id', $user->id)->get();
            // $order = Order::where('customer_id', $user->id)->orderBy('id', 'desc')->first();
            return view('summary', compact('products', 'user', 'carts', 'addresses'));
        }
    }

    public function directcheckout(Request $request)
    {
        // dd($request->all());
        $product_ids = $request->input('all_products_to_buy');
        $ids = json_decode($product_ids);
        $address_id = $request->input('address_id');
        $cart_id = $request->input('cart_id');
        $address = Addresses::where('id', $address_id)->first();
        $orderoption = "buy now";
        $cart = Cart::where('id', $cart_id)->with('items')->first();
        if ($cart) {
            $cart->load([
                'items' => function ($query) use ($ids) {
                    $query->whereIn('product_id', $ids);
                }
            ]);
        }

        if (!Auth::check()) {
            session(['url.intended' => route('checkout.direct')]);
            return redirect()->route("login");
        } else {
            $user = Auth::user();
            $order = Order::where('customer_id', $user->id)->orderBy('id', 'desc')->first();
            $orderoption = 'buynow';
            return view('checkout', compact('cart', 'user', 'address', 'order', 'orderoption', 'product_ids'));
        }
    }

    public function cartcheckout(Request $request)
    {
        $address_id = $request->address_id;
        $cart_id = $request->input('cart_id');
        $address = Addresses::where('id', $address_id)->first();
        $cart = Cart::where('id', $cart_id)->with('items')->first();
        if (!$cart) {
            return redirect()->route('home')->with('error', 'Cart not found.');
        }

        if (!Auth::check()) {
            session(['url.intended' => route('checkout.cart', ['cart_id' => $cart_id])]);
            return redirect()->route("login");
        } else {
            $user = Auth::user();
            $orderoption = 'cart';
            return view('checkout', compact('cart', 'user', 'address', 'orderoption'));
        }
    }

    public function createorder(Request $request)
    {
        $user_id = Auth::check() ? Auth::id() : null;
        $cart_id = $request->input('cart_id');
        $product_ids = $request->input('product_ids');

        if ($product_ids != null && $cart_id != null) {
            $ids = json_decode($product_ids);
            $cart = Cart::where('id', $cart_id)->with('items')->first();

            if (!$cart) {
                return redirect()->route('home')->with('error', 'Cart not found.');
            }

            $cart->load([
                'items' => function ($query) use ($ids) {
                    $query->whereIn('product_id', $ids);
                }
            ]);

            // Generate a custom order number
            $latestOrder = Order::orderBy('id', 'desc')->first();
            $customOrderId = $latestOrder ? intval(Str::after($latestOrder->id, '-')) + 1 : 1;
            $orderNumber = 'CARTONBOXGURU_O' . $customOrderId;

            $itemCount = $cart->items->whereIn('product_id', $ids)->sum('quantity');

            $total = $cart->items->whereIn('product_id', $ids)->sum(function ($item) {
                return $item->unit_price * $item->quantity;
            });
            $discount = $cart->items->whereIn('product_id', $ids)->sum(function ($item) {
                return ($item->unit_price - $item->discount) * $item->quantity;
            });
            $shipping = $cart->items->whereIn('product_id', $ids)->sum('shipping');
            $packaging = $cart->items->whereIn('product_id', $ids)->sum('packaging');
            $handling = $cart->items->whereIn('product_id', $ids)->sum('handling');
            $taxes = $cart->items->whereIn('product_id', $ids)->sum('taxes');
            $grandTotal = $total - $discount + $shipping + $packaging + $handling + $taxes;
            $shippingWeight = $cart->items->whereIn('product_id', $ids)->sum('shipping_weight');

            $addressId = $request->input('address_id');
            $address = Addresses::find($addressId);

            if (!$address) {
                return redirect()->route('home')->with('error', 'Address not found.');
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

            // Create the order
            $order = Order::create([
                'order_number' => $orderNumber,
                'customer_id' => $user_id,
                'item_count' => $itemCount,
                'quantity' => $itemCount,
                'total' => $total,
                'discount' => $discount,
                'shipping' => $shipping,
                'packaging' => $packaging,
                'handling' => $handling,
                'taxes' => $taxes,
                'grand_total' => $grandTotal,
                'shipping_weight' => $shippingWeight,
                'status' => 1, // Created
                'payment_type' => $request->input('payment_type'),
                'payment_status' => 1,
                'delivery_address' => json_encode($deliveryAddress)
            ]);

            // Create order items
            foreach ($cart->items->whereIn('product_id', $ids) as $item) {
                $itemNumber = 'CBG0' . $order->id . '-CBG' . $item->product->shop_id . 'P' . $item->product_id;

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

            // Delete ordered items from the cart
            foreach ($cart->items->whereIn('product_id', $ids) as $cart_item) {
                $cart->item_count -= 1; // Decrease item count
                $cart->quantity -= $cart_item->quantity; // Decrease total quantity
                $cart->total -= ($cart_item->unit_price * $cart_item->quantity); // Subtract total price
                $cart->discount -= (($cart_item->unit_price - $cart_item->discount) * $cart_item->quantity); // Subtract discount
                $cart->shipping -= $cart_item->shipping; // Subtract shipping cost
                $cart->packaging -= $cart_item->packaging; // Subtract packaging cost
                $cart->handling -= $cart_item->handling; // Subtract handling cost
                $cart->taxes -= $cart_item->taxes; // Subtract taxes
                $cart->grand_total -= (
                    ($cart_item->discount * $cart_item->quantity) +
                    $cart_item->shipping +
                    $cart_item->packaging +
                    $cart_item->handling +
                    $cart_item->taxes
                ); // Update grand total
                $cart->shipping_weight -= $cart_item->shipping_weight; // Subtract shipping weight

                $cart_item->delete(); // Delete the cart item
            }

            // Save updated cart or delete if empty
            if ($cart->item_count <= 0 || $cart->quantity <= 0) {
                $cart->delete();
            } else {
                $cart->save(); // Save updated cart
            }
        } elseif ($cart_id != null && $product_ids == null) {
            // Handle cart order
            $cart = Cart::with('items')->where('id', $cart_id)->first();
            if (!$cart) {
                return redirect()->route('home')->with('error', 'Cart not found.');
            }

            // Create order for the cart items
            $latestOrder = Order::orderBy('id', 'desc')->first();
            $customOrderId = $latestOrder ? intval(Str::after($latestOrder->id, '-')) + 1 : 1;
            $orderNumber = 'CARTONBOXGURU_O' . $customOrderId;
            $addressId = $request->input('address_id');
            $address = Addresses::find($addressId);

            if (!$address) {
                return redirect()->route('home')->with('error', 'Address not found.');
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
                'order_number' => $orderNumber,
                'customer_id' => $user_id,
                'item_count' => $cart->item_count,
                'quantity' => $cart->quantity,
                'total' => $cart->total,
                'discount' => $cart->discount,
                'shipping' => $cart->shipping,
                'packaging' => $cart->packaging,
                'handling' => $cart->handling,
                'taxes' => $cart->taxes,
                'grand_total' => $cart->grand_total,
                'shipping_weight' => $cart->shipping_weight,
                'status' => 1, //created
                'payment_type' => $request->input('payment_type'),
                'payment_status' => 1,
                'delivery_address' => json_encode($deliveryAddress)
            ]);

            foreach ($cart->items as $item) {
                $itemNumber = 'CBG0' . $order->id . '-CBG' . $item->product->shop_id . 'P' . $item->product_id;

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
            return redirect()->route('home')->with('error', 'Invalid request.');
        }

        // $shop = Shop::where('id',$product->shop_id)->first();
        //$customer = User::where('id', $user_id)->first();
        //$orderdetails = Order::where('id', $order->id)->with(['items'])->first();

        // Mail::to($shop->email)->send(new OrderCreated($shop,$customer,$orderdetails));
        // dd($order);

        $decodedAddress = json_decode($order->delivery_address, true);

        $addressFields = [
            'address' => $decodedAddress['address'] ?? '',
            'postal_code' => $decodedAddress['postal_code'] ?? '',
            'unit' => $decodedAddress['unit'] ?? ''
        ];

        $formattedAddress = implode(", ", array_filter([
            $addressFields['address'],
            $addressFields['postal_code'],
        ]));

        if ($addressFields['unit']) {
            $formattedAddress .= " - " . $addressFields['unit'];
        }

        $message = [
            'order' => "Order Placed Successfully !",
            'delivery' => "Delivering to",
            'address' => $formattedAddress
        ];

        return redirect()->route('home')->with('status1', $message);
    }

    public function getAllOrdersByCustomer()
    {
        $customerId = Auth::check() ? Auth::id() : null;

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

        return view('orders', compact('orders'));
    }

    public function showOrderByCustomerId($id, $product_id)
    {
        $order = Order::with([
            'items' => function ($query) use ($product_id) {
                $query->where('product_id', $product_id);
            },
            'items.product.productMedia:id,resize_path,order,type,imageable_id',
            'items.shop' => function ($query) {
                $query->select('id', 'name', 'email', 'mobile', 'description', 'street', 'street2', 'city', 'zip_code', 'legal_name', 'deleted_at')->withTrashed();
            }
        ])->find($id);

        if (!$order || Auth::id() !== $order->customer_id) {
            return view('orderView', ['order' => null]);
        }

        $orderReviewedByUser = false;

        if ($order->items->count() > 0) {
            foreach ($order->items as $item) {
                // Check if product exists
                if ($item->product && $item->product->review) {
                    $review = $item->product->review->firstWhere('user_id', Auth::id());

                    if ($review) {
                        $orderReviewedByUser = true;
                        break;
                    }
                }
            }
        }
        // dd($order);

        return view('orderView', compact('order', 'orderReviewedByUser'));
    }

    public function orderInvoice($id)
    {
        $order = Order::with('items', 'shop')->find($id);

        if (!$order || Auth::id() !== $order->customer_id) {
            return redirect()->route('orders')->with('error', 'Order not found or unauthorized access.');
        }

        $logoPath = public_path('assets/images/home/header-logo.webp');
        $logoBase64 = null;

        if (file_exists($logoPath)) {
            $logoBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));
        } else {
            $logoBase64 = 'https://cartonBoxGuru.com/assets/images/home/header-logo.webp';
        }

        $data = [
            'order' => $order,
            'items' => $order->items,
            'shop' => $order->shop,
            'logo' => $logoBase64,
        ];

        $pdf = Pdf::loadView('orderinvoice', $data)->setOptions(['isRemoteEnabled' => true]);

        $fileName = $order->order_number . '.pdf';
        return $pdf->download($fileName);
    }
}

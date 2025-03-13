<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItems;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Traits\ApiResponses;

class UserController extends Controller
{
    use ApiResponses;

    public function getAllUser()
    {
        $users = User::where('role', 3)->orderBy('created_at', 'desc')->get();
        return $this->success('User retrieved successfully.', $users);
    }

    public function userShow($id)
    {
        $user = User::where('role', 3)->find($id);

        if (!$user) {
            return $this->error('User Not Found.', ['error' => 'User Not Found'], 404);
        }

        return $this->success('User Retrived Succesfully!', $user);
    }

    public function getAllOrders()
    {
        $orderItems = OrderItems::with([
            'order' => function ($query) {
                $query->select('id', 'order_number', 'customer_id', 'created_at');
            },
            'order.customer' => function ($query) {
                $query->select('id', 'name');
            },
            'shop' => function ($query) {
                $query->select('id', 'legal_name');
            }
        ])->orderBy('created_at', 'desc')->get();

        return $this->success('Order Items Retrieved Successfully', $orderItems);
    }


    public function getOrderById($order_id, $product_id)
    {
        $orderItem = OrderItems::with([
            'product.productMedia:id,resize_path,order,type,imageable_id',
            'shop',
            'order.customer',
            'order.address'
        ])
            ->where('order_id', $order_id)
            ->where('product_id', $product_id)
            ->first();

        if (!$orderItem) {
            return $this->error('Order Item Not Found.', ['error' => 'Order Item Not Found']);
        }
        OrderItems::where('order_id', $order_id)
            ->where('product_id', $product_id)
            ->update(['viewed_by_admin' => 0]);

        return $this->success('Order Item Retrieved Successfully', $orderItem);
    }

    public function getAllReferrersAndReferrerVendors()
    {
        $users = User::whereIn('type', ['referrer', 'referrer-vendor'])
            ->orderBy('created_at', 'desc')
            ->get(['id', 'name', 'type']);

        return $this->success('Referrers and Referrer Vendors retrieved successfully.', $users);
    }

    public function getReferralsByUserId($userId)
    {
        $referralCode = 'DLR500' . $userId;

        $referrals = User::where('referral_code', $referralCode)
            ->orderBy('created_at', 'desc')
            ->get(['id', 'name', 'referral_code', 'shop_id', 'created_at']);

        if ($referrals->isEmpty()) {
            return $this->success('Referral list retrieved successfully.', []);
        }

        return $this->success('Referral list retrieved successfully.', $referrals);
    }

    public function getAllProductWithIds()
    {
        $products = Product::where('active', 1)->orderBy('id', 'desc')->get(['id', 'name'])->map(function ($product) {
            return [
                'value' => $product->id,
                'label' => $product->name,
            ];
        });

        return $this->success('Product list retrieved successfully.', $products);
    }

    public function updateProductOrder(Request $request)
    {
        $orders = $request->all();

        Product::query()->update(['order' => null]);

        $processedProducts = [];
        $currentOrder = 1;

        foreach ($orders as $order) {
            $productId = $order['product_id'];

            if (!in_array($productId, $processedProducts)) {
                Product::where('id', $productId)->update(['order' => $currentOrder]);

                $processedProducts[] = $productId;
                $currentOrder++;
            }
        }

        return $this->success('Product order updated successfully.', $processedProducts);
    }



    public function getOrderedProducts()
    {
        $products = Product::whereNotNull('order')
            ->orderBy('order', 'asc')
            ->get(['id', 'name', 'order'])
            ->map(function ($product) {
                return [
                    'value' => $product->id,
                    'label' => $product->name,
                    'order' => $product->order,
                ];
            });

        return $this->success('Ordered product list retrieved successfully.', $products);
    }
}

<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItems;
use App\Models\Shop;
use App\Models\ShopHour;
use App\Models\ShopPolicy;
use App\Models\User;
use Illuminate\Http\Request;
use App\Traits\ApiResponses;
use Illuminate\Support\Facades\Validator;

class ShopController extends Controller
{
    use ApiResponses;

    public function showshopdetails($id)
    {
        $shop = Shop::select('name', 'legal_name', 'company_registeration_no', 'slug', 'email', 'mobile', 'external_url', 'shop_type', 'logo', 'banner', 'shop_ratings', 'description')->where('id', $id)->first();
        return $this->success('Shop Details Retrieved Successfully!', $shop);
    }

    public function showshoplocation($id)
    {
        $shop = Shop::select('street', 'street2', 'city', 'zip_code', 'state', 'country', 'address', 'map_url', 'shop_lattitude', 'shop_longtitude')->where('id', $id)->first();
        return $this->success('Shop Location Retrieved Successfully!', $shop);
    }

    public function showshoppayment($id)
    {
        $shop = Shop::select('payment_id', 'account_holder', 'account_type', 'account_number', 'bank_name', 'bank_address', 'bank_code')->where('id', $id)->first();
        return $this->success('Shop Payment Retrieved Successfully!', $shop);
    }

    public function updateshopdetails(Request $request, $id)
    {
        $shop = Shop::find($id);
        if (!$shop) {
            return $this->error('Shop Not Found.', ['error' => 'Shop Not Found']);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|unique:shops,name,' . $id,
            'company_registeration_no' => 'sometimes|required|string',
            'legal_name' => 'sometimes|required|string',
            'slug' => 'sometimes|required|string|unique:shops,slug,' . $id,
            'email' => 'sometimes|required|email|unique:shops,email,' . $id,
            'mobile' => 'sometimes|required|string|unique:shops,mobile,' . $id,
            'description' => 'sometimes|required|string',
            'external_url' => 'nullable',
            'logo' => (!$shop->logo ? 'required|' : 'sometimes|') . 'image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            // 'banner' => (!$shop->banner ? 'required|' : 'sometimes|') . 'image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            // 'map_url' => (!$shop->map_url ? 'required|' : 'sometimes|') . 'url'
        ], [
            'name.required' => 'The name field is required.',
            'name.unique' => 'The name field must be unique.',
            'company_registeration_no.required' => 'The company registeration number field is required.',
            'legal_name.required' => 'The legal name field is required.',
            'slug.required' => 'The slug field is required.',
            'slug.unique' => 'The slug must be unique.',
            'email.required' => 'The email field is required.',
            'email.email' => 'The email must be a valid email address.',
            'email.unique' => 'The email must be unique.',
            'description.required' => 'The description field is required.',
            'external_url.url' => 'The website URL must be a valid URL.',
            'mobile.required' => 'The mobile number is required.',
            'mobile.unique' => 'Mobile number already exists',
            'logo.required' => 'The logo field is required.',
            'logo.image' => 'The logo must be an image.',
            'logo.mimes' => 'The logo must be a file of type: jpeg, png, jpg, gif, svg, or webp file.',
            'logo.max' => 'The logo may not be greater than 2048 kilobytes.',
            // 'banner.required' => 'The banner field is required.',
            // 'banner.image' => 'The banner must be an image.',
            // 'banner.mimes' => 'The banner must be a file of type: jpeg, png, jpg, gif, svg, or webp file.',
            // 'banner.max' => 'The banner may not be greater than 2048 kilobytes.',
            // 'map_url.required' => 'Map URL is required.',
            // 'map_url.url' => 'The map URL must be a valid URL.',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $updateData = array_filter($request->only([
            'name',
            'legal_name',
            'company_registeration_no',
            'slug',
            'email',
            'mobile',
            'external_url',
            'shop_type',
            'logo',
            'banner',
            'shop_ratings',
            'description',
        ]), fn($value) => !is_null($value));

        if ($request->hasFile('logo')) {
            if ($shop->logo && file_exists(public_path($shop->logo))) {
                unlink(public_path($shop->logo));
            }

            $image = $request->file('logo');
            $imagePath = 'assets/images/shops/' . $shop->id . '/logo';

            if (!file_exists($imagePath)) {
                mkdir($imagePath, 0755, true);
            }

            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->move($imagePath, $imageName);

            $updateData['logo'] = $imagePath . '/' . $imageName;
        }

        if ($request->hasFile('banner')) {
            if ($shop->banner && file_exists(public_path($shop->banner))) {
                unlink(public_path($shop->banner));
            }

            $image = $request->file('banner');
            $imagePath = 'assets/images/shops/' . $shop->id . '/banner';

            if (!file_exists($imagePath)) {
                mkdir($imagePath, 0755, true);
            }

            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->move($imagePath, $imageName);

            $updateData['banner'] = $imagePath . '/' . $imageName;
        }

        $shop->update($updateData);

        return $this->success('Shop Details Updated Successfully!', $shop);
    }

    public function updateshoplocation(Request $request, $id)
    {
        $shop = Shop::find($id);
        if (!$shop) {
            return $this->error('Shop Not Found.', ['error' => 'Shop Not Found']);
        }

        $validator = Validator::make($request->all(), [
            'street' => 'nullable|string',
            'zip_code' => 'nullable|string',
            'country' => 'nullable|string',
        ], [
            'street.required' => 'Street is required.',
            'zip_code.required' => 'Zip Code is required.',
            'country.required' => 'Country is required.',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $updateData = array_filter($request->only(['street', 'street2', 'city', 'zip_code', 'state', 'country', 'address', 'map_url', 'shop_lattitude', 'shop_longtitude']), fn($value) => !is_null($value));

        $shop->update($updateData);

        return $this->success('Shop Location Updated Successfully!', $shop);
    }

    public function updateshoppayment(Request $request, $id)
    {
        $shop = Shop::find($id);
        if (!$shop) {
            return $this->error('Shop Not Found.', ['error' => 'Shop Not Found']);
        }

        $updateData = array_filter($request->only(['payment_id', 'account_holder', 'account_type', 'account_number', 'bank_name', 'bank_address', 'bank_code']), fn($value) => !is_null($value));

        $shop->update($updateData);

        return $this->success('Shop Payment Updated Successfully!', $shop);
    }

    public function showpolicy($shop_id)
    {
        $shopPolicy = ShopPolicy::where('shop_id', $shop_id)->first();
        if (!$shopPolicy) {
            return $this->error('Shop Policy Not Found.', ['error' => 'Shop Policy Not Found']);
        }

        return $this->success('Shop Policy Retrieved Successfully!', $shopPolicy);
    }

    public function updateorcreatepolicy(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'shop_id' => 'required|exists:shops,id',
            'refund_policy' => 'required|string',
            'cancellation_policy' => 'required|string',
            'shipping_policy' => 'required|string',
        ], [
            'shop_id.required' => 'The shop ID is required.',
            'shop_id.exists' => 'The selected shop ID is invalid.',
            'refund_policy.required' => 'The refund policy is required.',
            'cancellation_policy.required' => 'The cancellation policy is required.',
            'shipping_policy.required' => 'The shipping policy is required.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $validatedData = $validator->validated();

        $shopPolicy = ShopPolicy::updateOrCreate(
            ['shop_id' => $validatedData['shop_id']],
            [
                'refund_policy' => $validatedData['refund_policy'],
                'cancellation_policy' => $validatedData['cancellation_policy'],
                'shipping_policy' => $validatedData['shipping_policy'],
            ]
        );

        return $this->success('Shop Policy Saved Successfully!', $shopPolicy);
    }

    public function showhour($shop_id)
    {
        $shopHour = ShopHour::where('shop_id', $shop_id)->first();
        if (!$shopHour) {
            return $this->error('Shop Hour Not Found.', ['error' => 'Shop Hour Not Found']);
        }

        return $this->success('Shop Hour Retrieved Successfully!', $shopHour);
    }

    public function updateorcreatehour(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'shop_id' => 'required|exists:shops,id',
            'daily_timing' => 'required|array'
        ], [
            'shop_id.required' => 'The shop ID is required.',
            'shop_id.exists' => 'The selected shop ID is invalid.',
            'daily_timing.required' => 'The daily timing is required.'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $validatedData = $validator->validated();

        $shopHour = ShopHour::updateOrCreate(
            ['shop_id' => $validatedData['shop_id']],
            ['daily_timing' => $validatedData['daily_timing']]
        );

        return $this->success('Shop Hour Saved Successfully!', $shopHour);
    }

    public function status($id)
    {
        $shopStatus = Shop::select('active')->where('id', $id)->first();
        if ($shopStatus->active == 1) {
            $shopStatus = Shop::select('active', 'logo')->where('id', $id)->first();
        }
        return $this->success('Shop Status Retrived Successfully!', $shopStatus);
    }

    public function getAllOrdersByShop($shop_id)
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
            },
            'country' => function ($query) {
                $query->select('id', 'country_name');
            }
        ])
            ->where('seller_id', $shop_id)
            ->orderBy('created_at', 'desc')
            ->get();

        return $this->success('Order Items retrieved successfully.', $orderItems);
    }

    public function showOrderById($order_id, $product_id)
    {
        $orderItem = OrderItems::where('order_id', $order_id)
            ->where('product_id', $product_id)
            ->first();

        if (!$orderItem) {
            return $this->error('Order Item Not Found.', ['error' => 'Order Item Not Found']);
        }

        // Load relationships dynamically
        $orderItem->loadMissing([
            'product' => function ($query) {
                $query->with('productMedia:id,resize_path,order,type,imageable_id');
            },
            'shop',
            'order' => function ($query) {
                $query->with('customer', 'address');
            }
        ]);

        OrderItems::where('order_id', $order_id)
            ->where('product_id', $product_id)
            ->update(['viewed_by_vendor' => 0]);

        return $this->success('Order Item Retrieved Successfully', $orderItem);
    }


    public function getReferralsByUserId($userId)
    {
        $referralCode = 'CBG500' . $userId;

        $referrals = User::where('referral_code', $referralCode)
            ->orderBy('created_at', 'desc')
            ->get();

        if ($referrals->isEmpty()) {
            return $this->success('Referral list retrieved successfully.', []);
        }

        return $this->success('Referral list retrieved successfully.', $referrals);
    }
}

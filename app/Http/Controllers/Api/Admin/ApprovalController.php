<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Traits\ApiResponses;
use Illuminate\Support\Facades\Mail;
use App\Models\Shop;
use App\Mail\ProductApprovedSuccessfully;

class ApprovalController extends Controller
{
    use ApiResponses;

    public function approveProduct($id)
    {
        $UpdatableProduct = Product::where('id', $id)->update(['active' => '1']);
        $product = Product::where('id', $id)->first();
        $shop_id = $product->shop_id;
        $shop = Shop::where('id', $shop_id)->first();

        // Mail::to($shop->email)->send(new ProductApprovedSuccessfully($shop, $product));


        return $this->ok('Product Approved Successfully!');
    }

    public function disapproveProduct($id)
    {
        $product = Product::where('id', $id)->update(['active' => '0']);
        return $this->ok('Product Disapproved Successfully!');
    }

    public function approveCategory($id)
    {
        $product = Category::where('id', $id)->update(['active' => '1']);
        return $this->ok('Category Approved Successfully!');
    }
}

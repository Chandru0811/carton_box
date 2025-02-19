<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DealCategory;
use App\Models\Product;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $hotpicks = DealCategory::where('active', 1)->get();
        $products = Product::where('active', 1)
            ->with(['productMedia:id,resize_path,order,type,imageable_id', 'shop:id,country,city,shop_ratings'])
            ->orderBy('created_at', 'desc')
            ->get();

        if ($request->ajax()) {
            if ($products->isEmpty()) {
                return response('', 204);
            }

            return response()->json([
                'html' => view('contents.home.products', compact('products'))->render()
            ]);
        }
        // dd($products);

        return view('home', compact('products'));
    }


    public function productdescription($id, Request $request)
    {
        $products = Product::with(['productMedia', 'shop', 'shop.hour', 'shop.policy'])->where('id', $id)
            ->first();

        $bookmarkedProducts = collect();


        $url = url()->current();
        $title = $products->name;
        $description = $products->description;
        $image = $products->image_url1;

        $pageurl = url()->current();
        $pagetitle = $products->name;
        $pagedescription = $products->description;
        $pageimage = $products->image_url1;
        $reviewData = collect();

        dd($products);
        return view('productDescription', compact('product', 'bookmarkedProducts', 'shareButtons', 'pageurl', 'reviewData', 'pagetitle', 'pagedescription', 'pageimage', 'vedios'));
    }
}

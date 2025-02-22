<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DealCategory;
use App\Models\Product;
use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $hotpicks = DealCategory::where('active', 1)->get();
        $products = Product::where('active', 1)
            ->with([
                'productMedia:id,resize_path,order,type,imageable_id',
                'shop:id,country,city,shop_ratings',
                'country:id,country_name,currency_symbol'
            ])
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
        return view('home', compact('hotpicks', 'products'));
    }


    public function productdescription($id, Request $request)
    {
        $product = Product::with([
            'productMedia',
            'country:id,country_name,currency_symbol',
            'shop',
            'shop.hour',
            'shop.policy'
        ])->where('id', $id)->firstOrFail();

        $bookmarkedProducts = collect();
        $reviewData = collect();
        $shareButtons = [];
        $vedios = [];

        $pageurl = url()->current();
        $pagetitle = $product->name;
        $pagedescription = $product->description;
        $pageimage = $product->image_url1;

        $relatedProducts = Product::where('id', '!=', $product->id)
            ->where(function ($query) use ($product) {
                $query->whereBetween('box_width', [$product->box_width - 10, $product->box_width + 10])
                    ->whereBetween('box_height', [$product->box_height - 10, $product->box_height + 10])
                    ->whereBetween('box_length', [$product->box_length - 10, $product->box_length + 10]);
            })
            ->orWhere(function ($query) use ($product) {
                $query->where('id', '!=', $product->id)
                    ->where(function ($q) use ($product) {
                        $q->where('original_price', $product->original_price)
                            ->orWhere('pack', $product->pack);
                    });
            })
            ->with('productMedia')
            ->get();



        // dd($relatedProducts);

        return view('productDescription', compact(
            'product',
            'bookmarkedProducts',
            'shareButtons',
            'pageurl',
            'reviewData',
            'pagetitle',
            'pagedescription',
            'pageimage',
            'vedios',
            'relatedProducts'
        ));
    }




    public function search(Request $request)
    {
        $term = $request->input('q');
        $perPage = $request->input('per_page', 10);

        $query = Product::with('productMedia:id,resize_path,order,type,imageable_id', 'shop')
            ->where('active', 1);



        // Search by product name or shop details
        if (!empty($term)) {
            $query->where(function ($subQuery) use ($term) {
                $subQuery->where('name', 'LIKE', '%' . $term . '%')
                    ->orWhereHas('shop', function ($shopQuery) use ($term) {
                        $shopQuery->where('name', 'LIKE', '%' . $term . '%')
                            ->orWhere('country', 'LIKE', '%' . $term . '%')
                            ->orWhere('state', 'LIKE', '%' . $term . '%')
                            ->orWhere('city', 'LIKE', '%' . $term . '%')
                            ->orWhere('street', 'LIKE', '%' . $term . '%')
                            ->orWhere('street2', 'LIKE', '%' . $term . '%');
                    });
            });
        }

        // Brand Filter
        if ($request->has('brand')) {
            $brandTerms = $request->input('brand');
            if (is_array($brandTerms) && count($brandTerms) > 0) {
                $query->whereIn('brand', $brandTerms);
            }
        }

        // Discount Filter
        if ($request->has('discount')) {
            $discountTerm = $request->input('discount');
            if (is_array($discountTerm) && count($discountTerm) > 0) {
                $roundedDiscounts = array_map('round', $discountTerm);
                $query->whereIn(DB::raw('ROUND(discount_percentage)'), $roundedDiscounts);
            }
        }

        // Rating Filter
        if ($request->has('rating_item') && is_array($request->rating_item)) {
            $ratings = $request->rating_item;
            if (!empty($ratings)) {
                $query->whereHas('shop', function ($q) use ($ratings) {
                    $q->whereIn('shop_ratings', $ratings);
                });
            }
        }

        // Price Range Filter
        if ($request->has('price')) {
            $priceRanges = $request->input('price');
            $query->where(function ($priceQuery) use ($priceRanges) {
                foreach ($priceRanges as $range) {
                    $prices = explode('-', $range);
                    $minPrice = isset($prices[0]) ? (float) $prices[0] : null;
                    $maxPrice = isset($prices[1]) ? (float) $prices[1] : null;

                    if ($maxPrice !== null) {
                        $priceQuery->orWhereBetween('discounted_price', [$minPrice, $maxPrice]);
                    } else {
                        $priceQuery->orWhere('discounted_price', '>=', $minPrice);
                    }
                }
            });
        }

        if ($request->has('length')) {
            $lengths = $request->input('length');
            if (is_array($lengths) && count($lengths) > 0) {
                $query->whereIn('length', $lengths);
            }
        }


        // Sorting Options
        if ($request->has('short_by')) {
            $shortby = $request->input('short_by');
            if ($shortby == 'trending') {
                $query->withCount([
                    'views' => function ($viewQuery) {
                        $viewQuery->whereDate('viewed_at', now()->toDateString());
                    }
                ])->orderBy('views_count', 'desc')->addSelect(DB::raw("'TRENDING' as label"));
            } elseif ($shortby == 'popular') {
                $query->withCount('views')->orderBy('views_count', 'desc')->addSelect(DB::raw("'POPULAR' as label"));
            } elseif ($shortby == 'early_bird') {
                $query->whereDate('start_date', now())->addSelect(DB::raw("'EARLY BIRD' as label"));
            } elseif ($shortby == 'last_chance') {
                $query->whereDate('end_date', now())->addSelect(DB::raw("'LAST CHANCE' as label"));
            } elseif ($shortby == 'limited_time') {
                $query->whereRaw('DATEDIFF(end_date, start_date) <= ?', [2])->addSelect(DB::raw("'LIMITED TIME' as label"));
            } elseif ($shortby == 'nearby') {
                $user_latitude = $request->input('latitude');
                $user_longitude = $request->input('longitude');

                if (!isset($user_latitude) || !isset($user_longitude)) {
                    return view('errors.locationError');
                }

                $shops = Shop::select(
                    "shops.id",
                    "shops.name",
                    DB::raw("6371 * acos(cos(radians(" . $user_latitude . "))
                        * cos(radians(shops.shop_lattitude))
                        * cos(radians(shops.shop_longtitude) - radians(" . $user_longitude . "))
                        + sin(radians(" . $user_latitude . "))
                        * sin(radians(shops.shop_lattitude))) AS distance")
                )
                    ->having('distance', '<=', 20)
                    ->orderBy('distance', 'asc')
                    ->get();

                $shopIds = $shops->pluck('id');
                $query->whereIn('shop_id', $shopIds);
            }
        }

        $deals = $query->paginate($perPage);

        // Get filter options for the UI
        $brands = Product::where('active', 1)->whereNotNull('brand')->where('brand', '!=', '')->distinct()->orderBy('brand', 'asc')->pluck('brand');
        $discounts = Product::where('active', 1)->pluck('discount_percentage')->map(fn($discount) => round($discount))->unique()->sort()->values();
        $rating_items = Shop::where('active', 1)->select('shop_ratings', DB::raw('count(*) as rating_count'))->groupBy('shop_ratings')->get();

        // Generate price ranges dynamically
        $priceRanges = [];
        $priceStep = 50;  // Adjusted to match your UI
        $minPrice = Product::min(DB::raw('LEAST(original_price, discounted_price)'));
        $maxPrice = Product::max(DB::raw('GREATEST(original_price, discounted_price)'));

        for ($start = $minPrice; $start <= $maxPrice; $start += $priceStep) {
            $end = $start + $priceStep;
            if ($end > $maxPrice) {
                $priceRanges[] = ['label' => '$' . number_format($start, 2) . ' - $' . number_format($maxPrice, 2)];
                break;
            }
            $priceRanges[] = ['label' => '$' . number_format($start, 2) . ' - $' . number_format($end, 2)];
        }

        $shortby = DealCategory::where('active', 1)->get();
        $totaldeals = $deals->total();

        return view('productfilter', compact('deals', 'brands', 'discounts', 'rating_items', 'priceRanges', 'shortby', 'totaldeals'));
    }
}

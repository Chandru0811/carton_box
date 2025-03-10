<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DealCategory;
use App\Models\CategoryGroup;
use App\Models\Category;
use App\Models\Product;
use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $categoryGroups = CategoryGroup::with('categories')->get();
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
        // dd($categoryGroups);
        return view('home', compact('categoryGroups', 'hotpicks', 'products'));
    }

    public function home(Request $request)
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



        // dd($product);

        return view('productDescription', compact(
            'product',
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

    public function subcategorybasedproducts(Request $request, $slug)
    {
        $perPage = $request->input('per_page', 3);

        $categoryGroups = CategoryGroup::with('categories')->get(); // Fetch category groups

        $query = Product::with(['productMedia:id,resize_path,order,type,imageable_id', 'shop:id,country,state,city,street,street2,zip_code,shop_ratings'])
            ->where('active', 1);

        if ($slug === 'all') {
            $categoryGroupId = $request->input('category_group_id');
            if ($categoryGroupId) {
                $categorygroup = CategoryGroup::find($categoryGroupId);
            } else {
                $categorygroup = CategoryGroup::whereHas('categories')->first();
            }
            $category = null;
            $query->whereHas('category', function ($query) use ($categorygroup) {
                $query->where('category_group_id', $categorygroup->id);
            });
        } else {
            $category = Category::where('slug', $slug)->first();
            $categorygroup = CategoryGroup::where('id', $category->category_group_id)->first();
            $query->whereHas('category', function ($query) use ($slug) {
                $query->where('slug', $slug);
            });
        }

        // Handle price filter
        if ($request->has('price')) {
            $priceFilters = $request->input('price');
            $query->where(function ($query) use ($priceFilters) {
                foreach ($priceFilters as $priceRange) {
                    list($min, $max) = explode('-', str_replace('₹', '', $priceRange));
                    $query->orWhereBetween('discounted_price', [(float)$min, (float)$max]);
                }
            });
        }

        // Handle unit filter
        if ($request->has('unit')) {
            $unitFilters = $request->input('unit');
            $query->whereIn('unit', $unitFilters);
        }

        // Handle pack filter
        if ($request->has('pack')) {
            $packFilters = $request->input('pack');
            $query->where(function ($query) use ($packFilters) {
                foreach ($packFilters as $packRange) {
                    if ($packRange === '100+') {
                        $query->orWhere('pack', '>=', 100);
                    } else {
                        list($min, $max) = explode('-', $packRange);
                        $query->orWhereBetween('pack', [(int)$min, (int)$max]);
                    }
                }
            });
        }

        $deals = $query->paginate($perPage);
        $totaldeals = $deals->total();

        return view('productfilter', compact('deals', 'totaldeals', 'category', 'categorygroup', 'categoryGroups'));
    }

    public function search(Request $request)
    {
        $term = $request->input('q');
        $perPage = $request->input('per_page', 10);

        // Fetch category groups
        $categoryGroups = CategoryGroup::with('categories')->get();

        $query = Product::with('productMedia:id,resize_path,order,type,imageable_id', 'shop')
            ->where('active', 1);

        // Search by Product Name or Shop Details
        if (!empty($term)) {
            $query->where(function ($subQuery) use ($term) {
                $subQuery->where('name', 'LIKE', '%' . $term . '%')
                    ->orWhereHas('shop', function ($shopQuery) use ($term) {
                        $shopQuery->where('name', 'LIKE', '%' . $term . '%')
                            ->orWhere('country_id', 'LIKE', '%' . $term . '%')
                            ->orWhere('state', 'LIKE', '%' . $term . '%')
                            ->orWhere('city', 'LIKE', '%' . $term . '%')
                            ->orWhere('street', 'LIKE', '%' . $term . '%')
                            ->orWhere('street2', 'LIKE', '%' . $term . '%');
                    });
            });
        }

        // Brand Filter
        if ($request->has('brand')) {
            $brandTerms = (array) $request->input('brand');
            if (!empty($brandTerms)) {
                $query->whereIn('brand', $brandTerms);
            }
        }

        // Discount Filter
        if ($request->has('discount')) {
            $discountTerm = (array) $request->input('discount');
            if (!empty($discountTerm)) {
                $roundedDiscounts = array_map('round', $discountTerm);
                $query->whereIn(DB::raw('ROUND(discount_percentage)'), $roundedDiscounts);
            }
        }

        // Rating Filter
        if ($request->has('shop_ratings') && is_array($request->shop_ratings)) {
            $ratings = $request->shop_ratings;
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
                    $cleanRange = str_replace(['₹', ',', ' '], '', $range);
                    $priceRange = explode('-', $cleanRange);
                    $minPrice = isset($priceRange[0]) ? (float) $priceRange[0] : null;
                    $maxPrice = isset($priceRange[1]) ? (float) $priceRange[1] : null;
                    if ($maxPrice !== null) {
                        $priceQuery->orWhereBetween('discounted_price', [$minPrice, $maxPrice]);
                    } else {
                        $priceQuery->orWhere('discounted_price', '>=', $minPrice);
                    }
                }
            });
        }

        // Unit Filter
        if ($request->has('unit')) {
            $units = (array) $request->input('unit');
            if (!empty($units)) {
                $query->whereIn('unit', $units);
            }
        }

        // Pack Filter
        if ($request->has('pack')) {
            $packRanges = $request->input('pack');

            $query->where(function ($packQuery) use ($packRanges) {
                foreach ($packRanges as $range) {
                    if ($range === '100+') {
                        $packQuery->orWhere('pack', '>', 100);
                    } else {
                        $packRange = explode('-', $range);
                        $minPack = isset($packRange[0]) ? (int) $packRange[0] : null;
                        $maxPack = isset($packRange[1]) ? (int) $packRange[1] : null;

                        if ($maxPack !== null) {
                            $packQuery->orWhereBetween('pack', [$minPack, $maxPack]);
                        } else {
                            $packQuery->orWhere('pack', '>=', $minPack);
                        }
                    }
                }
            });
        }

        // Length Filter
        if ($request->has('box_length')) {
            $lengths = (array) $request->input('box_length');
            if (!empty($lengths)) {
                $query->whereIn('box_length', $lengths);
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

        // Paginate Results
        $deals = $query->paginate($perPage);

        // Fetch Filter Options for UI
        $brands = Product::where('active', 1)->whereNotNull('brand')->where('brand', '!=', '')->distinct()->orderBy('brand', 'asc')->pluck('brand');
        $discounts = Product::where('active', 1)->pluck('discount_percentage')->map(fn($discount) => round($discount))->unique()->sort()->values();
        $rating_items = Shop::where('active', 1)->select('shop_ratings', DB::raw('count(*) as rating_count'))->groupBy('shop_ratings')->get();

        // Generate Price Ranges
        $priceRanges = [];
        $priceStep = 50;
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

        // Get Sorting Categories
        $shortby = DealCategory::where('active', 1)->get();
        $totaldeals = $deals->total();

        return view('productfilter', compact('deals', 'brands', 'discounts', 'rating_items', 'priceRanges', 'shortby', 'totaldeals', 'categoryGroups'));
    }
}

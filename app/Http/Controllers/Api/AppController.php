<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\ApiResponses;
use App\Models\CategoryGroup;
use App\Models\Category;
use App\Models\DealCategory;
use App\Models\Product;
use App\Models\Shop;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AppController extends Controller
{


    use ApiResponses;


    public function homepage(Request $request)
    {
        $today = now()->toDateString();

        $categoryGroups = CategoryGroup::where('active', 1)->get();
        $cashBackDeals = DealCategory::where('active', 1)->get();

        $products = Product::where('active', 1)->with(['productMedia:id,resize_path,order,type,imageable_id', 'shop:id,city,shop_ratings'])->orderBy('created_at', 'desc')->get();

        $earlybirddeals = Product::where('active', 1)->whereDate('start_date', now())->get();
        $lastchancedeals = Product::where('active', 1)->whereDate('end_date', now())->get();
        $limitedtimedeals = Product::where('active', 1)->whereRaw('DATEDIFF(end_date, start_date) <= ?', [2])->get();


        $homePageData = [
            'categoryGroups' => $categoryGroups,
            'cashBackDeals' => $cashBackDeals,
            'products' => $products,
        ];

        return $this->success('HomePage Retrieved Successfully!', $homePageData);
    }

    public function categories($id)
    {
        $categories = Category::where('active', 1)
            ->where('category_group_id', $id)
            ->withCount(['products' => function ($query) {
                $query->where('active', 1);
            }])
            ->get();

        return $this->success('Categories Retrieved Successfully!', $categories);
    }

    public function getDeals($category_id, Request $request)
    {
        $deals = Product::with('productMedia:id,resize_path,order,type,imageable_id', 'shop')->where('category_id', $category_id)->where('active', 1)->get();
        $brands = Product::where('active', 1)->where('category_id', $category_id)->whereNotNull('brand')->where('brand', '!=', '')->distinct()->orderBy('brand', 'asc')->pluck('brand');
        $discounts = Product::where('active', 1)->where('category_id', $category_id)->pluck('discount_percentage')->map(function ($discount) {
            return round($discount);
        })->unique()->sort()->values();
        $rating_items = Shop::where('active', 1)->select('shop_ratings', DB::raw('count(*) as rating_count'))->groupBy('shop_ratings')->get();
        $priceRanges = [];
        $priceStep = 2000;
        $minPrice = Product::min(DB::raw('LEAST(original_price, discounted_price)'));
        $maxPrice = Product::max(DB::raw('GREATEST(original_price, discounted_price)'));

        for ($start = $minPrice; $start <= $maxPrice; $start += $priceStep) {
            $end = $start + $priceStep;

            if ($end > $maxPrice) {
                $priceRanges[] = [
                    'label' => '$' . number_format($start, 2) . ' - $' . number_format($end, 2)
                ];
                break;
            }
            $priceRanges[] = [
                'label' => '$' . number_format($start, 2) . ' - $' . number_format($end, 2)
            ];
        }
        $shortby = DealCategory::where('active', 1)->take(5)->get();
        $totaldeals = $deals->count();

        $dealdata = [
            'deals' => $deals,
            'brands' => $brands,
            'discounts' => $discounts,
            'rating_items' => $rating_items,
            'priceRanges' => $priceRanges,
            'shortby' => $shortby,
            'totaldeals' => $totaldeals,
        ];

        return $this->success('Deals Retrieved Successfully!', $dealdata);
    }


    public function dealDescription($id, Request $request)
    {
        $deal = Product::with(['productMedia:id,resize_path,order,type,imageable_id', 'shop', 'shop.hour', 'shop.policy'])
            ->where('id', $id)
            ->first();

        if (!$deal) {
            return $this->error('Deal not found!', 404);
        }

        return $this->success('Deal Retrieved Successfully!', $deal);
    }



    public function search(Request $request)
    {
        $term = $request->input('q');

        $query = Product::with('productMedia:id,resize_path,order,type,imageable_id', 'shop')->where('active', 1);

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

        if ($request->has('brand')) {
            $brandTerms = $request->input('brand');
            if (is_array($brandTerms) && count($brandTerms) > 0) {
                $query->whereIn('brand', $brandTerms);
            }
        }

        if ($request->has('discount')) {
            $discountTerm = $request->input('discount');
            if (is_array($discountTerm) && count($discountTerm) > 0) {
                $roundedDiscounts = array_map('round', $discountTerm);
                $query->whereIn(DB::raw('ROUND(discount_percentage)'), $roundedDiscounts);
            }
        }

        if ($request->has('rating_item') && is_array($request->rating_item)) {
            $ratings = $request->rating_item;
            if (!empty($ratings)) {
                $query->whereHas('shop', function ($q) use ($ratings) {
                    $q->whereIn('shop_ratings', $ratings);
                });
            }
        }

        if ($request->has('price_range')) {
            $priceRanges = $request->input('price_range');

            // Apply price range filters for each selected range
            $query->where(function ($priceQuery) use ($priceRanges) {
                foreach ($priceRanges as $range) {
                    // Clean and split the price range
                    $cleanRange = str_replace(['Rs', ',', ' '], '', $range);
                    $priceRange = explode('-', $cleanRange);

                    $minPrice = isset($priceRange[0]) ? (float)$priceRange[0] : null;
                    $maxPrice = isset($priceRange[1]) ? (float)$priceRange[1] : null;

                    // Apply the range filter
                    if ($maxPrice !== null) {
                        $priceQuery->orWhereBetween('discounted_price', [$minPrice, $maxPrice]);
                    } else {
                        $priceQuery->orWhere('discounted_price', '>=', $minPrice);
                    }
                }
            });
        }

        // Initialize the units variable with an empty array
        $units = [];

        if ($request->has('unit')) {
            $units = (array) $request->input('unit');
            if (!empty($units)) {
                $query->whereIn('unit', $units);
            }
        }

        if ($request->has('short_by')) {
            $shortby = $request->input('short_by');
            if ($shortby == 'trending') {
                $query->withCount(['views' => function ($viewQuery) {
                    $viewQuery->whereDate('viewed_at', now()->toDateString());
                }])
                    ->with(['shop:id,country,state,city,street,street2,zip_code,shop_ratings'])
                    ->orderBy('views_count', 'desc')
                    ->addSelect(DB::raw("'TRENDING' as label"));
            } elseif ($shortby == 'popular') {
                $query->withCount('views')
                    ->with(['shop:id,country,state,city,street,street2,zip_code,shop_ratings'])
                    ->orderBy('views_count', 'desc')
                    ->addSelect(DB::raw("'POPULAR' as label"));
            } elseif ($shortby == 'early_bird') {
                $query->with(['shop:id,country,state,city,street,street2,zip_code,shop_ratings'])
                    ->whereDate('start_date', now())
                    ->whereHas('shop')
                    ->select('*', DB::raw("'EARLY BIRD' as label"));
            } elseif ($shortby == 'last_chance') {
                $query->with(['shop:id,country,state,city,street,street2,zip_code,shop_ratings'])
                    ->whereDate('end_date', now())
                    ->addSelect(DB::raw("'LAST CHANCE' as label"));
            } elseif ($shortby == 'limited_time') {
                $query->with(['shop:id,country,state,city,street,street2,zip_code,shop_ratings'])
                    ->whereRaw('DATEDIFF(end_date, start_date) <= ?', [2])
                    ->select('*', DB::raw("'LIMITED TIME' as label"));
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

        $deals = $query->get();

        $brands = Product::where('active', 1)->whereNotNull('brand')->where('brand', '!=', '')->distinct()->orderBy('brand', 'asc')->pluck('brand');
        $discounts = Product::where('active', 1)->pluck('discount_percentage')->map(function ($discount) {
            return round($discount);
        })->unique()->sort()->values();
        $rating_items = Shop::where('active', 1)->select('shop_ratings', DB::raw('count(*) as rating_count'))->groupBy('shop_ratings')->get();

        $priceRanges = [];
        $priceStep = 2000;
        $minPrice = Product::min(DB::raw('LEAST(original_price, discounted_price)'));
        $maxPrice = Product::max(DB::raw('GREATEST(original_price, discounted_price)'));

        for ($start = $minPrice; $start <= $maxPrice; $start += $priceStep) {
            $end = $start + $priceStep;

            if ($end > $maxPrice) {
                $priceRanges[] = [
                    'label' => 'Rs' . number_format($start, 2) . ' - Rs' . number_format($end, 2)
                ];
                break;
            }
            $priceRanges[] = [
                'label' => 'Rs' . number_format($start, 2) . ' - Rs' . number_format($end, 2)
            ];
        }

        $shortby = DealCategory::where('active', 1)->take(5)->get();
        $totaldeals = $deals->count();

        $dealdata = [
            'deals' => $deals,
            'brands' => $brands,
            'discounts' => $discounts,
            'rating_items' => $rating_items,
            'priceRanges' => $priceRanges,
            'units' => $units,
            'shortby' => $shortby,
            'totaldeals' => $totaldeals,
        ];

        return $this->success('Deals Retrieved Successfully!', $dealdata);
    }


    public function subcategorybasedproductsformobile($id, Request $request)
    {
        $query = Product::with('productMedia:id,resize_path,order,type,imageable_id', 'shop')
            ->with(['shop:id,country,state,city,street,street2,zip_code,shop_ratings'])
            ->where('active', 1);

        if ($id === '0') {
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
            $brands = Product::where('active', 1)
                ->whereHas('category', function ($query) use ($categorygroup) {
                    $query->where('category_group_id', $categorygroup->id);
                })
                ->whereNotNull('brand')
                ->where('brand', '!=', '')
                ->distinct()
                ->orderBy('brand', 'asc')
                ->pluck('brand');

            $discounts = Product::where('active', 1)
                ->whereHas('category', function ($query) use ($categorygroup) {
                    $query->where('category_group_id', $categorygroup->id);
                })
                ->pluck('discount_percentage')
                ->map(function ($discount) {
                    return round($discount);
                })
                ->unique()
                ->sort()
                ->values();
        } else {
            $category = Category::where('id', $id)->first();
            $categorygroup = CategoryGroup::where('id', $category->category_group_id)->first();

            $query->whereHas('category', function ($query) use ($id) {
                $query->where('id', $id);
            });
            $brands = Product::where('active', 1)->where('category_id', $category->id)->whereNotNull('brand')->where('brand', '!=', '')->distinct()->orderBy('brand', 'asc')->pluck('brand');
            $discounts = Product::where('active', 1)->where('category_id', $category->id)->pluck('discount_percentage')->map(function ($discount) {
                return round($discount);
            })->unique()->sort()->values();
        }

        // Apply price filter
        if ($request->has('price')) {
            $priceRanges = $request->input('price');
            $query->where(function ($priceQuery) use ($priceRanges) {
                foreach ($priceRanges as $range) {
                    $cleanRange = str_replace(['₹', ',', ' '], '', $range);
                    $priceRange = explode('-', $cleanRange);

                    $minPrice = isset($priceRange[0]) ? (float)$priceRange[0] : null;
                    $maxPrice = isset($priceRange[1]) ? (float)$priceRange[1] : null;

                    if ($maxPrice !== null) {
                        $priceQuery->orWhereBetween('discounted_price', [$minPrice, $maxPrice]);
                    } else {
                        $priceQuery->orWhere('discounted_price', '>=', $minPrice);
                    }
                }
            });
        }

        // Apply unit filter
        if ($request->has('unit')) {
            $units = $request->input('unit');
            $query->whereIn('unit', $units);
        }

        // Apply pack filter
        if ($request->has('pack')) {
            $packs = $request->input('pack');
            $query->where(function ($packQuery) use ($packs) {
                foreach ($packs as $pack) {
                    $packRange = explode('-', $pack);
                    $minPack = isset($packRange[0]) ? (float)$packRange[0] : null;
                    $maxPack = isset($packRange[1]) ? (float)$packRange[1] : null;

                    if ($maxPack !== null) {
                        $packQuery->orWhereBetween('pack', [$minPack, $maxPack]);
                    } else {
                        $packQuery->orWhere('pack', '>=', $minPack);
                    }
                }
            });
        }

        $deals = $query->get();

        $rating_items = Shop::where('active', 1)->select('shop_ratings', DB::raw('count(*) as rating_count'))->groupBy('shop_ratings')->get();

        $priceRanges = [];
        $priceStep = 2000;
        $minPrice = Product::min(DB::raw('LEAST(original_price, discounted_price)'));
        $maxPrice = Product::max(DB::raw('GREATEST(original_price, discounted_price)'));

        for ($start = $minPrice; $start <= $maxPrice; $start += $priceStep) {
            $end = $start + $priceStep;

            if ($end > $maxPrice) {
                $priceRanges[] = [
                    'label' => '₹' . number_format($start, 2) . ' - ₹' . number_format($end, 2)
                ];
                break;
            }
            $priceRanges[] = [
                'label' => '₹' . number_format($start, 2) . ' - ₹' . number_format($end, 2)
            ];
        }

        $shortby = DealCategory::where('active', 1)->take(5)->get();
        $totaldeals = $deals->count();

        $dealdata = [
            'deals' => $deals,
            'brands' => $brands,
            'discounts' => $discounts,
            'rating_items' => $rating_items,
            'priceRanges' => $priceRanges,
            'units' => $units ?? [],
            'packs' => $packs ?? [],
            'shortby' => $shortby,
            'totaldeals' => $totaldeals,
        ];

        return $this->success('Deals Retrieved Successfully!', $dealdata);
    }
}

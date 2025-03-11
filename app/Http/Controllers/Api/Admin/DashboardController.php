<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\OrderItems;
use App\Models\DealClick;
use App\Models\DealViews;
use App\Models\Shop;
use App\Models\Dealenquire;
use App\Models\DealShare;
use App\Models\CouponCodeUsed;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Traits\ApiResponses;


class DashboardController extends Controller
{

    use ApiResponses;
    public function index()
    {
        $userId = Auth::id();
        $shop = Shop::where('owner_id', $userId)->first();
        $shopId = $shop->id;
        $products = Product::where('shop_id', $shopId)->where('active', 1)->get();
        $productscount = Product::where('shop_id', $shopId)->where('active', 1)->count();
        $dealcount = Product::where('shop_id', $shopId)->count();
        $dealactivecount = Product::where('shop_id', $shopId)->where('active', 1)->count();
        $productIds = Product::where('shop_id', $shopId)->where('active', 1)->pluck('id');
        $dealclicks = DealClick::whereIn('deal_id', $productIds)->Count();
        $dealviews = DealViews::whereIn('deal_id', $productIds)->Count();
        $discountcopied = CouponCodeUsed::whereIn('deal_id', $productIds)->Count();
        $dealshares = DealShare::whereIn('deal_id', $productIds)->Count();
        $dealenquires = Dealenquire::whereIn('deal_id', $productIds)->Count();
        $orderscount = OrderItems::where('seller_id', $shopId)->count();



        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();

        $dealClicksData = [];
        $dealViewsData = [];
        $discountCopiedData = [];
        $dealSharesData = [];
        $dealEnquiresData = [];


        foreach (range(0, 6) as $day) {
            $currentDay = $startOfWeek->copy()->addDays($day);

            // Get the counts for each day
            $dealClicksData[] = DealClick::whereIn('deal_id', $productIds)
                ->whereDate('clicked_at', $currentDay)
                ->count();

            $dealViewsData[] = DealViews::whereIn('deal_id', $productIds)
                ->whereDate('viewed_at', $currentDay)
                ->count();

            $discountCopiedData[] = CouponCodeUsed::whereIn('deal_id', $productIds)
                ->whereDate('copied_at', $currentDay)
                ->count();

            $dealSharesData[] = DealShare::whereIn('deal_id', $productIds)
                ->whereDate('share_at', $currentDay)
                ->count();

            $dealEnquiresData[] = Dealenquire::whereIn('deal_id', $productIds)
                ->whereDate('enquire_at', $currentDay)
                ->count();
        }

        $chartdata = [
            'series' => [
                [
                    'name' => 'Deal Clicks',
                    'data' => $dealClicksData
                ],
                [
                    'name' => 'Deal Views',
                    'data' => $dealViewsData
                ],
                [
                    'name' => 'Discount Copied',
                    'data' => $discountCopiedData
                ],
                [
                    'name' => 'Deal Shares',
                    'data' => $dealSharesData
                ],
                [
                    'name' => 'Deal Enquiries',
                    'data' => $dealEnquiresData
                ]
            ]
        ];


        $dashboardData = [
            'totaldealclicks' => $dealclicks,
            'totaldealviews' => $dealviews,
            'totaldiscountcopied' => $discountcopied,
            'totaldealshared' => $dealshares,
            'totaldealenquired' => $dealenquires,
            'totalproductscount' => $productscount,
            'totalorderscount' => $orderscount,
            'products' => $products,
            'chatdata' => $chartdata
        ];

        return $this->success('Dashboard Retrieved Successfully!', $dashboardData);
    }

    public function graphdata(Request $request)
    {
        $userId = Auth::id();
        $shop = Shop::where('owner_id', $userId)->first();
        $shopId = $shop->id;

        $productIds = $request->input('product_id');
        if (empty($productIds)) {
            return response()->json(['error' => 'Product IDs are required'], 400);
        }

        $week = $request->input('week');
        $year = substr($week, 0, 4);
        $weekNumber = substr($week, 6);

        $startOfWeek = Carbon::now()->setISODate($year, $weekNumber)->startOfWeek();
        $endOfWeek = Carbon::now()->setISODate($year, $weekNumber)->endOfWeek();

        $dealClicksData = [];
        $dealViewsData = [];
        $discountCopiedData = [];
        $dealSharesData = [];
        $dealEnquiresData = [];

        foreach (range(0, 6) as $day) {
            $currentDay = $startOfWeek->copy()->addDays($day);

            $dealClicksData[] = DealClick::whereIn('deal_id', $productIds)
                ->whereDate('clicked_at', $currentDay)
                ->count();

            $dealViewsData[] = DealViews::whereIn('deal_id', $productIds)
                ->whereDate('viewed_at', $currentDay)
                ->count();

            $discountCopiedData[] = CouponCodeUsed::whereIn('deal_id', $productIds)
                ->whereDate('copied_at', $currentDay)
                ->count();

            $dealSharesData[] = DealShare::whereIn('deal_id', $productIds)
                ->whereDate('share_at', $currentDay)
                ->count();

            $dealEnquiresData[] = Dealenquire::whereIn('deal_id', $productIds)
                ->whereDate('enquire_at', $currentDay)
                ->count();
        }

        return response()->json([
            'series' => [
                [
                    'name' => 'Deal Clicks',
                    'data' => $dealClicksData
                ],
                [
                    'name' => 'Deal Views',
                    'data' => $dealViewsData
                ],
                [
                    'name' => 'Discount Copied',
                    'data' => $discountCopiedData
                ],
                [
                    'name' => 'Deal Shares',
                    'data' => $dealSharesData
                ],
                [
                    'name' => 'Deal Enquires',
                    'data' => $dealEnquiresData
                ]
            ]
        ]);
    }
}

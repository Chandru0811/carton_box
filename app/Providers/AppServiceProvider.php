<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use App\Models\CategoryGroup;
use App\Models\Product;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
{
    // Share data with all views
    View::share('country_code', session('country_code'));

    // Share data with the nav.footer view
    View::composer('nav.footer', function ($view) {
        // Fetch or calculate the data you want to pass
        $categoryGroups = CategoryGroup::with('categories')->get();
        $category = null; // Default value
        $categorygroup = null; // Default value
        $deals = Product::where('active', 1)->paginate(3); // Example query
        $totaldeals = $deals->total();

        // Pass the data to the view
        $view->with([
            'categoryGroups' => $categoryGroups,
            'category' => $category,
            'categorygroup' => $categorygroup,
            'deals' => $deals,
            'totaldeals' => $totaldeals,
        ]);
    });
}
}

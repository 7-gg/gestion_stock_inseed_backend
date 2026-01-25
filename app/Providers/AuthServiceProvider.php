<?php

namespace App\Providers;

use App\Models\StockMovement;
use App\Models\User;
use App\Models\Stock;
use App\Models\Product;
use App\Models\StockProduct;
use App\Models\StockUser;
use App\Models\ProductCategory;
use App\Models\ProductUnit;
use App\Models\AdminHistory;
use App\Policies\UserPolicy;
use App\Policies\StockPolicy;
use App\Policies\ProductPolicy;
use App\Policies\StockMovementPolicy;
use App\Policies\StockProductPolicy;
use App\Policies\StockUserPolicy;
use App\Policies\ProductCategoryPolicy;
use App\Policies\ProductUnitPolicy;
use App\Policies\AdminHistoryPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        User::class => UserPolicy::class,
        Stock::class => StockPolicy::class,
        Product::class => ProductPolicy::class,
        StockMovement::class => StockMovementPolicy::class,
        StockProduct::class => StockProductPolicy::class,
        StockUser::class => StockUserPolicy::class,
        ProductCategory::class => ProductCategoryPolicy::class,
        ProductUnit::class => ProductUnitPolicy::class,
        AdminHistory::class => AdminHistoryPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
    }
}

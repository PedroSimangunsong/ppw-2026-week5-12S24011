<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;
use App\Listeners\LogDatabaseQueries;
use Illuminate\Database\Events\QueryExecuted;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Menggunakan pagination Bootstrap 5
        Paginator::useBootstrapFive();

        // Audit query database
        DB::listen(function (QueryExecuted $query) {
            app(LogDatabaseQueries::class)->handle($query);
        });
    }
}

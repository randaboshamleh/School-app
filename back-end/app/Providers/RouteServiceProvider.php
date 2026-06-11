<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * المسار إلى "الصفحة الرئيسية" بعد تسجيل الدخول.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * تسجيل أي خدمات للتطبيق.
     */
    public function boot(): void
    {
        parent::boot();

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }
}

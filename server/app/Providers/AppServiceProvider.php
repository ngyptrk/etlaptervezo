<?php

namespace App\Providers;

use App\Models\User;
use App\Models\Unit;
use App\Policies\UserPolicy;
use App\Policies\UnitPolicy;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Laravel\Sanctum\Http\Middleware\CheckAbilities;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Illuminate\Support\Facades\Exceptions;
use Illuminate\Support\Facades\Gate;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Schema::defaultStringLength(191);

        Route::aliasMiddleware('ability', CheckAbilities::class);

        Exceptions::renderable(function (AccessDeniedHttpException $e, $request) {
            if ($request->is('api/*')) {
                $message = $e->getMessage() ?? 'Access denied.';

                if (str_contains($message, 'Invalid ability provided.')) {
                    $message = 'Access denied.';
                }

                return response()->json([
                    'message' => $message
                ], 403);
            }
        });

        // Policy regisztrációk
        Gate::policy(User::class, UserPolicy::class);
        Gate::policy(Unit::class, UnitPolicy::class);
    }
}

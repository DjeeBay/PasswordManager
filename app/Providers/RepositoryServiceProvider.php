<?php

namespace App\Providers;

use App\Interfaces\CategoryRepositoryInterface;
use App\Interfaces\FavoriteRepositoryInterface;
use App\Interfaces\IconRepositoryInterface;
use App\Interfaces\KeepassRepositoryInterface;
use App\Interfaces\UserCategoriesRepositoryInterface;
use App\Interfaces\UserRepositoryInterface;
use App\Repositories\CategoryRepository;
use App\Repositories\FavoriteRepository;
use App\Repositories\IconRepository;
use App\Repositories\KeepassRepository;
use App\Repositories\UserCategoriesRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(CategoryRepositoryInterface::class, CategoryRepository::class);
        $this->app->bind(FavoriteRepositoryInterface::class, FavoriteRepository::class);
        $this->app->bind(IconRepositoryInterface::class, IconRepository::class);
        $this->app->bind(KeepassRepositoryInterface::class, KeepassRepository::class);
        $this->app->bind(UserCategoriesRepositoryInterface::class, UserCategoriesRepository::class);
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}

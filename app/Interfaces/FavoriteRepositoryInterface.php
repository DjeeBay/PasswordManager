<?php


namespace App\Interfaces;


use Illuminate\Database\Eloquent\Collection;

interface FavoriteRepositoryInterface extends BaseRepositoryInterface
{
    public function getList() : Collection;

    public function storeMultiple(array $keepasses);
}

<?php


namespace App\Interfaces;


interface IconRepositoryInterface extends BaseRepositoryInterface
{
    public function getSearchIcons(array $parameters = []);
}

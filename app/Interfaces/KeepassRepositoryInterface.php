<?php


namespace App\Interfaces;


interface KeepassRepositoryInterface extends BaseRepositoryInterface
{
    public function getStructuredItems($category_id);
}

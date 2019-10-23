<?php


namespace App\Interfaces;


use Illuminate\Database\Eloquent\Model;

interface BaseRepositoryInterface
{
    public function create(array $attributes = []) : Model;

    public function update(Model $entity, array $attributes = []) : Model;

    public function delete(Model $entity) : bool;

    public function get(int $id) : ?Model;
}

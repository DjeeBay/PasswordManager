<?php


namespace App\Repositories;


use App\Interfaces\CategoryRepositoryInterface;
use App\Models\Category;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class CategoryRepository implements CategoryRepositoryInterface
{
    protected $model;

    public function __construct()
    {
        $this->model = new Category();
    }

    public function create(array $attributes = []): Model
    {
        //TODO associate to user
        $this->model->name = Arr::get($attributes, 'name');
        $this->model->save();

        return $this->model;
    }

    public function update(Model $entity, array $attributes = []): Model
    {
        // TODO: Implement update() method.
    }

    public function delete(Model $entity): bool
    {
        // TODO: Implement delete() method.
    }

    public function get(int $id): ?Model
    {
        // TODO: Implement get() method.
    }
}

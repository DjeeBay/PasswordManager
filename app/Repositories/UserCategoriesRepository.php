<?php


namespace App\Repositories;


use App\Interfaces\UserCategoriesRepositoryInterface;
use App\Models\UserCategory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class UserCategoriesRepository implements UserCategoriesRepositoryInterface
{
    /** @var UserCategory $model */
    protected $model;

    public function __construct()
    {
        $this->model = new UserCategory();
    }

    public function create(array $attributes = []): Model
    {
        $this->model = $this->model->newInstance();
        $id = $this->model->insertGetId([
            'user_id' => Arr::get($attributes, 'user_id'),
            'category_id' => Arr::get($attributes, 'category_id')
        ]);

        return $this->model->find($id);
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

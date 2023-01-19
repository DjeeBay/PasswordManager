<?php

namespace App\Repositories;

use App\Interfaces\PrivateCategoryRepositoryInterface;
use App\Models\PrivateCategory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PrivateCategoryRepository implements PrivateCategoryRepositoryInterface
{
    protected $model;

    public function __construct()
    {
        $this->model = new PrivateCategory();
    }

    public function create(array $attributes = []): Model
    {
        $entity = null;
        DB::transaction(function () use ($attributes, &$entity) {
            $this->model = $this->model->newInstance();
            $this->model->name = Arr::get($attributes, 'name');
            $this->model->description = Arr::get($attributes, 'description');
            $this->model->color = Arr::get($attributes, 'color');
            $this->model->owner_id = Auth::user()->id;
            $this->model->save();

            $entity = $this->model;
        });

        return $entity;
    }

    public function update(Model $entity, array $attributes = []): Model
    {
        DB::transaction(function () use (&$entity, $attributes) {
            $entity->update([
                'name' => Arr::get($attributes, 'name'),
                'description' => Arr::get($attributes, 'description'),
                'color' => Arr::get($attributes, 'color'),
            ]);
        });

        return $entity;
    }

    public function delete(Model $entity): bool
    {
        return $entity->delete();
    }

    public function get(int $id): ?Model
    {
        // TODO: Implement get() method.
    }
}

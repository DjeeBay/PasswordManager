<?php


namespace App\Repositories;

use App\Interfaces\UserRepositoryInterface;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class UserRepository implements UserRepositoryInterface
{
    protected $model;

    public function __construct()
    {
        $this->model = new User();
    }

    public function create(array $attributes = []): Model
    {
        $this->model->name = Arr::get($attributes, 'name');
        $this->model->firstname = Arr::get($attributes, 'firstname');
        $this->model->lastname = Arr::get($attributes, 'lastname');
        $this->model->password = bcrypt(Arr::get($attributes, 'password'));
        $this->model->is_admin = Arr::get($attributes, 'is_admin', 0);
        $this->model->email = Arr::get($attributes, 'email');
        $this->model->save();

        return $this->model;
    }

    public function update(Model $entity, array $attributes = []): Model
    {
        $entity->update([
            'name' => Arr::get($attributes, 'name'),
            'firstname' => Arr::get($attributes, 'firstname'),
            'lastname' => Arr::get($attributes, 'lastname'),
            'password' => Arr::get($attributes, 'password') ? bcrypt(Arr::get($attributes, 'password')) : $entity->password,
            'is_admin' => Arr::get($attributes, 'is_admin', 0),
            'email' => Arr::get($attributes, 'email')
        ]);

        /** @var User $entity */
        $entity->syncPermissions(Arr::get($attributes, 'permissions'));

        return $entity;
    }

    public function delete(Model $entity): bool
    {
        /** @var User $entity */
        return $entity->delete();
    }

    public function get(int $id): ?Model
    {
        // TODO: Implement get() method.
    }
}

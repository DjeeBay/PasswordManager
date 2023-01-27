<?php


namespace App\Repositories;

use App\Interfaces\UserRepositoryInterface;
use App\Models\Category;
use App\Models\User;
use App\Models\UserCategory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserRepository implements UserRepositoryInterface
{
    protected $model;

    public function __construct()
    {
        $this->model = new User();
    }

    public function create(array $attributes = []): Model
    {
        /** @var User $entity */
        $entity = null;
        DB::transaction(function () use ($attributes, &$entity) {
            $this->model = $this->model->newInstance();
            $this->model->name = Arr::get($attributes, 'name');
            $this->model->firstname = Arr::get($attributes, 'firstname');
            $this->model->lastname = Arr::get($attributes, 'lastname');
            $this->model->password = bcrypt(Arr::get($attributes, 'password'));
            $this->model->is_admin = Arr::get($attributes, 'is_admin', 0);
            $this->model->email = Arr::get($attributes, 'email');
            $this->model->save();
            $entity = $this->model;

            $entity->syncPermissions(Arr::get($attributes, 'permissions'));
            $this->syncCategories($entity, Arr::get($attributes, 'categories') ?? []);
        });

        return $entity;
    }

    public function update(Model $entity, array $attributes = []): Model
    {
        DB::transaction(function () use (&$entity, $attributes) {
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
            $this->syncCategories($entity, Arr::get($attributes, 'categories') ?? []);
        });

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

    private function syncCategories(User $user, array $categories)
    {
        if (Auth::user()->is_admin || Auth::user()->can('manage user permissions')) {
            if ($user->is_admin) {
                $categories = Category::all()->pluck('id')->toArray();
            }
            UserCategory::where('user_id', '=', $user->id)->delete();
            $userCategoryRepository = app(UserCategoriesRepository::class);
            foreach ($categories as $category) {
                $userCategoryRepository->create(['user_id' => $user->id, 'category_id' => $category]);
            }
        }

    }
}

<?php


namespace App\Repositories;


use App\Interfaces\CategoryRepositoryInterface;
use App\Models\Category;
use App\Models\User;
use App\Models\UserCategory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CategoryRepository implements CategoryRepositoryInterface
{
    protected $model;

    public function __construct()
    {
        $this->model = new Category();
    }

    public function create(array $attributes = []): Model
    {
        $entity = null;
        DB::transaction(function () use ($attributes, &$entity) {
            $this->model = $this->model->newInstance();
            $this->model->name = Arr::get($attributes, 'name');
            $this->model->description = Arr::get($attributes, 'description');
            $this->model->restricted = Arr::get($attributes, 'restricted', 0);
            $this->model->save();
            $this->saveUsersCategory($this->model);

            $entity = $this->model;

            $this->syncUsers($entity, Arr::get($attributes, 'users') ?? []);
        });

        return $entity;
    }

    public function update(Model $entity, array $attributes = []): Model
    {
        DB::transaction(function () use (&$entity, $attributes) {
            $entity->update([
                'name' => Arr::get($attributes, 'name'),
                'description' => Arr::get($attributes, 'description'),
                'restricted' => Arr::get($attributes, 'restricted', 0)
            ]);

            $this->syncUsers($entity, Arr::get($attributes, 'users') ?? []);
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

    private function saveUsersCategory(Category $category)
    {
        $userCategoriesRepository = app(UserCategoriesRepository::class);
        if (!Auth::user()->is_admin) {
            $userCategoriesRepository->create(['category_id' => $category->id, 'user_id' => Auth::user()->id]);
        }
        foreach (User::where('is_admin', '=', 1)->get() as $user) {
            $userCategoriesRepository->create(['category_id' => $category->id, 'user_id' => $user->id]);
        }
    }

    /**
     * Sync users except admins
     *
     * @param Category $category
     * @param array $users
     */
    private function syncUsers(Category $category, array $users)
    {
        $adminIds = User::where('is_admin', '=', 1)->pluck('id')->toArray();
        UserCategory::where('category_id', '=', $category->id)
            ->whereNotIn('user_id', $adminIds)
            ->delete();

        $userCategoriesRepository = app(UserCategoriesRepository::class);
        foreach ($users as $user) {
            $userCategoriesRepository->create(['user_id' => $user, 'category_id' => $category->id]);
        }
    }
}

<?php


namespace App\Repositories;


use App\Interfaces\KeepassRepositoryInterface;
use App\Models\Keepass;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class KeepassRepository implements KeepassRepositoryInterface
{
    protected $model;

    public function __construct()
    {
        $this->model = new Keepass();
    }

    public function create(array $attributes = []): Model
    {
        $entity = null;
        DB::transaction(function () use ($attributes, &$entity) {
            $this->model->title = Arr::get($attributes, 'title');
            $this->model->category_id = Arr::get($attributes, 'category_id');
            $this->model->is_folder = Arr::get($attributes, 'is_folder');
            $this->model->parent_id = Arr::get($attributes, 'parent_id');
            $this->model->login = Arr::get($attributes, 'login');
            $this->model->password = Arr::get($attributes, 'password') ? encrypt(Arr::get($attributes, 'password')) : null;
            $this->model->url = Arr::get($attributes, 'url');
            $this->model->notes = Arr::get($attributes, 'notes');
            $this->model->save();
            $entity = $this->model;
        });

        return $entity;
    }

    public function update(Model $entity, array $attributes = []): Model
    {
        DB::transaction(function () use (&$entity, $attributes) {
            $entity->update([
                'title' => Arr::get($attributes, 'title'),
                'category_id' => Arr::get($attributes, 'category_id'),
                'is_folder' => Arr::get($attributes, 'is_folder'),
                'parent_id' => Arr::get($attributes, 'parent_id'),
                'login' => Arr::get($attributes, 'login'),
                'password' => Arr::get($attributes, 'password') ? encrypt(Arr::get($attributes, 'password')) : null,
                'url' => Arr::get($attributes, 'url'),
                'notes' => Arr::get($attributes, 'notes'),
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

    public function getStructuredItems($category_id)
    {
        $items = $this->model->where('category_id', '=', $category_id)
            ->orderBy('title')
            ->get();

        return $this->setStructureRecusively($items, collect());
    }

    private function setStructureRecusively(Collection $allItems, $items)
    {
        $items = count($items) ? $items : $allItems->where('parent_id', null)->where('is_folder', 1)->values();
        foreach ($items as $item) {
            if ($item->is_folder) {
                $children = $allItems->where('parent_id', $item->id)->sortBy('title', SORT_NATURAL|SORT_FLAG_CASE, false)->values();
                foreach ($children as $child) {
                    $child->password = $child->password ? decrypt($child->password) : null;
                }
                $item->children = $children;
                $this->setStructureRecusively($allItems, $children);
            }
        }

        return $items;
    }
}

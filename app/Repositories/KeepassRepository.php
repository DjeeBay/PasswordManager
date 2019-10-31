<?php


namespace App\Repositories;

use App\Interfaces\KeepassRepositoryInterface;
use App\Models\Category;
use App\Models\Keepass;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
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
            $this->model = $this->model->newInstance();
            $this->model->title = Arr::get($attributes, 'title', 'no_title');
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

        $rootFolders = $items->where('is_folder', 1)
        ->where ('parent_id', null)
        ->values()->toArray();

        return $this->setStructureRecusively($items->toArray(), $rootFolders);
    }

    private function setStructureRecusively(array $allItems, array &$folders)
    {
        foreach ($folders as &$folder) {
            $folder = (array) $folder;
            $children = [];
            foreach ($allItems as $item) {
                if ($item['parent_id'] === $folder['id']) {
                    array_push($children, $item);
                }
            }
            foreach ($children as &$child) {
                $child['password'] = $child['password'] ? decrypt($child['password']) : null;
            }
            $folder['children'] = $children;
            $this->setStructureRecusively($allItems, $folder['children']);
        }

        return $folders;
    }

    public function processXml(\SimpleXMLElement $xml, $categoryName) : bool
    {
        $imported = false;
        DB::transaction(function() use ($xml, $categoryName, &$imported) {
            $categoryRepository = app(CategoryRepository::class);
            $category = $categoryRepository->create(['name' => $categoryName]);

            $root = $xml->xpath('Root');
            if ($root && count($root)) {
                $mainGroup = $root[0]->xpath('Group');
                if ($mainGroup && count($mainGroup)) {
                    $groups = $mainGroup[0]->xpath('Group');
                    if ($groups) {
                        $this->createKeepassesRecursively(app(KeepassRepository::class), $category, $groups);
                    }
                }
            }

            $imported = true;
        });

        return $imported;
    }

    public function getHistoric(array $parameters = [])
    {
        /** @var Builder $query */
        $query = Keepass::withTrashed();

        if (Arr::has($parameters, 'sortBy')) {
            switch (Arr::get($parameters, 'sortBy')) {
                case 'created':
                    $query->orderByDesc('created_at');
                    break;
                case 'deleted':
                    $query->orderByDesc('deleted_at');
                    break;
                default:
                    $query->orderByDesc('updated_at');
            }
        } else {
            $query->orderByDesc('updated_at');
        }

        if (Arr::has($parameters, 'category') && $parameters['category']) {
            $query->where('category_id', '=', Arr::get($parameters, 'category'));
        } else {
            $query->whereIn('category_id', Auth::user()->categories->pluck('id')->toArray());
        }

        if (Arr::has($parameters, 'title') && $parameters['title']) {
            $query->where('title', 'like', '%'.Arr::get($parameters, 'title').'%');
        }

        return $query->simplePaginate(15);
    }

    private function createKeepassesRecursively(KeepassRepositoryInterface $keepassRepository, Category $category, array $groups, $parentID = null)
    {
        foreach ($groups as $group) {
            $folder = $keepassRepository->create([
                'title' => (string) $group->Name,
                'category_id' => $category->id,
                'is_folder' => 1,
                'parent_id' => $parentID
            ]);
            $entries = $group->xpath('Entry');
            if ($entries && is_array($entries)) {
                foreach ($entries as $entry) {
                    $params = [
                        'title' => null,
                        'category_id' => $category->id,
                        'is_folder' => 0,
                        'parent_id' => $folder->id,
                        'login' => null,
                        'password' => null,
                        'url' => null,
                        'notes' => null,
                    ];
                    $columns = $entry->xpath('String');
                    if ($columns && is_array($columns)) {
                        /** @var \SimpleXMLElement $column */
                        foreach ($columns as $column) {
                            $keyArray = $column->xpath('Key');
                            $valueArray = $column->xpath('Value');
                            if ($keyArray && is_array($keyArray) && count($keyArray) && $valueArray && is_array($valueArray) && count($valueArray)) {
                                $key = (string) $keyArray[0];
                                switch ($key) {
                                    case 'Notes':
                                        $valueArray = $column->xpath('Value');
                                        $params['notes'] = strlen((string) $valueArray[0]) ? (string) $valueArray[0] : null;
                                        break;
                                    case 'Password':
                                        $valueArray = $column->xpath('Value');
                                        $params['password'] = strlen((string) $valueArray[0]) ? (string) $valueArray[0] : null;
                                        break;
                                    case 'Title':
                                        $valueArray = $column->xpath('Value');
                                        $params['title'] = strlen((string) $valueArray[0]) ? (string) $valueArray[0] : 'no_title';
                                        break;
                                    case 'URL':
                                        $valueArray = $column->xpath('Value');
                                        $params['url'] = strlen((string) $valueArray[0]) ? (string) $valueArray[0] : null;
                                        break;
                                    case 'UserName':
                                        $valueArray = $column->xpath('Value');
                                        $params['login'] = strlen((string) $valueArray[0]) ? (string) $valueArray[0] : null;
                                        break;
                                }
                            }
                        }
                    }
                    $this->create($params);
                }
            }
            $children = $group->xpath('Group');
            if ($children && is_array($children)) {
                $this->createKeepassesRecursively($keepassRepository, $category, $children, $folder->id);
            }
        }
    }
}

<?php


namespace App\Repositories;


use App\Interfaces\IconRepositoryInterface;
use App\Models\Icon;
use App\Models\Keepass;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class IconRepository implements IconRepositoryInterface
{
    protected $model;

    public function __construct()
    {
        $this->model = new Icon();
    }

    public function create(array $attributes = []): Model
    {
        $entity = null;
        DB::transaction(function () use ($attributes, &$entity) {
            $filename = $this->storeImage(Arr::get($attributes, 'icon'));
            $this->model = $this->model->newInstance();
            $this->model->path = 'img/icons/'.$filename;
            $this->model->filename = $filename;
            $this->model->title = Arr::get($attributes, 'title');
            $this->model->save();

            $entity = $this->model;
        });

        return $entity;
    }

    public function update(Model $entity, array $attributes = []): Model
    {
        DB::transaction(function () use (&$entity, $attributes) {
            $data = [
                'title' => Arr::get($attributes, 'title')
            ];
            if (Arr::get($attributes, 'icon')) {
                $filename = $this->storeImage(Arr::get($attributes, 'icon'));
                $data['filename'] = $filename;
                $data['path'] = 'img/icons/'.$filename;
            }
            $entity->update($data);
        });

        return $entity;
    }

    public function delete(Model $entity): bool
    {
        $deleted = false;
        DB::transaction(function () use ($entity, &$deleted) {
            Keepass::where('icon_id', '=', $entity->id)
                ->update(['icon_id' => null]);

            $deleted = $entity->delete();
        });

        return $deleted;
    }

    public function get(int $id): ?Model
    {
        // TODO: Implement get() method.
    }

    public function getSearchIcons(array $parameters = [])
    {
        $query = $this->model->newQuery();
        if (Arr::has($parameters, 'is_deletable') && $parameters['is_deletable'] !== null) {
            $query->where('is_deletable', '=', Arr::get($parameters, 'is_deletable'));
        }
        if (Arr::has($parameters, 'title') && $parameters['title']) {
            $query->where('title', 'like', '%'.Arr::get($parameters, 'title').'%');
        }
        if (Arr::has($parameters, 'filename') && $parameters['filename']) {
            $query->where('filename', 'like', '%'.Arr::get($parameters, 'filename').'%');
        }

        return $query->orderByDesc('created_at')->simplePaginate(30);
    }

    private function storeImage(UploadedFile $image) : string
    {
        $filename = File::exists(public_path('storage/img/icons/'.$image->getClientOriginalName())) ? time().'_'.$image->getClientOriginalName() : $image->getClientOriginalName();
        Storage::putFileAs('public/img/icons', $image, $filename);

        return $filename;
    }
}

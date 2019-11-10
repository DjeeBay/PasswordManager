<?php


namespace App\Repositories;


use App\Favorite;
use App\Interfaces\FavoriteRepositoryInterface;
use App\Models\Category;
use App\Models\UserCategory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FavoriteRepository implements FavoriteRepositoryInterface
{

    public function create(array $attributes = []): Model
    {
        // TODO: Implement create() method.
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

    public function getList() : Collection
    {
        $favorites = Favorite::with('keepass')
            ->where('user_id', '=', Auth::user()->id)
            ->whereHas('keepass', function (Builder $q) {
                $q->whereNull('deleted_at');
            })
            ->get();
        $favorites->each(function ($item) {
            $item->keepass->password = $item->keepass->password ? decrypt($item->keepass->password) : null;
        });

        return $favorites;
    }

    public function storeMultiple(array $keepasses)
    {
        DB::transaction(function () use ($keepasses) {
            $userID = Auth::user()->id;
            $userCategories = Auth::user()->is_admin ? Category::all()->pluck('id')->toArray() : UserCategory::where('user_id', '=', $userID)->pluck('category_id')->toArray();
            $hasRight = true;
            $favorites = [];
            foreach ($keepasses as $keepass) {
                $keepassID = Arr::get($keepass, 'id');
                $hasRight &= in_array(Arr::get($keepass, 'category_id'), $userCategories);
                if (!Favorite::where([['keepass_id', '=', $keepassID], ['user_id', '=', $userID]])->first()) {
                    array_push($favorites, [
                        'user_id' => $userID,
                        'keepass_id' => $keepassID,
                    ]);
                }
            }
            if ($hasRight) {
                Favorite::insert($favorites);
            }
        });
    }
}

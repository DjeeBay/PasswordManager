<?php


namespace App\Repositories;


use App\Favorite;
use App\Interfaces\FavoriteRepositoryInterface;
use App\Models\Category;
use App\Models\PrivateCategory;
use App\Models\UserCategory;
use App\Services\PassphraseService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Encryption\Encrypter;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class FavoriteRepository implements FavoriteRepositoryInterface
{
    private PassphraseService $passphraseService;

    public function __construct(PassphraseService $passphraseService)
    {
        $this->passphraseService = $passphraseService;
    }

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
        $privateEncrypter = $this->passphraseService->getPrivateEncrypter();

        $favorites = Favorite::with('keepass')
            ->where('user_id', '=', Auth::user()->id)
            ->whereHas('keepass', function (Builder $q) {
                $q->whereNull('deleted_at');
            })
            ->get();
        $favorites->each(function ($item) use ($privateEncrypter) {
            $item->keepass->fullpath = $item->keepass->fullpath;
            $item->keepass->password = $item->keepass->password ?
                (!$item->keepass->private_category_id ? decrypt($item->keepass->password)
                    : ($privateEncrypter?->decrypt($item->keepass->password)))
                : null;
        });

        return $favorites;
    }

    public function storeMultiple(array $keepasses, bool $isPrivate)
    {
        DB::transaction(function () use ($keepasses, $isPrivate) {
            $userID = Auth::user()->id;
            $userCategories = $isPrivate ? PrivateCategory::where('owner_id', '=', Auth::user()->id)->pluck('id')->toArray() : (Auth::user()->is_admin ? Category::all()->pluck('id')->toArray() : UserCategory::where('user_id', '=', $userID)->pluck('category_id')->toArray());
            $hasRight = true;
            $favorites = [];
            foreach ($keepasses as $keepass) {
                $keepassID = Arr::get($keepass, 'id');
                $hasRight &= in_array(Arr::get($keepass, $isPrivate ? 'private_category_id' : 'category_id'), $userCategories);
                if (!Favorite::where([['keepass_id', '=', $keepassID], ['user_id', '=', $userID]])->first()) {
                    $favorites[] = [
                        'user_id' => $userID,
                        'keepass_id' => $keepassID,
                    ];
                }
            }
            if ($hasRight) {
                Favorite::insert($favorites);
            }
        });
    }

    private function getPrivateEncrypter()
    {
        $user = Auth::user();

        return $user->passphrase_validator && Hash::check(Session::get('kpm.private_passphrase').env('KEEPASS_PASSPHRASE_VALIDATOR'), $user->passphrase_validator) ? new Encrypter($this->getPassphraseWithCorrectLength(Session::get('kpm.private_passphrase')), config('app.cipher')) : null;
    }
}

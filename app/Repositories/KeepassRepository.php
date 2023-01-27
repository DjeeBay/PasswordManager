<?php


namespace App\Repositories;

use App\Interfaces\KeepassRepositoryInterface;
use App\Models\Category;
use App\Models\Icon;
use App\Models\Keepass;
use App\Models\User;
use App\Services\PassphraseService;
use http\Encoding\Stream\Debrotli;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Encryption\Encrypter;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class KeepassRepository implements KeepassRepositoryInterface
{
    protected $model;
    private PassphraseService $passphraseService;

    public function __construct(PassphraseService $passphraseService)
    {
        $this->model = new Keepass();
        $this->passphraseService = $passphraseService;
    }

    public function create(array $attributes = []): Model
    {
        $entity = null;
        DB::transaction(function () use ($attributes, &$entity) {
            $privateCategoryId = Arr::get($attributes, 'private_category_id');
            $isPrivate = (bool)$privateCategoryId;
            $passphraseValidator = Auth::user()->passphrase_validator;
            $passphrase = Session::get('kpm.private_passphrase');
            if (env('KEEPASS_PASSPHRASE_VALIDATOR') && $isPrivate && $passphraseValidator && !Hash::check($passphrase.env('KEEPASS_PASSPHRASE_VALIDATOR'), $passphraseValidator)) {
                throw new \Exception('Incorrect passphrase');
            }
            $isPassphraseRequired = env('KEEPASS_PASSPHRASE_VALIDATOR') && $isPrivate && $passphraseValidator;
            $privateEncrypter = $isPassphraseRequired ? $this->passphraseService->getPrivateEncrypter() : null;

            $this->model = $this->model->newInstance();
            $this->model->title = Arr::get($attributes, 'title', 'no_title');
            $this->model->category_id = Arr::get($attributes, 'category_id');
            $this->model->private_category_id = $privateCategoryId;
            $this->model->is_folder = Arr::get($attributes, 'is_folder');
            $this->model->parent_id = Arr::get($attributes, 'parent_id');
            $this->model->login = Arr::get($attributes, 'login');
            $this->model->password = Arr::get($attributes, 'password') ? ($isPassphraseRequired ? $privateEncrypter->encrypt(Arr::get($attributes, 'password')) : encrypt(Arr::get($attributes, 'password'))) : null;
            $this->model->url = Arr::get($attributes, 'url');
            $this->model->notes = Arr::get($attributes, 'notes');
            $this->model->icon_id = Arr::get($attributes, 'icon_id');
            $this->model->save();
            $entity = $this->model;

            if ($entity->password) {
                $entity->password = $isPassphraseRequired ? $privateEncrypter->decrypt($entity->password) : decrypt($entity->password);
            }
        });

        return $entity;
    }

    public function update(Model $entity, array $attributes = []): Model
    {
        DB::transaction(function () use (&$entity, $attributes) {
            $privateCategoryId = Arr::get($attributes, 'private_category_id');
            $isPrivate = (bool)$privateCategoryId;
            $passphraseValidator = Auth::user()->passphrase_validator;
            $passphrase = Session::get('kpm.private_passphrase');
            if (env('KEEPASS_PASSPHRASE_VALIDATOR') && $isPrivate && $passphraseValidator && !Hash::check($passphrase.env('KEEPASS_PASSPHRASE_VALIDATOR'), $passphraseValidator)) {
                throw new \Exception('Incorrect passphrase');
            }
            $isPassphraseRequired = env('KEEPASS_PASSPHRASE_VALIDATOR') && $isPrivate && $passphraseValidator;
            $privateEncrypter = $isPassphraseRequired ? $this->passphraseService->getPrivateEncrypter() : null;


            $entity->update([
                'title' => Arr::get($attributes, 'title'),
                'category_id' => Arr::get($attributes, 'category_id'),
                'private_category_id' => $privateCategoryId,
                'is_folder' => Arr::get($attributes, 'is_folder'),
                'parent_id' => Arr::get($attributes, 'parent_id'),
                'login' => Arr::get($attributes, 'login'),
                'password' => Arr::get($attributes, 'password') ? ($isPassphraseRequired ? $privateEncrypter->encrypt(Arr::get($attributes, 'password')) : encrypt(Arr::get($attributes, 'password'))) : null,
                'url' => Arr::get($attributes, 'url'),
                'notes' => Arr::get($attributes, 'notes'),
                'icon_id' => Arr::get($attributes, 'icon_id'),
            ]);

            if ($entity->password) {
                $entity->password = $isPassphraseRequired ? $privateEncrypter->decrypt($entity->password) : decrypt($entity->password);
            }
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

    public function createMultiple(array $keepasses, $category_id, bool $isPrivate) : array
    {
        $storedKeepasses = [];
        DB::transaction(function () use ($keepasses, $category_id, &$storedKeepasses, $isPrivate) {
            foreach ($keepasses as $keepass) {
                $keepass[$isPrivate ? 'private_category_id' : 'category_id'] = $category_id;
                $createdKeepass = $this->create($keepass);
                $createdKeepass->password = $createdKeepass->password ? ($isPrivate ? $this->passphraseService->getPrivateEncrypter()->decrypt($createdKeepass->password) : decrypt($createdKeepass->password)) : null;
                $storedKeepasses[] = $createdKeepass;
            }
        });

        return $storedKeepasses;
    }

    public function encryptPrivatePasswordsWithNewPassphrase(User $user, ?string $oldPassphrase, string $newPassphrase) : void
    {
        DB::transaction(function () use ($user, $oldPassphrase, $newPassphrase) {
            $passphraseValidator = $user->passphrase_validator;
            if ($passphraseValidator && !Hash::check($oldPassphrase.env('KEEPASS_PASSPHRASE_VALIDATOR'), $passphraseValidator)) {
                throw new \Exception('Incorrect passphrase');
            }

            $oldPassphraseWithCorrectLength = $oldPassphrase ? $this->passphraseService->getPassphraseWithCorrectLength($oldPassphrase) : null;
            $newPassphraseWithCorrectLength = $this->passphraseService->getPassphraseWithCorrectLength($newPassphrase);

            $oldEncrypter = $oldPassphrase && $oldPassphraseWithCorrectLength ? new Encrypter($oldPassphraseWithCorrectLength, config('app.cipher')) : null;
            $newEncrypter = new Encrypter($newPassphraseWithCorrectLength, config('app.cipher'));

            $keepasses = Keepass::join('private_categories as pc', 'pc.id', '=', 'keepasses.private_category_id')
                ->select('keepasses.*')
                ->where([
                    ['pc.owner_id', '=', $user->id],
                    ['keepasses.is_folder', '=', 0],
                ])
                ->whereNotNull('private_category_id')
                ->whereNotNull('password')
                ->get();

            foreach ($keepasses as $entry) {
                $pwdDecrypted = $oldPassphrase ? $oldEncrypter->decrypt($entry->password) : decrypt($entry->password);
                $entry->password = $newEncrypter->encrypt($pwdDecrypted);
                $entry->save();
            }

            $newPassphraseValidator = Hash::make($newPassphrase.env('KEEPASS_PASSPHRASE_VALIDATOR'));
            $storedUser = User::findOrFail($user->id);
            $storedUser->passphrase_validator = $newPassphraseValidator;
            $storedUser->save();
        });
    }

    public function getStructuredItems($category_id, bool $isPrivate)
    {

        $rootFolders = Keepass::where($isPrivate ? 'private_category_id' : 'category_id', '=', $category_id)->where('is_folder', 1)
        ->where('parent_id', null)->get()->toArray();

        //TODO using it and splice the collection shows a slightly better performance but needs to find a correct way to splice...
//        $allItems = DB::table('keepasses')
//            ->where('category_id', '=', $category_id)
//            ->whereNotNull('parent_id')
//            ->get();


        return $this->setStructureRecusively([], $rootFolders, $isPrivate);
    }

    public function getStructuredEntryItems(Keepass $keepass)
    {
        $query = Keepass::where('is_folder', 1)
            ->where('id', $keepass->id);

        if ($keepass->private_category_id) {
            $query->where('private_category_id', '=', $keepass->private_category_id);
        } else {
            $query->where('category_id', '=', $keepass->category_id);
        }

		$folders = $query->get()->toArray();

        return $this->setStructureRecusively([], $folders, (bool)$keepass->private_category_id);
    }

    private function setStructureRecusively($allItems, array &$folders, bool $isPrivate)
    {
        $encrypter = $isPrivate ? $this->passphraseService->getPrivateEncrypter() : null;
        $foldersIDS = array_column($folders, 'id');
        $allItems = DB::table('keepasses')->whereNull(['deleted_at', 'deleted_by'])->whereIn('parent_id', $foldersIDS)->get();

        foreach ($folders as &$folder) {
            $folder = (array) $folder;
            $children = [];
            foreach ($allItems as $item) {
                if ($item->parent_id === $folder['id']) {
                    $item->password = $item->password ? ($isPrivate && $encrypter ? $encrypter->decrypt($item->password) : decrypt($item->password)) : null;
                    $children[] = $item;
                }
            }
            $folder['children'] = $children;
            $this->setStructureRecusively($allItems, $folder['children'], $isPrivate);
        }

        return $folders;
    }

    public function processXml(\SimpleXMLElement $xml, $categoryName, bool $withIcons) : bool
    {
        $imported = false;
        DB::transaction(function() use ($xml, $categoryName, $withIcons, &$imported) {
            $categoryRepository = app(CategoryRepository::class);
            $category = $categoryRepository->create(['name' => $categoryName]);

            $icons = Icon::all();
            $icons->each(function ($icon) {$icon->identifier = (string)($icon->id - 1);});
            $meta = $xml->xpath('Meta');
            if ($withIcons && $meta && count($meta)) {
                $customIcons = $meta[0]->xpath('CustomIcons');
                if ($customIcons && count($customIcons)) {
                    $iconsData = $customIcons[0]->xpath('Icon');
                    $nbIconsData = count($iconsData);
                    if ($iconsData && $nbIconsData) {
                        $modelIcon = new Icon();
                        for ($i = 0; $i < $nbIconsData; $i++) {
                            $base64 = $iconsData[$i]->xpath('Data');
                            $uuid = $iconsData[$i]->xpath('UUID');
                            if ($base64 && count($base64) && $uuid && count($uuid)) {
                                $storagePath = storage_path('app/public/');
                                $filename = uniqid($i).'.png';
                                $path = 'img/icons/'.$filename;
                                file_put_contents($storagePath.$path, base64_decode((string)$base64[0]));
                                $createdIcon = $modelIcon->create([
                                    'path' => $path,
                                    'filename' => $filename,
                                    'is_deletable' => 1
                                ]);
                                $createdIcon->identifier = (string)$uuid[0];
                                $icons->push($createdIcon);
                            }
                        }
                    }
                }
            }

            $root = $xml->xpath('Root');
            if ($root && count($root)) {
                $mainGroup = $root[0]->xpath('Group');
                if ($mainGroup && count($mainGroup)) {
                    $groups = $mainGroup[0]->xpath('Group');
                    if ($groups) {
                        $this->createKeepassesRecursively(app(KeepassRepository::class), $category, $groups, $withIcons ? $icons : collect());
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
        $query = Keepass::notPrivate()->withTrashed();

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

    private function createKeepassesRecursively(KeepassRepositoryInterface $keepassRepository, Category $category, array $groups, \Illuminate\Support\Collection $icons, $parentID = null)
    {
        foreach ($groups as $group) {
            $xmlIconID = (string) $group->CustomIconUUID && strlen((string) $group->CustomIconUUID) ? (string) $group->CustomIconUUID : (string) $group->IconID;
            $folder = $keepassRepository->create([
                'title' => (string) $group->Name,
                'category_id' => $category->id,
                'is_folder' => 1,
                'parent_id' => $parentID,
                'icon_id' => $icons->whereStrict('identifier', $xmlIconID)->pluck('id')->first()
            ]);
            $entries = $group->xpath('Entry');
            if ($entries && is_array($entries)) {
                foreach ($entries as $entry) {
                    $xmlEntryIconID = (string) $entry->CustomIconUUID && strlen((string) $entry->CustomIconUUID) ? (string) $entry->CustomIconUUID : (string) $entry->IconID;
                    $params = [
                        'title' => null,
                        'category_id' => $category->id,
                        'is_folder' => 0,
                        'parent_id' => $folder->id,
                        'login' => null,
                        'password' => null,
                        'url' => null,
                        'notes' => null,
                        'icon_id' => $icons->whereStrict('identifier', $xmlEntryIconID)->pluck('id')->first(),
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
                $this->createKeepassesRecursively($keepassRepository, $category, $children, $icons, $folder->id);
            }
        }
    }
}

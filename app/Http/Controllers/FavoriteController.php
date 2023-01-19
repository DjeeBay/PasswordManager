<?php

namespace App\Http\Controllers;

use App\Favorite;
use App\Http\Requests\Favorite\DeleteFavoritesRequest;
use App\Http\Requests\Favorite\StoreFavoritesRequest;
use App\Http\Requests\Keepass\StoreFavoritesPrivateRequest;
use App\Interfaces\FavoriteRepositoryInterface;
use App\Repositories\FavoriteRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    protected FavoriteRepositoryInterface $repository;

    public function __construct(FavoriteRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function index(Request $request)
    {
        return view('favorite.index')
            ->withFavorites($this->repository->getList());
    }

    public function addMultiple(StoreFavoritesRequest $request)
    {
        return $this->addMultipleKeepasses($request, false);
    }

    public function addMultiplePrivate(StoreFavoritesPrivateRequest $request)
    {
        return $this->addMultipleKeepasses($request, true);
    }

    public function removeMultiple(DeleteFavoritesRequest $request)
    {
        Favorite::whereIn('keepass_id', $request->json('keepasses.*.id'))
            ->where('user_id', '=', Auth::user()->id)
            ->delete();

        return response()->json(true);
    }

    private function addMultipleKeepasses(Request $request, bool $isPrivate)
    {
        $this->repository->storeMultiple($request->keepasses, $isPrivate);

        return response()->json(true);
    }
}

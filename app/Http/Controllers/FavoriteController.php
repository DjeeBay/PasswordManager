<?php

namespace App\Http\Controllers;

use App\Favorite;
use App\Http\Requests\Favorite\DeleteFavoritesRequest;
use App\Http\Requests\Favorite\StoreFavoritesRequest;
use App\Interfaces\FavoriteRepositoryInterface;
use App\Repositories\FavoriteRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    /** @var FavoriteRepository $repository */
    protected $repository;

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
        $this->repository->storeMultiple($request->keepasses);

        return response()->json(true);
    }

    public function removeMultiple(DeleteFavoritesRequest $request)
    {
        Favorite::whereIn('keepass_id', $request->json('keepasses.*.id'))
            ->where('user_id', '=', Auth::user()->id)
            ->delete();

        return response()->json(true);
    }
}

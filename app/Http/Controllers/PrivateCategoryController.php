<?php

namespace App\Http\Controllers;

use App\Http\Requests\PrivateCategory\CanReadPrivateCategoryRequest;
use App\Http\Requests\PrivateCategory\SavePrivateCategoryRequest;
use App\Interfaces\PrivateCategoryRepositoryInterface;
use App\Models\PrivateCategory;
use App\Repositories\PrivateCategoryRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class PrivateCategoryController extends Controller
{
    private PrivateCategoryRepositoryInterface $repository;

    public function __construct(PrivateCategoryRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('private_category.index')
            ->withCategories(PrivateCategory::where('owner_id', '=', Auth::user()->id)->orderBy('name')->get());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('private_category.form');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param SavePrivateCategoryRequest $request
     * @return Response
     */
    public function store(SavePrivateCategoryRequest $request)
    {
        $this->repository->create($request->all());

        return redirect()->route('private-category.index')
            ->withSuccess('The private category has been created !');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\PrivateCategory  $privateCategory
     * @return \Illuminate\Http\Response
     */
    public function show(PrivateCategory $privateCategory)
    {
        return redirect()->route('private-category.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param CanReadPrivateCategoryRequest $request
     * @param PrivateCategory $privateCategory
     * @return Response
     */
    public function edit(CanReadPrivateCategoryRequest $request, PrivateCategory $privateCategory)
    {
        return view('private_category.form')
            ->withCategory($privateCategory);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param SavePrivateCategoryRequest $request
     * @param PrivateCategory $privateCategory
     * @return Response
     */
    public function update(SavePrivateCategoryRequest $request, PrivateCategory $privateCategory)
    {
        $category = $this->repository->update($privateCategory, $request->all());

        return redirect()->route('private-category.index')
            ->withSuccess('Private category '.$category->name.' has been updated.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param CanReadPrivateCategoryRequest $request
     * @param PrivateCategory $privateCategory
     * @return JsonResponse
     */
    public function destroy(CanReadPrivateCategoryRequest $request, PrivateCategory $privateCategory)
    {
        $this->repository->delete($privateCategory);
        session()->flash('success', 'Private category has been deleted.');

        return response()->json(['redirect' => route('private-category.index')]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaveCategoryRequest;
use App\Models\Category;
use App\Models\User;
use App\Repositories\CategoryRepository;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    /** @var CategoryRepository $repository */
    protected $repository;

    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->repository = $categoryRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $categories = Auth::user()->is_admin ? Category::all() : Auth::user()->categories;

        return view('category.index')
            ->withCategories($categories);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (Auth::user()->is_admin || Auth::user()->can('create category')) {
            return view('category.form')
                ->withUsers(User::where('is_admin', '=', 0)->get());
        }

        return redirect()->route('home');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param SaveCategoryRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(SaveCategoryRequest $request)
    {
        $this->repository->create($request->all());

        return redirect()->route('category.index')
            ->withSuccess('The category has been created !');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return redirect()->route('category.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (Auth::user()->is_admin || (Auth::user()->can('edit category') && Auth::user()->categories->where('id', $id)->first())) {
            return view('category.form')
                ->withCategory(Category::findOrFail($id))
                ->withUsers(User::where('is_admin', '=', 0)->get());
        }

        return redirect()->route('home');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  SaveCategoryRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(SaveCategoryRequest $request, $id)
    {
        $category = $this->repository->update(Category::findOrFail($id), $request->all());

        return redirect()->route('category.index')
            ->withSuccess('Category '.$category->name.' has been updated.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (Auth::user()->is_admin || (Auth::user()->can('delete category') && Auth::user()->categories->where('id', $id)->first())) {
            $this->repository->delete(Category::findOrFail($id));
            session()->flash('success', 'Category has been deleted.');

            return response()->json(['redirect' => route('category.index')]);
        }
    }
}

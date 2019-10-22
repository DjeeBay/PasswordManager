<?php

namespace App\Http\Controllers;

use App\Http\Requests\Keepass\DeleteKeepassRequest;
use App\Http\Requests\Keepass\GetKeepassRequest;
use App\Http\Requests\Keepass\SaveKeepassRequest;
use App\Interfaces\KeepassRepositoryInterface;
use App\Models\Category;
use App\Models\Keepass;
use App\Repositories\KeepassRepository;
use Illuminate\Http\Request;

class KeepassController extends Controller
{
    /**
     * @var KeepassRepository $repository
     */
    protected $repository;

    public function __construct(KeepassRepositoryInterface $keepassRepository)
    {
        $this->repository = $keepassRepository;
    }

    public function get(GetKeepassRequest $request, $category_id)
    {
        return view('keepass.list')
            ->withCategory(Category::findOrFail($category_id))
            ->withItems($this->repository->getStructuredItems($category_id));
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return void
     */
    public function index(Request $request)
    {
        return redirect()->route('home');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function save(SaveKeepassRequest $request, $category_id)
    {
        $attributes = $request->keepass;
        $attributes['category_id'] = $category_id;
        $keepass = !$request->has('keepass.id') ? $this->repository->create($attributes) : $this->repository->update(Keepass::findOrFail($request->json('keepass.id')), $attributes);
        $keepass->password = $keepass->password ? decrypt($keepass->password) : null;

        return response()->json(['keepass' => $keepass]);
    }

    public function delete(DeleteKeepassRequest $request, $category_id, $id)
    {
        $deleted = $this->repository->delete(Keepass::findOrFail($id));

        return response()->json($deleted);
    }
}

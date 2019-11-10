<?php

namespace App\Http\Controllers;

use App\Http\Requests\Keepass\CreateMultipleKeepassesRequest;
use App\Http\Requests\Keepass\DeleteKeepassRequest;
use App\Http\Requests\Keepass\GetKeepassRequest;
use App\Http\Requests\Keepass\ImportKeepassRequest;
use App\Http\Requests\Keepass\SaveKeepassRequest;
use App\Interfaces\KeepassRepositoryInterface;
use App\Models\Category;
use App\Models\Keepass;
use App\Repositories\KeepassRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        $keepass = !$request->has('keepass.id') || !$request->json('keepass.id') ? $this->repository->create($attributes) : $this->repository->update(Keepass::findOrFail($request->json('keepass.id')), $attributes);
        $keepass->password = $keepass->password ? decrypt($keepass->password) : null;

        return response()->json(['keepass' => $keepass]);
    }

    public function createMultiple(CreateMultipleKeepassesRequest $request, $category_id)
    {
        return response()->json(['keepasses' => $this->repository->createMultiple($request->keepasses, $category_id)]);
    }

    public function delete(DeleteKeepassRequest $request, $category_id, $id)
    {
        $deleted = $this->repository->delete(Keepass::findOrFail($id));

        return response()->json($deleted);
    }

    public function getImport()
    {
        if (Auth::user()->is_admin || Auth::user()->can('import keepass')) {
            return view('keepass.import');
        }

        return redirect()->route('home');
    }

    public function import(ImportKeepassRequest $request)
    {
        $xml = simplexml_load_file($request->file('xml'));
        if (!$xml || !$this->repository->processXml($xml, $request->category_name, $request->with_icons ? true : false)) {
            return back()->withErrors(['Invalid xml.']);
        }

        return redirect()->route('home')->withSuccess('XML successfully imported !');
    }
}

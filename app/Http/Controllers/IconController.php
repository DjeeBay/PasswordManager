<?php

namespace App\Http\Controllers;

use App\Http\Requests\Icon\SaveIconRequest;
use App\Interfaces\IconRepositoryInterface;
use App\Models\Icon;
use App\Repositories\IconRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IconController extends Controller
{
    /** @var IconRepository $repository */
    protected $repository;

    public function __construct(IconRepositoryInterface $iconRepository)
    {
        $this->repository = $iconRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (Auth::user()->is_admin || Auth::user()->can('create keepass') || Auth::user()->can('edit keepass')) {
            $icons = $this->repository->getSearchIcons($request->all());

            return view('icon.index')->withIcons($icons);
        }

        return redirect()->route('home');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (Auth::user()->is_admin || Auth::user()->can('create keepass') || Auth::user()->can('edit keepass')) {
            return view('icon.form');
        }

        return redirect()->route('home');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(SaveIconRequest $request)
    {
        $this->repository->create($request->all());

        return redirect()->route('icon.index')
            ->withSuccess('The icon has been created !');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return redirect()->route('icon.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (Auth::user()->is_admin || Auth::user()->can('create keepass') || Auth::user()->can('edit keepass')) {
            return view('icon.form')->withIcon(Icon::findOrFail($id));
        }

        return redirect()->route('home');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(SaveIconRequest $request, $id)
    {
        $icon = $this->repository->update(Icon::findOrFail($id), $request->all());

        return redirect()->route('icon.index')
            ->withSuccess('Icon '.$icon->filename.' has been updated.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (Auth::user()->is_admin || Auth::user()->can('edit keepass') || Auth::user()->can('create keepass')) {
            $this->repository->delete(Icon::findOrFail($id));
            session()->flash('success', 'Icon has been deleted.');

            return response()->json(['redirect' => route('icon.index')]);
        }
    }

    public function add(SaveIconRequest $request)
    {
        $icon = $this->repository->create($request->all());

        return response()->json(['icon' => $icon]);
    }
}

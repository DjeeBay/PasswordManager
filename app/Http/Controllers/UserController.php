<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\SaveUserRequest;
use App\Interfaces\UserRepositoryInterface;
use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /** @var UserRepositoryInterface $repository */
    protected $repository;

    public function __construct(UserRepository $userRepository)
    {
        $this->repository = $userRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('user.index')
            ->withUsers(User::all());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('user.form');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(SaveUserRequest $request)
    {
        if (($request->password || $request->password_confirmation) && $request->password !== $request->password_confirmation) {
            session()->flashInput($request->input());

            return back()
                ->withErrors(['Passwords must be identical'])
                ->withUsers(User::all());
        }
        $this->repository->create($request->all());

        return view('user.index')
            ->withUsers(User::all())
            ->withSuccess('The use has been created !');
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
        return view('user.form')
            ->withUser(User::find($id));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  SaveUserRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(SaveUserRequest $request, $id)
    {
        if (($request->password || $request->password_confirmation) && $request->password !== $request->password_confirmation) {
            session()->flashInput($request->input());

            return back()
                ->withErrors(['Passwords must be identical'])
                ->withUsers(User::all());
        }
        $this->repository->update(User::find($id), $request->all());

        return view('user.index')->withUsers(User::all());
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
}

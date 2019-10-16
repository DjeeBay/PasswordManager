<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\SaveUserRequest;
use App\Interfaces\UserRepositoryInterface;
use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        if (Auth::user()->is_admin || Auth::user()->can('create user')) {
            return view('user.form');
        }

        return redirect()->route('home');
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
        if (Auth::user()->is_admin || Auth::user()->can('edit user') || Auth::user()->id === intval($id)) {
            return view('user.form')
                ->withUser(User::find($id));
        }

        return redirect()->route('home');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param SaveUserRequest $request
     * @param int $id
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function update(SaveUserRequest $request, $id)
    {
        if (($request->password || $request->password_confirmation) && $request->password !== $request->password_confirmation) {
            session()->flashInput($request->input());

            return back()
                ->withErrors(['Passwords must be identical'])
                ->withUsers(User::all());
        }
        $attributes = $request->all();
        /** @var User $user */
        $user = User::find($id);
        if (!Auth::user()->is_admin && !Auth::user()->can('manage user permissions')) {
            $attributes['is_admin'] = $user->is_admin;
            $attributes['permissions'] = [];
            foreach ($user->getAllPermissions() as $permission) {
                array_push($attributes['permissions'], $permission->name);
            }
        }
        $this->repository->update($user, $attributes);

        return view('user.index')->withUsers(User::all())->withSuccess('User #'.$id.' has been updated.');
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

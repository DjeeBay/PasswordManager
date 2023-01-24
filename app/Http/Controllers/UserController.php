<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\SaveUserRequest;
use App\Http\Requests\User\UpdatePassphraseRequest;
use App\Interfaces\KeepassRepositoryInterface;
use App\Interfaces\UserRepositoryInterface;
use App\Models\Category;
use App\Models\User;
use App\Repositories\KeepassRepository;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use MongoDB\Driver\Session;

class UserController extends Controller
{
    /** @var UserRepositoryInterface $repository */
    protected $repository;

    private KeepassRepositoryInterface $keepassRepository;

    public function __construct(UserRepository $userRepository, KeepassRepository $keepassRepository)
    {
        $this->repository = $userRepository;
        $this->keepassRepository = $keepassRepository;
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
            return view('user.form')
                ->withCategories(Auth::user()->is_admin ? Category::all() : Category::where('restricted', '=', 0)->get());
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
                ->withErrors(['Passwords must be identical']);
        }
        $this->repository->create($request->all());

        return redirect()->route('user.index')
            ->withSuccess('The user has been created !');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return redirect()->route('user.index');
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
                ->withUser(User::find($id))
                ->withCategories(Auth::user()->is_admin ? Category::all() : Category::where('restricted', '=', 0)->get());
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
                ->withErrors(['Passwords must be identical']);
        }
        /** @var User $user */
        $user = User::find($id);
        if ($request->password && $user->passphrase_validator && !Hash::check($request->passphrase, $user->passphrase_validator)) {
            return back()
                ->withErrors(['Invalid passphrase']);
        }

        $attributes = $request->all();

        if (!Auth::user()->is_admin && !Auth::user()->can('manage user permissions')) {
            $attributes['is_admin'] = $user->is_admin;
            $attributes['permissions'] = [];
            foreach ($user->getAllPermissions() as $permission) {
                array_push($attributes['permissions'], $permission->name);
            }
        }
        $user = $this->repository->update($user, $attributes);

        return redirect()->route('user.index')
            ->withSuccess('User '.$user->name.' has been updated.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (Auth::user()->is_admin || Auth::user()->can('delete user') && intval($id) !== Auth::user()->id) {
            $this->repository->delete(User::findOrFail($id));
            session()->flash('success', 'User has been deleted.');

            return response()->json(['redirect' => route('user.index')]);
        }
    }

    public function updatePassphrase(UpdatePassphraseRequest $request)
    {
        if (!env('KEEPASS_PASSPHRASE_VALIDATOR')) {
            return back()
                ->withErrors(['This KeepassManager is not configured to use the passphrase']);
        }
        $authUser = Auth::user();
        if ($authUser->passphrase_validator && !Hash::check($request->old_passphrase.env('KEEPASS_PASSPHRASE_VALIDATOR'), $authUser->passphrase_validator)) {
            return back()
                ->withErrors(['The old passphrase is incorrect']);
        }

        try {
            $this->keepassRepository->encryptPrivatePasswordsWithNewPassphrase($authUser, $request->old_passphrase, $request->new_passphrase);
            \Illuminate\Support\Facades\Session::put('kpm.private_passphrase', $request->new_passphrase);
            return redirect()->route('user.index')
                ->withSuccess('Your passphrase has been updated. Please keep it safely.');
        } catch (\Exception $e) {
            return back()
                ->withErrors([$e->getMessage()]);
        }
    }
}

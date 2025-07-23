<?php

namespace App\Http\Controllers;

use App\DataTables\UserDataTable;
use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Controllers\AppBaseController;
use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class UserController extends AppBaseController
{
    /** @var UserRepository $userRepository*/
    private $userRepository;

    public function __construct(UserRepository $userRepo)
    {
        $this->userRepository = $userRepo;
    }

    /**
     * Display a listing of the User.
     */
    public function index(UserDataTable $userDataTable)
    {
    return $userDataTable->render('users.index');
    }


    /**
     * Show the form for creating a new User.
     */
    public function create()
    {
        return view('users.create');
    }

    /**
     * Store a newly created User in storage.
     */
    public function store(CreateUserRequest $request)
    {
        $input = $request->all();

        $user = $this->userRepository->create($input);

        Alert::success('User Created', 'The user was added successfully.');

        return redirect(route('users.index'));
    }

    /**
     * Display the specified User.
     */
    public function show($id)
    {
        $user = $this->userRepository->find($id);

        if (empty($user)) {
            Alert::error('User not found');

            return redirect(route('users.index'));
        }

        return view('users.show')->with('user', $user);
    }

    /**
     * Show the form for editing the specified User.
     */
    public function edit($id)
    {
        $user = $this->userRepository->find($id);

        if (empty($user)) {
            Alert::error('User not found');

            return redirect(route('users.index'));
        }

        return view('users.edit')->with('user', $user);
    }

    /**
     * Update the specified User in storage.
     */
    public function update($id, UpdateUserRequest $request)
    {
        $user = $this->userRepository->find($id);

        if (empty($user)) {
            Alert::error('User not found');

            return redirect(route('users.index'));
        }

        $user = $this->userRepository->update($request->all(), $id);

        Alert::success('User updated successfully.');

        return redirect(route('users.index'));
    }

    /**
     * Remove the specified User from storage.
     *
     * @throws \Exception
     */
    public function destroy($id)
    {
        $user = $this->userRepository->find($id);

        if (empty($user)) {
            Alert::error('User not found');

            return redirect(route('users.index'));
        }

        $this->userRepository->delete($id);

        Alert::success('User deleted successfully.');

        return redirect(route('users.index'));
    }
    public function changePasswordForm($id)
{
    $user = User::findOrFail($id);
    return view('users.change_password', compact('user'));
}
public function updatePassword(Request $request)
{
    $request->validate([
        'user_id' => 'required|exists:users,id',
        'new_password' => 'required|string|min:6|confirmed',
    ]);

    $user = User::findOrFail($request->user_id);
    $user->password = bcrypt($request->new_password);
    $user->save();

    return redirect()->back()->with('success', 'Password updated successfully.');
}

}

<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Yajra\DataTables\Facades\DataTables;

class UsersController extends Controller
{
    private $title = 'label.user';
    private $icon = 'mdi mdi-account';
    private $dir = 'backend.user.';

    public function index()
    {
        return view($this->dir . 'index', [
            'title' => __($this->title),
            'icon' => $this->icon,
        ]);
    }

    public function dataTable()
    {
        $users = User::select(['id', 'name', 'email', 'phone'])
            ->get()
            ->map(function ($user) {
                $user->encrypted_id = Crypt::encrypt($user->id);
                return $user;
            });
        return DataTables::of($users)
            ->addIndexColumn()
            ->make(true);
    }

    public function create()
    {
        return view($this->dir . 'create', [
            'title' => __('label.create') . ' ' . __($this->title),
            'icon' => $this->icon,
        ]);
    }

    public function store(UserRequest $request)
    {
        $data = $request->all();
        $data['password'] = bcrypt($data['password']);
        User::create($data);
        $message = __('message.create_success');
        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => $message]);
        }

        return redirect()->route('user.index')->with('success', $message);
    }

    public function edit(User $user)
    {
        return view($this->dir . 'edit', [
            'title' => __('label.edit') . ' ' . __($this->title),
            'icon' => $this->icon,
            'user' => $user,
        ]);
    }

    public function update(UserRequest $request, User $user)
    {
        $data = $request->all();
        if ($request->password) {
            $data['password'] = bcrypt($data['password']);
        } else {
            unset($data['password']);
        }
        $user->update($data);
        if ($request->ajax()) {
            return response()->json(['success' => __('message.update_success')]);
        }

        return redirect()->route('user.index')->with('success', __('message.update_success'));
    }

    public function destroy(User $user)
    {
        try {
            // $user->delete();
            $user->forceDelete();
            return response()->json(['success' => __('message.delete_success')]);
        } catch (\Exception $e) {
            return response()->json(['error' => __('message.delete_error')], 500);
        }
    }
}

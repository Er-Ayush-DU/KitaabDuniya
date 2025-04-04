<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Validator;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class UserController extends Controller implements HasMiddleware
{
    public static function middleware()
    {
        return [
            new Middleware('permission:view users', only: ['index']),
            new Middleware('permission:edit users', only: ['edit']),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::latest()->paginate(10);
        return view("users.index", [
            "users" => $users,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $user = User::findOrFail($id);
        $roles = Role::orderBy('name', 'ASC')->get();
        $hasRoles = $user->roles->pluck('id');
        return view("users.edit", [
            "user" => $user,
            'roles' => $roles,
            'hasRoles' => $hasRoles,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|min:3',
            'email' => 'required|email|unique:users,email,' . $id,
            'phone' => 'required|min:10|max:15',
            'address' => 'required|string|max:255',
            'gender' => 'required|in:male,female,other',
        ]);

        if ($validator->fails()) {
            return redirect()->route('users.edit', $id)->withInput()->withErrors($validator);
        }

        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->address = $request->address;
        $user->gender = $request->gender;
        $user->save();

        $user->syncRoles($request->role);

        return redirect()->route('users.index', $id)->with('success', 'User updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}

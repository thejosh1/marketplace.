<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\MessageBag;
use App\Http\Resources\UserResources;
use App\Role;

class UsersController extends Controller
{
    public function signUp(Request $request)
    {
        $errors = new MessageBag();

        $errors->add(401, 'Failed to sign in, Incorrect username or password');

        $validator = Validator::make($request->all(), [
            'first_name' => 'required|max:250',
            'last_name' => 'required|max:250',
            'email' => 'required|unique',
            'username' => 'requiredf|max:150',
            'password' => 'required|min:8',
            'gender' => 'required|enum',
            'phone' => 'required|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages, 422);
        }

        $email = $request['email'];
        $first_name = $request['first_name'];
        $last_name = $request['last_name'];
        $username = $request['username']->nullable();
        $password = bcrypt($request['password']);
        $phone = $request['phone'];
        $gender = $request['gender'];


        $user = new User();
        $user->email = $email;
        $user->first_name = $first_name;
        $user->last_name = $last_name;
        $user->username = $username;
        $user->password = $password;
        $user->phone = $phone;
        $user->gender = $gender;

        if ($user->save()) {
            Auth::login($user);
            return response()->json([
                'data' => true
            ], 201);
        } else {
            return response()->json($errors);
        }
    }

    public function signIn(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|unique',
            'username' => 'required|max:150',
            'password' => 'required|min:8'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages, 422);
        }

        if (Auth::attempt(['email' => $request['email'], 'username' => $request['username'], 'password' => bcrypt($request['password'])])) {
            return redirect()->route('dashboard');
        }
    }

    public function getAdmin(Request $request)
    {
        $admin_id = Auth::user()->id;
        $this->$admin_id = User::where('role_id', $admin_id);
        $admin = Auth::user()->find($admin_id);

        return response()->json([
            'data' => true
        ], 200);
    }

    public function getUser(Request $request)
    {
        $user = Auth::user();
    }

    public function getLogout(Request $request)
    {
        Auth::logout();
        return redirect()->route('dashboard');
    }

    public function userRoles(Request $request)
    {
        $admin_id = Auth::user()->id;
        $admin = User::find($admin_id);
        $id = (int)$request['id'];
        $roles = Role::find($id);

        if ($admin) {
            $roles = Auth::user()->setRoles($admin->$roles);
            return response()->json([
                'data' => $roles
            ], 200);
        } else {
            return response()->json(false, 500);
        }
    }
}

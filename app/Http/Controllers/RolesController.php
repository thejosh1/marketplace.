<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Http\Middleware\isAdmin;
use App\Role;
use App\User;



class RolesController extends Controller
{
    public function createRole(Request $request)
    {
        $validationMessages = [
            'required' => 'The :attribute field id required',
            'exists' => 'The specified :attribute reference_id does not exist',
            'integer' => 'The :attribute is of invalid type',
        ];

        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'display_name' => 'required|string'
        ], $validationMessages);

        if ($validator->fails()) {
            return response()->json($validator->messages, 422);
        }

        $user = User::pluck('first_name');
        $getUser = Auth::user()->id;

        $data = collect(request()->all()->except('name'))->toArray;
        //collect all except name so that we can check if the name exists
        $name = $user;
        if (!$name === $user) {
            return response()->json([
                'errors' => 'Sorry the name does not exist'
            ], 404);
        } else {
            $data['name'] = $name->attach($getUser);
        }

        $result = Role::create($data);

        if ($result) {
            return response()->json([
                'data' => true
            ], 200);
        } else {
            return response()->json(false, 500);
        }
    }

    public function get(Request $request)
    {
        $id = (int)$request->route('id');
        if ($role = Role::find($id)) {
            return response()->json([
                'data' => $role
            ], 201);
        } else {
            return response()->json(false, 400);
        }
    }

    public function update(Request $request)
    {
        $validationMessages = [
            'required' => 'The :attribute field id required',
            'exists' => 'The specified :attribute reference_id does not exist',
            'integer' => 'The :attribute is of invalid type',
        ];

        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'display_name' => 'required|string'
        ], $validationMessages);

        if ($validator->fails()) {
            return response()->json($validator->messages, 422);
        }

        $data = collect(request()->all())->toArray();
        $data['user_id'] = Auth::user()->where('first_name', 'id')->first();
        $id = (int)$request->route('id');
        $role = Role::find($id);
        $result = $role()->update($data);

        if ($result) {
            return response()->json([
                'data' => true
            ], 200);
        } else {
            return response()->json(false, 404);
        }
    }

    public function deleteRole(Request $request)
    {
        $id = (int)$request->route('id');
        if ($role = Role::find($id)) {
            $role->delete;
        } else {
            return response()->json([
                'errors' => 'An unknown error occurred'
            ], 400);
        }
    }

    public function deleteUserFromRole(Request $request)
    {
        $id = (int)$request->route('id');
        if ($id) {
            $user = User::find($id);
        }

        if ($user) {
            $name = $user()->get('role')->first();
            if (isset($name)) {
                $name->delete;
                return response()->json([
                    'data' => true
                ], 200);
            } else {
                return response()->json(false, 404);
            }
        } else {
            return response()->json([
                'errors' => 'An unknown error occured'
            ], 400);
        }
    }
}

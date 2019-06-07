<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Validator;
use DB;

class ApiAuthController extends Controller
{
    use SendsPasswordResetEmails;

    public $successStatus = 200;

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required:50',
            'last_name' => 'required:50',
            'email' => 'required|email',
            'username' => 'required:100',
            'contact' => 'required|max:20',
            'password' => 'required',
            'confirm_password' => 'required|same:password',
        ]);

        if ($validator->fails) {
            return response()->json([
                'error' => $validator->errors
            ], 400);
        }

        $data = $request->all()->toArray();
        $data['password'] = bcrypt($data['password']);
        $user = User::create($data);
        $success['token'] = $user->createToken('AppName')->accessToken;
        return response()->json([
            'success' => $success
        ], $this->successStatus);
    }

    public function login(Request $request)
    {
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();
            $success['token'] = $user->createToken('AppName')->accessToken;
            return response()->json([
                'success' => $success
            ], $this->successStatus);
        } else {
            return response()->json([
                'error' => 'unauthorized action'
            ], 401);
        }
    }

    public function forgotPassword(Request $request)
    {
        $data = Auth::attempt(['email' => $request->email, 'password' => $request->password]);
        $password = DB::table('users')->get('password');
        $email = DB::table('users')->get('email');
        if (!$data['password'] === $password && $data['email'] === $email) {
            $result =  SendsPasswordResetEmails;
        }
    }
}

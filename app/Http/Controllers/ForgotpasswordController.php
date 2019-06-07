<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Support\Facades\Password;

class ForgotpasswordController extends Controller
{
    use SendsPasswordResetEmails;
    
    public function __construct()
    {
        $this->middleware('guest');
    }

    public function __invoke(Request $request)
    {
        $this->validateEmail($request);

        $response = $this->broker()->sendResetLink(
            $request->only('email')
        );

        return $response == PASSWORD::RESET_LINK_SENT
                ? response()->json(['message' => 'reset link sent to your email', 'status' => true], 201)
                : response()->json(['message' => 'umable to send reset link', 'status' => false], 401);
    }
}

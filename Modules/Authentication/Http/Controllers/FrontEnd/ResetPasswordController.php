<?php

namespace Modules\Authentication\Http\Controllers\FrontEnd;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Authentication\Foundation\Authentication;
use Modules\Authentication\Http\Requests\FrontEnd\ResetPasswordRequest;
use Modules\Authentication\Repositories\FrontEnd\AuthenticationRepository as AuthenticationRepo;
use Modules\User\Entities\PasswordReset;

class ResetPasswordController extends Controller
{
    use Authentication;

    function __construct(AuthenticationRepo $auth)
    {
        $this->auth = $auth;
    }

    public function resetPassword($token)
    {
        abort_unless(PasswordReset::where('token', $token)->first(), 419);
        abort_unless(PasswordReset::where([
            'token' => $token,
            'email' => request('email'),
        ])->first(), 419);

        return view('authentication::frontend.auth.passwords.reset', compact('token'));
    }


    public function updatePassword(ResetPasswordRequest $request)
    {
        abort_unless(PasswordReset::where('token', $request->token)->first(), 419);
        abort_unless(PasswordReset::where([
            'token' => $request->token,
            'email' => $request->email,
        ])->first(), 419);

        $reset = $this->auth->resetPassword($request);
        $errors = $this->login($request);
        if ($errors)
            return redirect()->back()->withErrors($errors)->withInput($request->except('password'));

        return redirect()->route('frontend.home')->with(['status' => 'Password Reset Successfully']);
    }
}

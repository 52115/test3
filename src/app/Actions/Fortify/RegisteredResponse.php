<?php

namespace App\Actions\Fortify;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Laravel\Fortify\Contracts\RegisterResponse as RegisterResponseContract;

class RegisteredResponse implements RegisterResponseContract
{
    /**
     * Create an HTTP response that represents the object.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function toResponse($request)
    {
        $user = $request->user();

        // メール認証が必要な場合はメール認証ページへ、不要な場合はプロフィール設定画面へ
        if ($user instanceof MustVerifyEmail && !$user->hasVerifiedEmail()) {
            return redirect()->route('auth.email-verification-notice');
        }

        return redirect()->route('profile.initial-setup');
    }
}


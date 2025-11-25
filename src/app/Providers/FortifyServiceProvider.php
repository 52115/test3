<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Http\Requests\Auth\LoginRequest;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use App\Models\User;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Actions\RedirectIfTwoFactorAuthenticatable;
use Laravel\Fortify\Contracts\LoginViewResponse;
use Laravel\Fortify\Contracts\RegisterResponse;
use Laravel\Fortify\Contracts\VerifyEmailResponse;
use Laravel\Fortify\Fortify;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(LoginViewResponse::class, function ($app) {
            return new \App\Actions\Fortify\LoginViewResponse();
        });

        $this->app->singleton(RegisterResponse::class, function ($app) {
            return new \App\Actions\Fortify\RegisteredResponse();
        });

        $this->app->singleton(VerifyEmailResponse::class, function ($app) {
            return new \App\Actions\Fortify\VerifiedEmailResponse();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);
        Fortify::redirectUserForTwoFactorAuthenticationUsing(RedirectIfTwoFactorAuthenticatable::class);

        Fortify::registerView(fn () => view('auth.register'));
        Fortify::verifyEmailView(fn () => view('auth.verify-email'));

        Fortify::authenticateUsing(function (Request $request) {
            // 言語を日本語に設定
            app()->setLocale('ja');
            
            $credentials = LoginRequest::validateData($request->only('email', 'password'));
            $user = User::where('email', $credentials['email'])->first();

            if ($user && Hash::check($credentials['password'], $user->password)) {
                return $user;
            }

            throw ValidationException::withMessages([
                'email' => 'ログイン情報が登録されていません',
            ]);
        });

        RateLimiter::for('login', function (Request $request) {
            $throttleKey = Str::transliterate(Str::lower($request->input(Fortify::username())).'|'.$request->ip());

            return Limit::perMinute(5)->by($throttleKey);
        });

        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });
    }
}

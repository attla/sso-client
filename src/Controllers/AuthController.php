<?php

namespace Attla\SSO\Controllers;

use App\Http\Controllers\Controller;
use Attla\SSO\Resolver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function callback(Request $request)
    {
        $config = config();
        $defaultRedirect = Resolver::redirect($config->get('sso.default_redirect'));

        if ($user = Resolver::getUser($request)) {
            Auth::login($user, $config->get('sso.remember'));

            return redirect(Resolver::getRedirectFromRequest($request, $defaultRedirect));
        }

        return redirect($defaultRedirect);
    }

    public function logout()
    {
        Auth::logout();

        return redirect(Resolver::redirect(config('sso.default_redirect')));
    }
}

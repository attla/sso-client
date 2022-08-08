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
        $defaultRoute = $config->get('sso.default_route');

        if ($user = Resolver::getUser($request)) {
            Auth::login($user, $config->get('sso.tll'));

            return $request->has('redirect') ? redirect($request->redirect) : to_route($defaultRoute);
        }

        return to_route($defaultRoute);
    }

    public function logout()
    {
        Auth::logout();

        return to_route(config('sso.default_route'));
    }
}

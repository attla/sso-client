<?php

namespace Attla\SSO\Controllers;

use Attla\Controller;
use Attla\SSO\Resolver;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function callback(Request $request)
    {
        $defaultRoute = config('sso.default_route');

        if ($user = Resolver::getUser($request)) {
            \Auth::login($user, config('sso.tll'));

            return $request->has('redirect') ? redirect($request->redirect) : to_route($defaultRoute);
        }

        return to_route($defaultRoute);
    }

    public function logout()
    {
        \Auth::logout();

        return to_route(config('sso.default_route'));
    }
}

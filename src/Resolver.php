<?php

namespace Attla\SSO;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Contracts\Auth\Authenticatable;

class Resolver extends \Attla\Encrypter
{
    /**
     * Get user from sso callback
     *
     * @param Request $request
     * @return Authenticatable|false
     */
    public static function getUser(Request $request)
    {
        if ($data = static::jwtDecode($request->token)) {
            return new User($data);
        }

        return false;
    }

    /**
     * Make a sso logout
     *
     * @param Request $request
     * @return void
     */
    public static function logout(Request $request)
    {
        return redirect(config('sso.route.logout') . '?client=' . $request->root());
    }
}

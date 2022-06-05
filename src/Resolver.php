<?php

namespace Attla\SSO;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Contracts\Auth\Authenticatable;

class Resolver
{
    /**
     * Transform endpoint to SSO server url.
     *
     * @param string $endpoint
     * @return string
     */
    public static function link(string $endpoint = '', $redirect = ''): string
    {
        $server = trim(config('sso.server', 'http://127.0.0.1'), '/') . '/';

        return $server . trim(trim($endpoint), '/')
            . '?client=' . urlencode($_SERVER['HTTP_HOST'] ?? '')
            . '&r=' . urlencode(trim(trim((string) $redirect), '/') ?: static::redirect());
    }

    /**
     * Get SSO end redirect uri
     *
     * @return string
     */
    public static function redirect()
    {
        $redirect = config('sso.redirect', '/');
        return route()->has($route = trim(trim($redirect), '/.-'))
            ? route($route)
            : $redirect;
    }

    /**
     * Get user from sso callback
     *
     * @param Request $request
     * @return Authenticatable|false
     */
    public static function getUser(Request $request)
    {
        if ($data = \Jwt::decode($request->token)) {
            return new User($data);
        }

        return false;
    }

    /**
     * Make a sso logout
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public static function logout(Request $request)
    {
        return redirect(static::link(
            config('sso.route.logout', 'logout'),
            $request->redirect ?: $request->r ?: ''
        ));
    }
}

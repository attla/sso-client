<?php

namespace Attla\SSO;

use Attla\DataToken\Facade as DataToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

class Resolver
{
    /**
     * Store the config instance
     *
     * @var \Illuminate\Config\Repository
     */
    protected static $config;

    /**
     * Transform endpoint to SSO server url
     *
     * @param string $endpoint
     * @return string
     */
    public static function link(string $endpoint = '', $redirect = ''): string
    {
        $server = trim(static::getConfig('sso.server', 'http://127.0.0.1'), '/') . '/';

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
        $redirect = (string) static::getConfig('sso.redirect', '/');
        return Route::has($route = trim(trim($redirect), '/.-'))
            ? route($route)
            : $redirect;
    }

    /**
     * Get user from sso callback
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\Auth\Authenticatable|false
     */
    public static function getUser(Request $request)
    {
        if (
            static::getConfig('auth.guards.' . static::getConfig(
                'auth.defaults.guard',
                $guard = 'authentic'
            )) != $guard
        ) {
            throw new \InvalidArgumentException(
                'Authentication guard is not accepted. '
                . 'The SSO client only accepts "authentic" auth guard.'
            );
        }

        if (is_object($data = DataToken::decode($request->token))) {
            return Auth::createModel($data);
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
            static::getConfig('sso.route.logout', 'logout'),
            $request->redirect ?: $request->r ?: ''
        ));
    }

    /**
     * Retrieve config value
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    protected static function getConfig($key = null, $default = null)
    {
        if (is_null(static::$config)) {
            static::$config = config();
        }

        return static::$config->get($key, $default);
    }
}

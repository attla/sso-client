<?php

namespace Attla\SSO;

use Attla\DataToken\Facade as DataToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{
    Auth,
    Route
};

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
     * @param string $redirect
     * @return string
     */
    public static function link(string $endpoint = '', $redirect = ''): string
    {
        $server = trim(static::getConfig('sso.server', 'http://127.0.0.1'), '/') . '/';

        return $server . trim(trim($endpoint), '/')
            . '?client=' . urlencode($_SERVER['HTTP_HOST'] ?? '')
            . '&r=' . urlencode(static::redirect($redirect));
    }

    /**
     * Get SSO end redirect uri
     *
     * @param string $redirect
     * @return string
     */
    public static function redirect($redirect = null)
    {
        $redirect = trim(trim((string) $redirect), '/');
        !$redirect && $redirect = (string) static::getConfig('sso.redirect', '/');

        return Route::has($route = trim(trim($redirect), '/.-'))
            ? route($route)
            : $redirect;
    }

    /**
     * Detect redirect from request
     *
     * @param \Illuminate\Http\Request $request
     * @param string $default
     * @return string
     */
    public static function getRedirectFromRequest(Request $request, $default = null)
    {
        return $request->redirect
            ?: $request->r
            ?: $request->redirect_to
            ?: $request->redirect_uri
            ?: $request->redirect_url
            ?: $request->uri
            ?: $request->url
            ?: static::redirect($default);
    }

    /**
     * Detect state from request
     *
     * @param \Illuminate\Http\Request $request
     * @return string|null
     */
    public static function getStateFromRequest(Request $request)
    {
        return $request->state
            ?: $request->token;
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
            ) . '.driver') != $guard
        ) {
            throw new \InvalidArgumentException(
                'Authentication guard is not accepted. '
                . 'The SSO client only accepts "authentic" auth guard.'
            );
        }

        if (is_object($data = DataToken::decode(static::getStateFromRequest($request)))) {
            return Auth::createModel($data);
        }

        return false;
    }

    /**
     * Make a sso logout
     *
     * @return \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public static function logout(Request $request)
    {
        return redirect(static::link(
            static::getConfig('sso.route.logout', 'logout'),
            static::getRedirectFromRequest($request)
        ));
    }

    /**
     * Retrieve config value
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function getConfig($key = null, $default = null)
    {
        if (is_null(static::$config)) {
            static::$config = config();
        }

        return static::$config->get($key, $default);
    }
}

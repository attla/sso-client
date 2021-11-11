<?php

namespace Attla\SSO\Middlewares;

use Illuminate\Support\Str;
use Illuminate\Http\Request;

class Authorized
{
    /**
     * Handle an incoming request
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle(Request $request, \Closure $next)
    {
        $hasPermission = false;
        $user = $request->user();
        $currentAction = $this->actionToPermissionIdentifier($request->route()->getActionName());

        foreach ($user->roles()->get() as $role) {
            if ($hasPermission = $role->hasPermission($currentAction)) {
                break;
            }
        }

        if (!$hasPermission) {
            if ($request->headers->get('referer')) {
                return back();
            }

            return abort('404', 'Page not found');
        }

        return $next($request);
    }

    protected function actionToPermissionIdentifier($action)
    {
        list($controller, $method) = explode('@', $action);
        $controller = str_replace(['App\\Http\\Controllers\\', 'Controller'], '', $controller);
        $explodedControler = explode('\\', $controller);
        $controller = Str::snake(end($explodedControler), '-');
        $namespace = '';
        if (count($explodedControler) > 1) {
            $namespace = join('.', array_slice($explodedControler, 0, -1)) . '.';
        }

        return $namespace . $controller . '.' . Str::snake($method, '-');
    }
}

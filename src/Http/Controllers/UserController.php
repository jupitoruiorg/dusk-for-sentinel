<?php

namespace Rymanalu\DuskForSentinel\Http\Controllers;

use Cartalyst\Sentinel\Sentinel;
use Cartalyst\Sentinel\Users\EloquentUser;
use Illuminate\Contracts\Config\Repository as Config;

class UserController
{
    /**
     * Retrieve the authenticated user identifier and class name.
     *
     * @param  \Cartalyst\Sentinel\Sentinel  $auth
     * @return array
     */
    public function user(Sentinel $auth)
    {
        if (! $user = $auth->check()) {
            return [];
        }

        return [
            'id' => $user->getUserId(),
            'className' => get_class($user),
        ];
    }

    /**
     * Login using the given user ID.
     *
     * @param  \Cartalyst\Sentinel\Sentinel  $auth
     * @param  \Illuminate\Contracts\Config\Repository  $config
     * @param  int  $userId
     * @return void
     */
    public function login(Sentinel $auth, Config $config, $userId)
    {
        $model = $config->get('cartalyst.sentinel.users.model', EloquentUser::class);

        if (str_contains($userId, '@')) {
            $user = (new $model)->where('email', $userId)->first();
        } else {
            $user = (new $model)->find($userId);
        }

        if ($user) {
            $auth->login($user);

            if (env('CI')) {
                dd('Found', $user);
            }

        } else {
            if (env('CI')) {
                dd('Not found', $userId, (new $model), (new $model)->all()->pluck('email')->toArray());
            }
        }




    }

    /**
     * Log the user out of the application.
     *
     * @param  \Cartalyst\Sentinel\Sentinel  $auth
     * @return void
     */
    public function logout(Sentinel $auth)
    {
        $auth->logout();
    }
}

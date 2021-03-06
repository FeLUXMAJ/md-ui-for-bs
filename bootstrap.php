<?php

use App\Services\Hook;
use Illuminate\Http\Request;
use Illuminate\Contracts\Events\Dispatcher;

return function (Request $request, Dispatcher $events) {
    // Fallback to AdminLTE if uses IE
    $user_agent = $request->header('user-agent');
    if (str_contains($user_agent, 'MSIE') || str_contains($user_agent, 'Trident')) {
        return;
    }

    // If skin server was installed in sub directory, plugin will exit.
    if (!preg_match('/^https?:\/\/(?:\w+)(?:\.\w+)*\/?$/i', url('/'))) return;

    $ns = 'GPlane\MD';

    app()->make('Illuminate\Contracts\Http\Kernel')
        ->pushMiddleware($ns.'\RedirectToMD');

    $events->listen(App\Events\RenderingFooter::class, function ($event) {
        $event->addContent(
            '<link rel="prefetch" href="'.
                plugin_assets('md-ui', 'assets/dist/js/app.js').
            '" />'.
            '<link rel="prefetch" href="'.
                plugin_assets('md-ui', 'assets/dist/js/manifest.js').
            '" />'.
            '<link rel="prefetch" href="'.
                plugin_assets('md-ui', 'assets/dist/js/vendor.js').
            '" />'
        );
    });

    Hook::addRoute(function ($router) use ($ns) {
        $router->group([
            'prefix' => 'md',
            'middleware' => ['web', 'auth'],
            'namespace' => $ns
        ], function ($route) {
            $route->any('info/basic', 'InfoController@basicInfo');
            $route->any('info/user-index', 'InfoController@userIndexPage');
            $route->any('info/player', 'InfoController@playerInfo');
            $route->any('info/update', 'InfoController@getUpdateInfo');

            $route->group([
                'middleware' => ['web', 'auth', 'admin']
            ], function ($route) {
                $route->any('info/admin-panel', 'InfoController@adminPanel');

                $route->get('options/score', 'OptionController@getScoreOptions');
                $route->post('options/score', 'OptionController@setScoreOptions');
                $route->get('options/customize', 'OptionController@getCustomizeOptions');
                $route->post('options/customize', 'OptionController@setCustomizeOptions');
                $route->get('options/site', 'OptionController@getSiteOptions');
                $route->post('options/site', 'OptionController@setSiteOptions');
                $route->get('options/update', 'OptionController@getUpdateOptions');
                $route->post('options/update', 'OptionController@setUpdateOptions');
            });

            // For plugins
            $route->any('info/user-report', 'InfoController@userReport');

            $route->any('avatar/{tid}', 'MiscellaneousController@getAvatar');
            $route->any('player/{name}', 'MiscellaneousController@getPlayerId');
        });

        // Routers Hack
        $router->any('user', $ns.'\ViewController@userIndex')
            ->middleware(['web', 'auth']);
    });
};

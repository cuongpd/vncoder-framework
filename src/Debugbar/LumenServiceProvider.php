<?php

namespace VnCoder\Debugbar;

use VnCoder\Debugbar\Middleware\InjectDebugbar;
use DebugBar\DataFormatter\DataFormatter;
use DebugBar\DataFormatter\DataFormatterInterface;
use Illuminate\Container\Container;
use Illuminate\Contracts\View\Factory;
use Illuminate\Session\SessionManager;
use Illuminate\Support\Collection;
use Laravel\Lumen\Application;
use Illuminate\Support\ServiceProvider;

class LumenServiceProvider extends ServiceProvider
{
    /** @var  Application */
    protected $app;

    public function register()
    {
        $this->app->alias(
            DataFormatter::class,
            DataFormatterInterface::class
        );

        $this->app->singleton(LaravelDebugbar::class, function ($app) {
            $debugbar = new LaravelDebugbar($app);

            if ($app->bound(SessionManager::class)) {
                $sessionManager = $app->make(SessionManager::class);
                $httpDriver = new SymfonyHttpDriver($sessionManager);
                $debugbar->setHttpDriver($httpDriver);
            }

            return $debugbar;
        });

        $this->app->alias(LaravelDebugbar::class, 'debugbar');

        $this->app->singleton(
            'command.debugbar.clear',
            function ($app) {
                return new Console\ClearCommand($app['debugbar']);
            }
        );

        $this->app->extend(
            'view',
            function (Factory $factory, Container $application): Factory {
                $laravelDebugbar = $application->make(LaravelDebugbar::class);

                $shouldTrackViewTime = $laravelDebugbar->isEnabled() &&
                    $laravelDebugbar->shouldCollect('time', true) &&
                    $laravelDebugbar->shouldCollect('views', true) &&
                    $application['config']->get('debugbar.options.views.timeline', false);

                if (! $shouldTrackViewTime) {
                    return $factory;
                }

                $extensions = array_reverse($factory->getExtensions());
                $engines = array_flip($extensions);
                $enginesResolver = $application->make('view.engine.resolver');

                foreach ($engines as $engine => $extension) {
                    $resolved = $enginesResolver->resolve($engine);

                    $factory->addExtension($extension, $engine, function () use ($resolved, $laravelDebugbar) {
                        return new DebugbarViewEngine($resolved, $laravelDebugbar);
                    });
                }

                foreach ($extensions as $extension => $engine) {
                    $factory->addExtension($extension, $engine);
                }

                return $factory;
            }
        );

        Collection::macro('debug', function () {
            debug($this);
            return $this;
        });
    }

    public function boot()
    {
        $this->registerMiddleware(InjectDebugbar::class);
        $this->commands(['command.debugbar.clear']);
        $this->app->router->group(['namespace' => 'VnCoder\Debugbar\Controllers', 'prefix' => app('config')->get('debugbar.route_prefix')], function ($router) {
            $router->get('open', [
                'uses' => 'OpenHandlerController@handle',
                'as' => 'debugbar.openhandler',
            ]);

            $router->get('clockwork/{id}', [
                'uses' => 'OpenHandlerController@clockwork',
                'as' => 'debugbar.clockwork',
            ]);

            $router->get('assets/stylesheets', [
                'uses' => 'AssetController@css',
                'as' => 'debugbar.assets.css',
            ]);

            $router->get('assets/javascript', [
                'uses' => 'AssetController@js',
                'as' => 'debugbar.assets.js',
            ]);

            $router->delete('cache/{key}/{tags?}', [
                'uses' => 'CacheController@delete',
                'as' => 'debugbar.cache.delete',
            ]);
        });
    }

    protected function registerMiddleware($middleware)
    {
        $this->app->middleware([$middleware]);
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['debugbar', 'command.debugbar.clear'];
    }

}

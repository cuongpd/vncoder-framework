<?php
/** @noinspection PhpUndefinedMethodInspection */
/** @noinspection PhpUndefinedFieldInspection */

namespace VnCoder;

use Illuminate\Support\ServiceProvider;
use VnCoder\Models\VnConfig;

if (!defined('BASE_PATH')) {
    throw new \RuntimeException('BASE_PATH must be defined');
}

defined('LUMENT_START')             ||  define('LUMENT_START', microtime(true));
defined('TIME_NOW')                 ||  define('TIME_NOW', time());
defined('APP_VERSION')              ||  define('APP_VERSION', '1.0');
defined('BASE_URL')                 ||  define('BASE_URL', env('APP_URL'));
defined('APP_PATH')                 ||  define('APP_PATH', BASE_PATH . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR);
defined('PUBLIC_PATH')              ||  define('PUBLIC_PATH', BASE_PATH . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR);
defined('ADMIN_PATH')               ||  define('ADMIN_PATH', APP_PATH . 'Admin' . DIRECTORY_SEPARATOR);
defined('COMMAND_PATH')             ||  define('COMMAND_PATH', ADMIN_PATH . 'Command' . DIRECTORY_SEPARATOR);
defined('CONTROLLER_PATH')          ||  define('CONTROLLER_PATH', APP_PATH . 'Controllers' . DIRECTORY_SEPARATOR);
defined('API_PATH')                 ||  define('API_PATH', CONTROLLER_PATH . 'Api' . DIRECTORY_SEPARATOR);
defined('VNCODER_CORE_PATH')        ||  define('VNCODER_CORE_PATH', BASE_PATH . DIRECTORY_SEPARATOR . 'vendor/vncoder/framework' . DIRECTORY_SEPARATOR);

define('PHP_EXTENSION', '.php');

class BootstrapProvider extends ServiceProvider
{
    public function register()
    {
        $config = $this->app->make('config');
        $config->set('services', require VNCODER_CORE_PATH . 'configs/services.php');
        $config->set('session', require VNCODER_CORE_PATH . 'configs/session.php');

        $this->app->singleton(
            \Illuminate\Contracts\Debug\ExceptionHandler::class,
            Core\Exceptions\HandlerExceptions::class
        );

        $runningInConsole = $this->app->runningInConsole();
        $isRunConsoleRoute = !$runningInConsole && $this->isRunConsoleRoute();

        if ($runningInConsole || $isRunConsoleRoute) {
            $this->registerConsoleCommands();
        }

        if (!$runningInConsole) {
            $this->registerSessionServices();
            $this->registerRouter();

            if (cookie('__debugbar') === 'on') {
                $this->app->make('config')->set('debugbar', require VNCODER_CORE_PATH . 'configs/debugbar.php');
                $this->app->register(Debugbar\LumenServiceProvider::class);
            }
        }
    }

    public function boot()
    {
        if (cookie('__debugbar') !== 'on' && cookie('__minify_output') !== 'off') {
            ob_start('minify_output');
        }
        // Register Views Folders
        $this->loadViewsFrom(VNCODER_CORE_PATH  . 'src/Views', 'core');
        $this->loadViewsFrom(VNCODER_CORE_PATH  . 'src/Backend/Views', 'backend');
        $this->loadViewsFrom(ADMIN_PATH         .  'Views', 'admin');
        $this->loadViewsFrom(APP_PATH           .  'Views', 'frontend');
        $this->loadViewsFrom(APP_PATH           .  'Mailer/Views', 'mailer');
        // Session Start
        $this->app->middleware([
            \Illuminate\Session\Middleware\StartSession::class,
        ]);
    }

    protected function registerRouter(){
        // Middleware
        $this->app->routeMiddleware([
            'admin'     =>    \VnCoder\Core\Middleware\AdminMiddleware::class,
            'api'       =>    \VnCoder\Core\Middleware\ApiMiddleware::class,
            'website'   =>    \VnCoder\Core\Middleware\WebsiteMiddleware::class,
            'auth'      =>    \VnCoder\Core\Middleware\AuthMiddleware::class,
        ]);

        $this->app->router->get('backend/login.html',           [ 'as' => 'backend.login',          'uses' => 'VnCoder\Backend\Auth\AuthController@Login_Action']);
        $this->app->router->get('backend/logout.html',          [ 'as' => 'backend.logout',         'uses' => 'VnCoder\Backend\Auth\AuthController@Logout_Action']);
        $this->app->router->get('backend/reset-password.html',  [ 'as' => 'backend.reset_password', 'uses' => 'VnCoder\Backend\Auth\AuthController@Reset_Password_Action']);
        $this->app->router->post('backend/login.html', 'VnCoder\Backend\Auth\AuthController@Login_Action');

        $this->app->router->group(['as' => 'backend', 'prefix' => 'backend/', 'middleware' => 'admin'], static function ($router) {
            $router->get('/', [ 'as' => 'dashboard', function () {
                return redirect()->to('backend/dashboard');
            }]);
            $router->get('{module:[a-z-]+}/{controller:[a-z-]+}/{action:[a-z0-9-]+}',   'VnCoder\Core\Router\BackendRouterController@Module_Get_Action');
            $router->post('{module:[a-z-]+}/{controller:[a-z-]+}/{action:[a-z0-9-]+}',  'VnCoder\Core\Router\BackendRouterController@Module_Post_Action');
            $router->get('{controller:[a-z-]+}[/{action:[a-z0-9-]+}]',                  'VnCoder\Core\Router\BackendRouterController@Get_Action');
            $router->post('{controller:[a-z-]+}[/{action:[a-z0-9-]+}]',                 'VnCoder\Core\Router\BackendRouterController@Post_Action');
        });

        // API Router
        $this->app->router->group(['as' => 'api', 'prefix' => 'api/', 'middleware' => 'api'], static function ($router) {
            $router->get('/', 'VnCoder\Core\Router\ApiRouterController@Main_Action');
            $router->post('git-update',     'VnCoder\Core\Router\ApiRouterController@Git_Update_Action');
            $router->get('run-console',     'VnCoder\Core\Router\ApiRouterController@Run_Console_Action');
            $router->get('vn-helper.html',  'VnCoder\Core\Router\ApiRouterController@Open_Api_Action');
            $router->get('open-api.json',   'VnCoder\Core\Router\ApiRouterController@Open_Api_Data_Action');
            $router->get('{controller:[a-z][a-z0-9-]*}[/{action:[a-z0-9-]+}]',  'VnCoder\Core\Router\ApiRouterController@runApiControllerAction');
            $router->post('{controller:[a-z][a-z0-9-]*}[/{action:[a-z0-9-]+}]', 'VnCoder\Core\Router\ApiRouterController@runApiControllerAction');
        });

        // Frontend Router
        $this->app->router->get('404.html', function (){
            return view('core::page.page-404');
        });

        $this->app->router->get('maintenance.html', function (){
            $maintenanceData = VnConfig::getMaintenanceData();
            return view('core::page.maintenance', $maintenanceData);
        });

        $this->app->router->group(['namespace' => 'App\Controllers', 'middleware' => 'website'], static function ($router) {
            $router->get('/', [ 'as' => 'home', 'uses' => 'HomeController@Index_Action']);
            if (file_exists(BASE_PATH . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'route.php')) {
                require BASE_PATH . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'route.php';
            }
            $router->get('{controller:[a-z][a-z0-9]*}[/{action:[a-z0-9-]+}]', '\\VnCoder\\Core\\Router\\FrontendRouterController@Get_Action');
            $router->post('{controller:[a-z][a-z0-9]*}[/{action:[a-z0-9-]+}]', '\\VnCoder\\Core\\Router\\FrontendRouterController@Post_Action');
        });
    }


    protected function isRunConsoleRoute(): bool
    {
        if (!isset($_SERVER['REQUEST_URI'])) {
            return false;
        }
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        return $uri === '/api/run-console';
    }

    protected function registerConsoleCommands(): void
    {
        $this->commands([
            Core\Console\QueueCheckup::class,
            Core\Console\VnCoderCommand::class,
            Core\Console\VnCoderComposer::class,
            Core\Console\GitCommand::class,
        ]);

        $this->app->singleton(\Illuminate\Contracts\Console\Kernel::class, Core\Console\QueueSchedule::class);
    }

    protected function registerSessionServices(): void
    {
        $this->app->singleton(\Illuminate\Session\SessionManager::class, function () {
            return $this->app->loadComponent('session', \Illuminate\Session\SessionServiceProvider::class,  'session');
        });

        $this->app->singleton('session.store', function () {
            return $this->app->loadComponent( 'session', \Illuminate\Session\SessionServiceProvider::class, 'session.store');
        });
    }


}

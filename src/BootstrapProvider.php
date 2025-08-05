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
defined('BASE_URL')                 ||  define('BASE_URL', env('APP_URL'));
defined('APP_PATH')                 ||  define('APP_PATH', BASE_PATH . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR);
defined('STORAGE_PATH')             ||  define('STORAGE_PATH', BASE_PATH . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR);
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
        $this->app->make('config')->set('services', require VNCODER_CORE_PATH . 'configs' . DIRECTORY_SEPARATOR . 'services.php');
        $this->app->make('config')->set('session', require VNCODER_CORE_PATH . 'configs'. DIRECTORY_SEPARATOR .'session.php');

        $this->app->singleton(
            \Illuminate\Contracts\Debug\ExceptionHandler::class,
            Core\Exceptions\HandlerExceptions::class
        );

        if ($this->app->runningInConsole()) {
            $this->registerConsoleCommands();
        }else{
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


        // API Router
        $this->app->router->group(['as' => 'api', 'prefix' => 'api/', 'middleware' => 'api'], static function ($router) {
            $router->get('/', 'VnCoder\Core\Router\ApiRouterController@Main_Action');
            $router->post('git-update',     'VnCoder\Core\Router\ApiRouterController@Git_Update_Action');
            $router->get('vn-helper.html',  'VnCoder\Core\Router\ApiRouterController@Open_Api_Action');
            $router->get('open-api.json',   'VnCoder\Core\Router\ApiRouterController@Open_Api_Data_Action');
            $router->get('{controller:[a-z][a-z0-9-]*}[/{action:[a-z0-9-]+}]',  'VnCoder\Core\Router\ApiRouterController@runApiControllerAction');
            $router->post('{controller:[a-z][a-z0-9-]*}[/{action:[a-z0-9-]+}]', 'VnCoder\Core\Router\ApiRouterController@runApiControllerAction');
        });


        // Backend Router
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

        // Auth Router
        $this->app->router->get('auth/login.html',           [ 'as' => 'auth.login',          'uses' => 'VnCoder\Core\Router\AuthController@Login_Action']);
        $this->app->router->get('auth/register.html',        [ 'as' => 'auth.register',          'uses' => 'VnCoder\Core\Router\AuthController@Register_Action']);
        $this->app->router->get('auth/logout.html',          [ 'as' => 'auth.logout',         'uses' => 'VnCoder\Core\Router\AuthController@Logout_Action']);
        $this->app->router->get('auth/reset-password.html',  [ 'as' => 'auth.reset-password', 'uses' => 'VnCoder\Core\Router\AuthController@Reset_Password_Action']);
        $this->app->router->post('auth/login.html',     'VnCoder\Core\Router\AuthController@Do_Login_Action');
        $this->app->router->post('auth/register.html',  'VnCoder\Core\Router\AuthController@Do_Register_Action');
        $this->app->router->get('auth/modal/{action:[a-z-]+}', 'VnCoder\Core\Router\AuthController@Modal_Action');

        $this->app->router->get('auth/provider/{provider:[a-z0-9-]+}', [ 'as' => 'auth.provider', 'uses' => 'VnCoder\Core\Router\AuthController@Provider_Action']);
        $this->app->router->get('auth/provider/{provider:[a-z0-9-]+}/callback', [ 'as' => 'auth.provider-callback', 'uses' => 'VnCoder\Core\Router\AuthController@Provider_Callback_Action']);

        $this->app->router->group(['namespace' => 'App\Controllers', 'middleware' => 'website'], static function ($router) {
            $router->get('/', [ 'as' => 'home', 'uses' => 'HomeController@Index_Action']);
            if (file_exists(BASE_PATH . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'route.php')) {
                require BASE_PATH . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'route.php';
            }
            $router->get('{controller:[a-z][a-z0-9]*}[/{action:[a-z0-9-]+}]', '\\VnCoder\\Core\\Router\\FrontendRouterController@Get_Action');
            $router->post('{controller:[a-z][a-z0-9]*}[/{action:[a-z0-9-]+}]', '\\VnCoder\\Core\\Router\\FrontendRouterController@Post_Action');
        });

        // Frontend Router
        $this->app->router->get('404.html', function (){
            return view('core::page.page-404');
        });

        $this->app->router->get('maintenance.html', function (){
            $maintenanceData = VnConfig::getMaintenanceData();
            if($maintenanceData['status'] == 0){
                return redirect()->to('/');
            }
            return view('core::page.maintenance', $maintenanceData);
        });

    }

    protected function registerConsoleCommands(): void
    {
        $this->commands([
            Core\Console\QueueCheckup::class,
            Core\Console\QueueConsoleCommand::class,
            Core\Console\RunGitCommand::class,
            Core\Console\VnCoderCommand::class,
            Core\Console\VnCoderComposer::class,
        ]);

        $this->app->singleton(\Illuminate\Contracts\Console\Kernel::class, Core\Console\VnCoderSchedule::class);
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

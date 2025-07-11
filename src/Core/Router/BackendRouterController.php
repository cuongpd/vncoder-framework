<?php

namespace VnCoder\Core\Router;

use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller;

class BackendRouterController extends Controller
{
    public function Get_Action(Request $request, $controller, $action = 'index')
    {
        return $this->runAdminControllerAction($request, $controller, $action, false);
    }

    public function Post_Action(Request $request, $controller, $action = 'index')
    {
        return $this->runAdminControllerAction($request, $controller, $action,true);
    }

    public function Module_Get_Action(Request $request, $module, $controller, $action = 'index')
    {
        return $this->runAdminControllerAction($request, $controller, $action, false, $module);
    }

    public function Module_Post_Action(Request $request, $module, $controller, $action = 'index')
    {
        return $this->runAdminControllerAction($request, $controller, $action, true, $module);
    }

    public function runAdminControllerAction(Request $request, $controller, $action, $isPostMethod = false, $module = '')
    {
        $isBackendCore = false;
        if($module){
            $moduleNamespace = str_replace(' ', '',  ucwords(trim(str_replace('-', ' ', $module))));
            $controllerName = "\\App\Admin\\Modules\\". $moduleNamespace . "\\" . getNameByController($controller);
        }else{
            $moduleFile = ADMIN_PATH . 'Modules' . DIRECTORY_SEPARATOR . ucfirst($controller) . DIRECTORY_SEPARATOR  . getNameByController($action) . '.php';
            if(file_exists($moduleFile)) {
                $controllerName = "\\App\Admin\\Modules\\" . ucfirst($controller) . "\\" . getNameByController($action);
                $module = $controller;
                $controller = $action;
                $action = 'index';
            }else{
                if(strncmp($controller, 'core-', 5) === 0){
                    $isBackendCore = true;
                    $controllerName = "\\VnCoder\Backend\Controllers\\" . getNameByController(substr($controller, 5));
                }else{
                    $controllerName = "\\App\Admin\\Controllers\\" . getNameByController($controller);
                }
            }
        }

        if (class_exists($controllerName)) {
            $request->server->set('__ADMIN__MODULE__NAME', $module);
            $request->server->set('__ADMIN__CONTROLLER__NAME', $controller);
            $request->server->set('__ADMIN__ACTION__NAME', $action);
            $request->server->set('__ADMIN__CORE__ROUTER', $isBackendCore);

            $runController = app()->make($controllerName);
            $actionName = getNameByAction($action, $isPostMethod);
            if (method_exists($runController, $actionName)) {
                return $runController->$actionName($request);
            }

            return response()->json([
                'status'    => -1 ,
                'message'   => 'Method ' . $actionName . ' not active in class ' . $controllerName,
            ], 502, [], JSON_PRETTY_PRINT);
        }

        return response()->json([
            'status'    => -1 ,
            'message'   => 'Class ' . $controllerName . ' not found',
        ], 404, [], JSON_PRETTY_PRINT);
    }
}
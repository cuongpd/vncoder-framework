<?php

namespace VnCoder\Core\Router;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller;
class FrontendRouterController extends Controller
{
    public function Get_Action(Request $request, $controller, $action = 'index')
    {
        return $this->runAdminControllerAction($request, $controller, $action, false);
    }

    public function Post_Action(Request $request, $controller, $action = 'index')
    {
        return $this->runAdminControllerAction($request, $controller, $action,true);
    }

    public function runAdminControllerAction(Request $request, $controller, $action, $isPostMethod = false)
    {
        // URL : /{controller}
        // URL : /{controller}/{action}
        $controllerName = "\\App\\Controllers\\" . getNameByController($controller);
        if (class_exists($controllerName)) {
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
<?php

namespace VnCoder\Core\Router;

use Illuminate\Http\Request;
use OpenApi\Generator;
use VnCoder\Helper\DatabaseHelper;
use Laravel\Lumen\Routing\Controller;

class ApiRouterController extends Controller
{
    public function Main_Action(){
        return $this->print('Nothing to do', 403);
    }
    public function runApiControllerAction(Request $request, $controller, $action = 'index')
    {
        $controllerName = "\\App\\Controllers\\Api\\".getNameByController($controller);
        if (class_exists($controllerName)) {
            $runApiController = app()->make($controllerName);
            $actionName = getNameByAction($action, false);
            if (method_exists($runApiController, $actionName)) {
                return $runApiController->$actionName($request);
            }
            return response()->json([
                'status'    => -1 ,
                'message'     => 'Function ' . $actionName . ' does not exist in class ' . $controllerName,

            ], 503, [], JSON_PRETTY_PRINT);
        }

        return response()->json([
            'status'    => -1 ,
            'message'     => 'Class ' . $controllerName .' does not exist',
        ], 404, [], JSON_PRETTY_PRINT);

    }

    // Swagger API
    public function Open_Api_Action()
    {
        return view('core::api.api-helper');
    }

    public function Open_Api_Data_Action()
    {
        $openapi = Generator::scan([API_PATH]);
        header('Content-Type: application/json; charset=utf-8');
        return $openapi->toJson();
    }

    public function Git_Update_Action(Request $request){
        $gitMessage = $this->gitUpdate();
        $dbHelper = new DatabaseHelper();
        $dbHelper->updateDatabase();
        logData('git-update', $gitMessage);
        setAppVersion();
        return response()->json([
            'status'    => 1 ,
            'gitMessage'     => $gitMessage,
            'databaseMessage'     => $dbHelper->getMessage(),
        ], 200, [], JSON_PRETTY_PRINT);
    }

    protected function gitUpdate(){
        $gitUserUpdate = env('GIT_UPDATE_USER', '');
        $gitBranch = env('GIT_BRANCH', 'main');
        $returnVar = null; $output = [];
        /*
         * Để chạy được git tự động cần khai báo trên máy chủ:
         * Chạy lệnh 'sudo visudo' và thêm dòng sau vào cuối file:
         * www-data ALL=($gitUserUpdate) NOPASSWD: /usr/bin/git
         */
        // $addSafeDirCommand = 'sudo -u '.$gitUserUpdate.' git config --global --add safe.directory ' . BASE_PATH;
        // exec($addSafeDirCommand, $output, $returnVar);
        $gitCommand = $gitUserUpdate ? 'sudo -u ' . $gitUserUpdate . ' git pull origin ' . $gitBranch : 'git pull origin ' . $gitBranch;
        exec($gitCommand . ' 2>&1', $output, $returnVar);
        return $output;
    }

    protected function print($content, $status = 200){
        return response($content, $status)->header('Content-Type', 'text/plain');
    }

}
<?php

namespace VnCoder\Backend\Models;

use VnCoder\Models\VnModel;

class VnUserRole extends VnModel
{
    protected $table = '__users_role';
    protected $fillable = ['name', 'permissions', 'description'];

    public static function getData(){
        return self::where('status', '>', 0)->get();
    }

    protected function formConfig()
    {
        return [
            'name' => ['label' => 'Tên nhóm quyền', 'col' => 4, 'required' => true],
            'description' => ['label' => 'Mô tả', 'col' => 8, 'type' => 'text', 'required' => false],
        ];
    }

    public static function getPermissionData($roleId){
        $roleData = self::find($roleId);
        if($roleData){
            return json_decode($roleData->permissions, true);
        }
        return [];
    }

    public static function getAllPermission(){
        $permissionData = [];

        $backendControllerDir = CORE_PATH_SRC . 'Backend/Controllers/';
        $backendControllerFile = glob($backendControllerDir . '*Controller.php');
        foreach ($backendControllerFile as $item){
            $name = str_replace($backendControllerDir, '', $item);
            $name = strtolower( str_replace('Controller.php', '', $name));
            if(in_array($name, ['backend', 'ajax', 'crud'])){
                continue;
            }
            $name = 'core-' . $name;
            $permissionData[$name] = self::getMethodInController($item);
        }

        $adminControllerDir = ADMIN_PATH . 'Controllers/';
        $adminControllerFile = glob($adminControllerDir . '*Controller.php');
        foreach ($adminControllerFile as $item){
            $name = str_replace($adminControllerDir, '', $item);
            $name = strtolower( str_replace('Controller.php', '', $name));
            if(in_array($name, ['admin', 'ajax', 'crud'])){
                continue;
            }
            $name = 'admin-' . $name;
            $permissionData[$name] = self::getMethodInController($item);
        }
        return $permissionData;
    }

    public static function getMethodInController($controllerFile){
        $permission = [];
        $classContent = file_get_contents($controllerFile);
        $extended = '';
        preg_match('/extends\s+(\w+)/', $classContent, $matches);
        if (!empty($matches[1])) {
            $extended = $matches[1];
        }
        if($extended == 'CrudController'){
            $permission[] = 'List_Action';
            $permission[] = 'Edit_Action';
            $permission[] = 'Edit_Action_Submit';
            $permission[] = 'Delete_Action';
        }

        // Tìm và liệt kê tất cả các phương thức công cộng có hậu tố "_Action"
        preg_match_all('/public\s+function\s+(\w+)_Action\s*\(/', $classContent, $methods);
        $public_methods = $methods[1];
        if (!empty($public_methods)) {
            foreach ($public_methods as $method) {
                $permission[] = $method . '_Action';
            }
        }
        return $permission;
    }

}

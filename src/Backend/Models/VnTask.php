<?php

namespace VnCoder\Backend\Models;

use VnCoder\Backend\Models\VnAdmin;
use VnCoder\Models\VnModelBase;

class VnTask extends VnModelBase
{
    protected $table = "__tasks";
    protected $fillable = ['user_id', 'type', 'name', 'priority', 'created', 'updated', 'status'];

    public static function categoryData(){
        return [
            '1' => 'Công việc',
            '2' => 'Sự kiện',
            '3' => 'Học tập',
            '4' => 'Khác',
        ];
    }

    static function getTaskList($action){
        switch ($action){
            case 'todo':
                return self::where('status', 1)->orderBy('created', 'DESC')->get();
            case 'doing':
                return self::where('status', 2)->orderBy('created', 'DESC')->get();
            case 'done':
                return self::where('status', 10)->orderBy('created', 'DESC')->get();
            case 'delete':
                return self::where('status', -1)->orderBy('created', 'DESC')->get();

        }

        return [];
    }


    static function makeTask($category, $task){
        self::create([
            'user_id' => VnAdmin::getUserID(),
            'type' => $category,
            'name' => $task,
            'priority' => 1,
            'created' => time(),
            'status' => 1,
        ]);
        flash_message('Task đã được tạo thành công!', 'success');
    }
}

<?php

namespace VnCoder\Backend\Controllers;

use VnCoder\Backend\Models\Admin;
use Laravel\Lumen\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class AjaxController extends Controller
{
    public function Debugbar_Action_Submit(Request $request){
        $debugbar = $request->cookie('__debugbar', 'off');
        if ($debugbar == 'on') {
            cookie('__debugbar', 'off', 0);
            flash_message('Chế độ Debug đã tắt!', 'success');
        } else {
            cookie('__debugbar', 'on', 30 * 86400); // 30 days
            cookie('__minify_output', 'off', 30 * 86400); // 30 days
            flash_message('Chế độ Debug đã được bật!', 'success');
        }
        return response()->json(['status' => 1]);
    }

    public function Reset_Cache_Action_Submit(){
        $status = Cache::flush() ? 1 : -1;
        $message = $status == 1 ? 'Cache đã được xóa!' : 'Không thể xóa cache!';
        flash_message($message, $status == 1 ? 'success' : 'error');
        return response()->json(['status' => $status]);
    }

    public function Dark_Mode_Action_Submit(Request $request){
        cookie('__data_pc_theme', $request->input('data_pc_theme'), 30 * 86400);
        return response()->json(['status' => 1]);
     }

    public function Ping_Action_Submit(){
        Admin::setUserCookie(session('admin_uid'));
        return response()->json(['status' => 1]);
    }

    public function Update_Action_Submit(Request $request){
        $action = $request->input('action');
        $id = $request->input('id');
        $value = $request->input('value');
        flash_message('Cập nhật thành công!' . $value, 'success');
        return response()->json(['status' => $value]);
    }
}

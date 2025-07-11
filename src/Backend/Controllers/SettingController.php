<?php

namespace VnCoder\Backend\Controllers;

use VnCoder\Backend\Controllers\BackendController;
use VnCoder\Models\VnConfig;
use Illuminate\Http\Request;

class SettingController extends BackendController
{

    public function Index_Action(){
        return redirect()->to($this->linkAction('config'));
    }

    public function Config_Action()
    {
        $this->metaData->title = "Cấu hình Website";
        $this->usingFormEditor = true;
        $this->setData['settingForm'] = VnConfig::getSettingConfigForm();
        return $this->views('setting.form');
    }

    public function Config_Action_Submit(Request $request)
    {
        VnConfig::saveSettingConfig($request);
        return $this->redirectUrl('config');
    }

    public function Data_Action()
    {
        $this->metaData->title = "Thay đổi dữ liệu Website";
        $this->usingFormEditor = true;
        $this->setData['settingForm'] = VnConfig::getDataConfigForm();
        return $this->views('setting.form-config');
    }

    public function Data_Action_Submit(Request $request)
    {
        VnConfig::saveDataConfig($request);
        return $this->redirectUrl('data');
    }

    public function Website_Action()
    {
        $this->metaData->title = 'Tùy chỉnh HTML';
        $this->usingFormEditor = true;
        $this->setData['settingForm'] = VnConfig::getExtraConfigForm();
        $this->initCodeMirror();
        return $this->views('setting.form-extra');
    }

    public function Website_Action_Submit(Request $request)
    {
        VnConfig::saveExtraConfig($request);
        return $this->redirectUrl('website');
    }

    public function Maintenance_Mode_Action(){
        $this->metaData->title = 'Chế độ bảo trì website';
        $this->usingFormEditor = true;
        $this->setData['settingForm'] = VnConfig::getMaintenanceConfigForm();
        return $this->views('setting.form-maintenance');
    }

    public function Maintenance_Mode_Action_Submit(Request $request){
        VnConfig::saveMaintenanceConfig($request);
        return $this->redirectUrl('maintenance-mode');
    }

    public function Options_Action(){
        $this->metaData->title = 'Tùy chỉnh hệ thống Website';
        $this->usingFormEditor = true;
        $this->setData['settingForm'] = VnConfig::getOptionsConfigForm();
        return $this->views('setting.form');
    }

    public function Options_Action_Submit(Request $request){
        VnConfig::saveOptionsConfigForm($request);
        return $this->redirectUrl('options');
    }


}

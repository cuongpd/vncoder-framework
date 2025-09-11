<?php

namespace VnCoder\Backend\Controllers;

use VnCoder\Backend\Controllers\BackendController;
use VnCoder\Backend\Models\AuthConfig;
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
        $this->setData['settingForm'] = VnConfig::getConfigDataFormData();
        return $this->views('setting.form');
    }

    public function Config_Action_Submit(Request $request)
    {
        VnConfig::saveConfigData($request);
        return $this->redirectUrl('config');
    }

    public function Website_Action()
    {
        $this->metaData->title = 'Tùy chỉnh HTML';
        $this->usingFormEditor = true;
        $this->setData['settingForm'] = VnConfig::getWebsiteConfigFormData();
        $this->initCodeMirror();
        return $this->views('setting.form-extra');
    }

    public function Website_Action_Submit(Request $request)
    {
        VnConfig::saveWebsiteConfig($request);
        return $this->redirectUrl('website');
    }

    public function Maintenance_Mode_Action(){
        $this->metaData->title = 'Chế độ bảo trì website';
        $this->usingFormEditor = true;
        $this->setData['settingForm'] = VnConfig::getMaintenanceConfigFormData();
        return $this->views('setting.form-maintenance');
    }

    public function Maintenance_Mode_Action_Submit(Request $request){
        VnConfig::saveMaintenanceConfig($request);
        return $this->redirectUrl('maintenance-mode');
    }

    public function Data_Action()
    {
        $this->metaData->title = "Thay đổi dữ liệu Website";
        $this->usingFormEditor = true;
        $this->setData['settingForm'] = VnConfig::getDataConfigFormData();
        return $this->views('setting.form-config');
    }

    public function Data_Action_Submit(Request $request)
    {
        VnConfig::saveDataConfig($request);
        return $this->redirectUrl('data');
    }


    public function Login_Config_Action(){
        $this->metaData->title = "Cấu hình đăng nhập";
        $this->usingFormEditor = true;
        $this->setData['settingForm'] = VnConfig::authConfigData();
        $this->setData['authProviders'] = VnConfig::AUTH_PROVIDERS;
        return $this->views('setting.login-config');
    }

    public function Login_Config_Action_Submit(Request $request){
        VnConfig::saveAuthConfig($request);
        return $this->redirectUrl('login-config');
    }





}

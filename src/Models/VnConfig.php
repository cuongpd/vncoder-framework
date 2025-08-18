<?php

namespace VnCoder\Models;

use Illuminate\Http\Request;

class VnConfig extends VnModelBase
{
    public $timestamps = false;
    protected $table = '__configs';
    protected $fillable = ['type', 'name', 'data', 'description'];

    // Type : 'setting',  'website', 'config', 'core', 'database', 'console'

    public const SETTING_KEY = ['name', 'title', 'favicon', 'description', 'keywords', 'logo', 'photo', 'email', 'phone', 'author', 'author_url', 'address', 'copyright', 'about_us', 'facebook', 'twitter', 'youtube', 'instagram', 'gdpr_status', 'gdpr_message', 'privacy_policy'];

    public static function getConfigData($update = false){
        $cache_key = 'vn_site_config';
        $data = cache($cache_key);
        if (!$data || $update) {
            $configData = self::where('type', 'setting')->pluck('data', 'name')->toArray();
            $data = newObject();
            foreach (self::SETTING_KEY as $item){
                if (isset($configData[$item])) {
                    $data->$item = $configData[$item];
                } else {
                    $data->$item = '';
                }
            }
            cache($cache_key, $data, 86400);
        }
        return $data;
    }

    public static function saveConfigData(Request $request){
        $data = $request->except('__token');
        foreach ($data as $key => $value) {
            if (!in_array($key, self::SETTING_KEY)) {
                continue;
            }
            if (in_array($key, ['logo', 'photo', 'favicon']) && $request->hasFile($key)) {
                $uploadData = VnUploads::photo($request->file($key), 'setting-' . $key);
                if (!$uploadData['success']) {
                    flash_message($uploadData['message'], 'danger');
                    continue;
                }
                $value = $uploadData['path'];
            }
            self::updateOrCreate(
                ['type' => 'setting', 'name' => $key],
                ['data' => $value]
            );
        }
        self::getConfigData(true);
        flash_message('Setting has been updated');
    }

    public static function getConfigDataFormData(){
        $formData = [];
        $settingData = self::where('type', 'setting')->pluck('data', 'name')->toArray();

        $formData['name'] = ['label' => 'Tên website', 'col' => 2, 'type' => 'text', 'value' => $settingData['name'] ?? '', 'required' => true, 'maxlength' => 30];
        $formData['title'] = ['label' => 'Tiêu đề trang chủ', 'col' => 4, 'type' => 'text', 'value' => $settingData['title'] ?? '', 'required' => true, 'maxlength' => 80];
        $formData['email'] = ['label' => 'Email', 'col' => 2, 'type' => 'email', 'value' => $settingData['email'] ?? '', 'required' => true];
        $formData['phone'] = ['label' => 'Phone', 'col' => 2, 'type' => 'text', 'value' => $settingData['phone'] ?? '', 'required' => true];
        $formData['copyright'] = ['label' => 'Copyright', 'col' => 2, 'type' => 'text', 'value' => $settingData['copyright'] ?? '', 'required' => true];

        $formData['logo'] = ['label' => 'Logo', 'col' => 4, 'type' => 'photo', 'value' => $settingData['logo'] ?? ''];
        $formData['photo'] = ['label' => 'Ảnh đại diện', 'col' => 4, 'type' => 'photo', 'value' => $settingData['photo'] ?? ''];
        $formData['favicon'] = ['label' => 'Favicon', 'col' => 4, 'type' => 'photo', 'value' => $settingData['favicon'] ?? ''];

        $formData['description'] = ['label' => 'Description', 'col' => 4, 'type' => 'textarea', 'rows' => 2, 'value' => $settingData['description'] ?? '', 'required' => true];
        $formData['keywords'] = ['label' => 'Keywords', 'col' => 4, 'type' => 'textarea', 'rows' => 2, 'value' => $settingData['keywords'] ?? '', 'required' => true];
        $formData['address'] = ['label' => 'Address', 'col' => 4, 'type' => 'textarea', 'rows' => 2, 'value' => $settingData['address'] ?? '', 'required' => true];
        $formData['about_us'] = ['label' => 'About Us', 'col' => 12, 'rows' => 10, 'type' => 'editor', 'value' => $settingData['about_us'] ?? '', 'required' => true];

        $formData['author'] = ['label' => 'Author', 'col' => 2, 'type' => 'text', 'value' => $settingData['author'] ?? ''];
        $formData['author_url'] = ['label' => 'Author URL', 'col' => 2, 'type' => 'text', 'value' => $settingData['author_url'] ?? ''];
        $formData['facebook'] = ['label' => 'Facebook', 'col' => 2, 'type' => 'text', 'value' => $settingData['facebook'] ?? ''];
        $formData['twitter'] = ['label' => 'Twitter', 'col' => 2, 'type' => 'text', 'value' => $settingData['twitter'] ?? ''];
        $formData['youtube'] = ['label' => 'Youtube', 'col' => 2, 'type' => 'text', 'value' => $settingData['youtube'] ?? ''];
        $formData['instagram'] = ['label' => 'Instagram', 'col' => 2, 'type' => 'text', 'value' => $settingData['instagram'] ?? ''];

        $formData['privacy_meta'] = ['label' => 'Privacy Policy', 'type' => 'header'];
        $formData['privacy_policy'] = ['label' => 'Nội dung điều khoản dịch vụ Website', 'col' => 12, 'rows' => 10, 'type' => 'editor', 'value' => $settingData['privacy_policy'] ?? ''];

        $formData['gdpr_meta'] = ['label' => 'GDPR Message', 'type' => 'header'];
        $formData['gdpr_status'] = ['label' => 'GDPR Cookie', 'col' => 12, 'type' => 'checkbox', 'value' => $settingData['gdpr_status'] ?? '', 'placeholder' => 'Bật thông báo GDPR'];
        $formData['gdpr_message'] = ['label' => 'Nội dung thông báo GDPR', 'col' => 12, 'type' => 'textarea', 'value' => $settingData['gdpr_message'] ?? ''];

        return $formData;
    }

    public static function getDataConfig($update = false)
    {
        $cache_key = 'vn_data_config';
        $data = cache($cache_key);
        if (!$data || $update) {
            $data = self::where('type', 'config')->pluck('data', 'name')->toArray();
            cache($cache_key, $data, 86400);
        }
        return $data;
    }

    public static function getDataConfigFormData(){
        $formData = [];
        $dataConfig = self::where('type', 'config')->get();
        if ($dataConfig) {
            foreach ($dataConfig as $item) {
                $description = $item->description;
                if(!$description){
                    $description = str_replace('_', ' ', $item->name);
                    $description = ucwords(str_replace('-', ' ', $description));
                }
                $formData[$item->name] = ['label' => $description, 'col' => 12, 'type' => 'text', 'value' => $item->data, 'required' => '', 'placeholder' => $description];
            }
        }
        return $formData;
    }

    public static function saveDataConfig(Request $request)
    {
        $data = $request->except('__token');
        foreach ($data as $key => $value) {
            self::updateOrCreate(
                ['type' => 'config', 'name' => $key],
                ['type' => 'config', 'name' => $key, 'data' => $value, 'description' => $key]
            );
        }
        self::getDataConfig(true);
        flash_message('Data config has been updated');
    }

    public static function getWebsiteConfig($update = false)
    {
        $cache_key = 'vn_website_data';
        $data = cache($cache_key);
        if (!$data || $update) {
            $data = self::where('type', 'website')->pluck('data', 'name')->toArray();
            cache($cache_key, $data, 86400);
        }
        return $data;
    }

    public static function saveWebsiteConfig(Request $request)
    {
        $data = $request->except('__token');
        foreach ($data as $key => $value) {
            self::updateOrCreate(
                ['type' => 'website', 'name' => $key],
                ['type' => 'website', 'name' => $key, 'data' => $value, 'description' => $key]
            );
        }
        self::getWebsiteConfig(true);
        flash_message('Website meta data has been updated');
    }

    public static function getWebsiteConfigFormData()
    {
        $formData = [];
        $dataConfig = self::where('type', 'website')->get();
        if ($dataConfig) {
            foreach ($dataConfig as $item) {
                $formData[$item->name] = ['label' => $item->description, 'col' => 6, 'type' => 'textarea', 'rows' => 12, 'value' => $item->data, 'required' => ''];
            }
        }
        return $formData;
    }


    public static function getMaintenanceData($update = false)
    {
        $cacheKey = 'vn_maintenance_data';
        $data = cache($cacheKey);
        if(!$data || $update){
            $maintenanceData = self::where('type', 'core')->where('name', 'maintenance')->first();
            if ($maintenanceData) {
                $maintenanceData = json_decode($maintenanceData->data, true);
                $data = [
                    'status' => $maintenanceData['status'] ?? 0,
                    'heading' => $maintenanceData['heading'] ?? '',
                    'message' => $maintenanceData['message'] ?? ''
                ];
            } else {
                $data = [
                    'status' => 0,
                    'heading' => '',
                    'message' => ''
                ];
            }
            cache($cacheKey, $data, 86400);
        }
        return $data;
    }

    public static function saveMaintenanceConfig(Request $request)
    {
        $status = $request->input('status', 0);
        $message = $request->input('message', '');
        $heading = $request->input('heading', '');

        $maintenanceData = [
            'status' => $status,
            'heading' => $heading,
            'message' => $message
        ];
        self::updateOrCreate(
            ['type' => 'core', 'name' => 'maintenance'],
            ['type' => 'core', 'name' => 'maintenance', 'data' => json_encode($maintenanceData)]
        );
        self::getMaintenanceData(true);
        flash_message('Maintenance mode has been updated');
    }

    public static function getMaintenanceConfigFormData(){
        $data = self::where('type', 'core')->where('name', 'maintenance')->first();
        $maintenanceData = $data ? json_decode($data->data, true) : [];
        return [
            'status' => ['label' => '', 'col' => 12, 'type' => 'checkbox', 'value' => $maintenanceData['status'] ?? 0, 'required' => '', 'placeholder' => 'Bật chế độ bảo trì website'],
            'heading' => ['label' => 'Tiêu đề thông báo', 'col' => 12, 'type' => 'text', 'value' => $maintenanceData['heading'] ?? '', 'required' => ''],
            'message' => ['label' => 'Nội dung thông báo', 'col' => 12, 'type' => 'editor', 'value' => $maintenanceData['message'] ?? '', 'required' => ''],
        ];
    }

    public static function appVersion(bool $update = false): string
    {
        $cacheKey = 'vn-app-version-update';
        $version = cache($cacheKey);
        if (!$version || $update) {
            $record = self::where('type', 'core')->where('name', 'version')->first();
            if ($record) {
                $version = $update ? ((int) $record->data + 1) : (int) $record->data;
                if ($update) {
                    $record->update(['data' => $version]);
                }
            } else {
                $version = 100;
                self::updateOrCreate(['type' => 'core', 'name' => 'version'], ['data' => $version]);
            }
            cache($cacheKey, $version, 86400);
        }
        return 'v' . number_format($version / 100, 2, '.', '');
    }


    public static function getConfig($key, $default, $description)
    {
        $dataConfig = self::getDataConfig();
        if (isset($dataConfig[$key])) {
            return $dataConfig[$key]['data'];
        }else{
            self::updateOrCreate(
                ['type' => 'config', 'name' => $key],
                ['type' => 'config', 'name' => $key, 'data' => $default, 'description' => $description]
            );
            self::getDataConfig(true);
        }
        return $default;
    }

    public static function getSiteConfig($name){
        $configData = self::getConfigData();
        return $configData->$name ?? '';
    }


    public static function clearConsoleData($reset = false)
    {
        self::where('type', 'core')->where('name', 'console')->delete();
        if($reset){
            self::where('type', 'core')->where('name', 'console-runtime')->delete();
            self::where('type', 'core')->where('name', 'console-logs')->delete();
        }
    }


    public static function getCommandData()
    {
        $data = self::where('type', 'core')->where('name', 'console')->first();
        return $data ? unserialize($data->data) : false;
    }

    public static function setCommandData($data)
    {
        return self::updateOrCreate(
            ['type' => 'core', 'name' => 'console'],
            ['type' => 'core', 'name' => 'console', 'data' => serialize($data)]
        );
    }

    public static function getConsoleLog($name = 'console-log'){
        $data = self::where('type', 'core')->where('name', $name)->first();
        return $data ? $data->data : '';
    }

    public static function setConsoleLogs($message)
    {
        self::updateOrCreate(
            ['type' => 'core', 'name' => 'console-runtime'],
            ['type' => 'core', 'name' => 'console-runtime', 'data' => $message]
        );
        $data = self::where('type', 'core')->where('name', 'console-logs')->first();
        if($data){
            $data->data = $data->data . "\n" . $message;
            return $data->save();
        }else{
            return self::create([
                'type' => 'core',
                'name' => 'console-logs',
                'data' => $message
            ]);
        }
    }

    public static function getData($name = ''){
        $data = self::where('type', 'database')->where('name', $name)->first();
        return $data ? json_decode($data->data, true) : [];
    }

    public static function setData($name, $value, $encode = true)
    {
        return VnConfig::updateOrCreate(
            ['type' => 'database', 'name' => $name],
            ['type' => 'database', 'name' => $name, 'data' => $encode ? json_encode($value) : $value]
        );
    }


}

<?php

namespace VnCoder\Models;

use Illuminate\Http\Request;

class VnConfig extends VnModelBase
{
    public $timestamps = false;
    protected $table = '__configs';
    protected $fillable = ['type', 'input', 'name', 'data', 'description'];

    public const SETTING_KEY = ['name', 'title', 'favicon', 'description', 'keywords', 'logo', 'photo', 'email', 'phone', 'author', 'author_url', 'address', 'copyright', 'about_us', 'facebook', 'twitter', 'youtube', 'instagram', 'gdpr_status', 'gdpr_message', 'privacy_policy'];

    public static function getConfig($key, $default, $description)
    {
        $configData = self::getCacheData('config');
        if ($configData && isset($configData[$key])) {
            if($configData[$key]['input'] == 'checkbox'){
                return $configData[$key]['data'] == 1;
            }else{
                return $configData[$key]['data'];
            }
        }
        $inputType = self::determineInputType($default);
        $configDataQuery = self::where('type','config')->where('name', $key)->first();
        if ($configDataQuery) {
            self::getCacheData('config', true);
            return $configDataQuery->input == 'checkbox' ? $configDataQuery->data == 1 : $configDataQuery->data;
        }

        self::create([
            'type' => 'config',
            'name' => $key,
            'data' => $default,
            'description' => $description,
            'input' => $inputType
        ]);

        return $default;
    }

    public static function getSiteConfig($key)
    {
        $data = self::getCacheData('setting');
        return isset($data[$key]) ? $data[$key]['data'] : '';
    }

    public static function getWebConfig($update = false)
    {
        return self::getConfigData('setting', $update);
    }

    public static function getEmbedConfig($update = false)
    {
        return self::getConfigData('website', $update);
    }

    public static function getOptionsConfig($update = false)
    {
        return self::getConfigData('options', $update);
    }

    private static function getConfigData($type, $update = false)
    {
        $cacheKey = 'config_info_' . $type;
        $configData = cache($cacheKey);
        if (!$configData || $update) {
            $configData = newObject();
            $configQuery = self::getCacheData($type, true);
            if($configQuery){
                foreach ($configQuery as $key => $item) {
                    $configData->$key = $item['data'];
                }
            }
            cache($cacheKey, $configData, 86400);
        }
        return $configData;
    }


    public static function getSettingConfigForm()
    {
        $formData = [];
        $dataConfig = self::getQueryData('setting');
        $settingData = [];
        foreach ($dataConfig as $item) {
            $settingData[$item->name] = $item->data;
        }

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

    public static function saveSettingConfig(Request $request)
    {
        $data = $request->except('__token');
        foreach ($data as $key => $value) {
            if (in_array($key, self::SETTING_KEY)) {
                if ($key == 'logo' || $key == 'photo' || $key == 'favicon') {
                    if ($request->hasFile($key)) {
                        $uploadData = VnUploads::photo($request->file($key), 'setting-' . $key);
                        if ($uploadData['success']) {
                            $value = $uploadData['path'];
                            self::updateOrCreate(['type' => 'setting', 'name' => $key], ['data' => $value]);
                        } else {
                            flash_message($uploadData['message'], 'danger');
                        }
                    }
                } else {
                    self::updateOrCreate(['type' => 'setting', 'name' => $key], ['data' => $value]);
                }
            }
        }
        self::updateCacheData('setting');
        flash_message('Setting has been updated', 'success');
    }

    public static function getDataConfigForm()
    {
        $formData = [];
        $dataConfig = self::getQueryData('config');
        if ($dataConfig) {
            foreach ($dataConfig as $item) {
                $description = $item->description;
                if(!$description){
                    $description = str_replace('_', ' ', $item->name);
                    $description = ucwords(str_replace('-', ' ', $description));
                }
                $formData[$item->name] = ['label' => $description, 'col' => 12, 'type' => $item->input, 'value' => $item->data, 'required' => '', 'placeholder' => $description];
            }
        }
        return $formData;
    }

    public static function saveDataConfig(Request $request)
    {
        $data = $request->except('__token');
        foreach ($data as $key => $value) {
            self::updateOrCreate(['type' => 'config', 'name' => $key], ['type' => 'config', 'name' => $key, 'data' => $value]);
        }
        self::getCacheData('config', true);
        flash_message('Setting has been updated', 'success');
    }

    public static function getExtraConfigForm()
    {
        $formData = [];
        $dataConfig = self::getQueryData('website');
        if ($dataConfig) {
            foreach ($dataConfig as $item) {
                $formData[$item->name] = ['label' => $item->description, 'col' => 6, 'type' => 'textarea', 'rows' => 12, 'value' => $item->data, 'required' => ''];
            }
        }
        return $formData;
    }

    public static function saveExtraConfig(Request $request)
    {
        $data = $request->all();
        unset($data['__token']);
        foreach ($data as $key => $value) {
            self::updateOrCreate(['type' => 'website', 'name' => $key], ['type' => 'website', 'name' => $key, 'data' => $value]);
        }
        self::updateCacheData('website');
        flash_message('Setting has been updated', 'success');
    }

    public static function getMaintenanceData($update = false)
    {
        if(!$update){
            $maintenanceData = self::getConfigData('maintenance');
            $status = $maintenanceData->status ?? 0;
            $heading = $maintenanceData->heading ?? '';
            $message = $maintenanceData->message ?? '';
        }else{
            $maintenanceData = self::select('name', 'data')->where('type', 'maintenance')->pluck('data', 'name');
            $status = $maintenanceData['status'] ?? 0;
            $heading = $maintenanceData['heading'] ?? '';
            $message = $maintenanceData['message'] ?? '';
        }
        return [
            'status' => $status,
            'heading' => $heading,
            'message' => $message
        ];
    }

    public static function getMaintenanceConfigForm()
    {
        $maintenanceData = self::getMaintenanceData();
        return [
            'status' => ['label' => '', 'col' => 12, 'type' => 'checkbox', 'value' => $maintenanceData['status'], 'required' => '', 'placeholder' => 'Bật chế độ bảo trì website'],
            'heading' => ['label' => 'Tiêu đề thông báo', 'col' => 12, 'type' => 'text', 'value' => $maintenanceData['heading'], 'required' => ''],
            'message' => ['label' => 'Nội dung thông báo', 'col' => 12, 'type' => 'editor', 'value' => $maintenanceData['message'], 'required' => ''],
        ];
    }

    public static function saveMaintenanceConfig(Request $request)
    {
        $status = $request->input('status', 0);
        $message = $request->input('message', '');
        $heading = $request->input('heading', '');
        self::updateOrCreate(
            ['type' => 'maintenance', 'name' => 'status'],
            ['type' => 'maintenance', 'name' => 'status', 'data' => $status]
        );
        self::updateOrCreate(
            ['type' => 'maintenance', 'name' => 'heading'],
            ['type' => 'maintenance', 'name' => 'heading', 'data' => $heading]
        );
        self::updateOrCreate(
            ['type' => 'maintenance', 'name' => 'message'],
            ['type' => 'maintenance', 'name' => 'message', 'data' => $message]
        );

        self::updateCacheData('maintenance');
        flash_message('Setting has been updated');
    }
    
    public static function getQueryData($type)
    {
        return self::where('type', $type)->get();
    }

    private static function getCacheData($type, $update = false)
    {
        $cacheKey = 'config_data_' . $type;
        $configData = cache($cacheKey);
        if (!$configData || $update) {
            $configQueryData = self::getQueryData($type);
            foreach ($configQueryData as $item) {
                $configData[$item->name] = [
                    'data' => $item->data,
                    'input' => $item->input,
                    'description' => $item->description
                ];
            }
            cache($cacheKey, $configData, 86400);
        }
        return $configData;
    }

    private static function updateCacheData($type)
    {
        self::getCacheData($type, true);
        self::getConfigData($type, true);
    }

    private static function determineInputType($default)
    {
        if (is_bool($default)) {
            return 'checkbox';
        }
        if (is_numeric($default)) {
            return 'number';
        }
        if (is_string($default) && strlen($default) > 20) {
            return 'textarea';
        }
        return 'text';
    }

    public static function deleteConsole($name)
    {
        return self::where('type', 'console')->where('name', $name)->delete();
    }

    public static function deleteConsoleData()
    {
        return self::where('type', 'console')->delete();
    }

    public static function getConsoleData($name, $decode = true)
    {
        $data = self::where('type', 'console')->where('name', $name)->first();
        return $decode && $data ? json_decode($data->data, true) : ($data->data ?? []);
    }

    public static function setConsoleData($name, $value, $encode = true)
    {
        return VnConfig::updateOrCreate(
            ['type' => 'console', 'name' => $name],
            ['type' => 'console', 'name' => $name, 'data' => $encode ? json_encode($value) : $value]
        );
    }

    public static function appVersion(bool $update = false): string
    {
        $cacheKey = 'vn-app-version-update';
        $version = cache($cacheKey);
        if (!$version || $update) {
            $record = self::where('type', 'app')->where('name', 'version')->first();
            if ($record) {
                $version = $update ? ((int) $record->data + 1) : (int) $record->data;
                if ($update) {
                    $record->update(['data' => $version]);
                }
            } else {
                $version = 100;
                self::updateOrCreate(['type' => 'app', 'name' => 'version'], ['data' => $version]);
            }
            cache($cacheKey, $version, 86400);
        }
        return 'v' . number_format($version / 100, 2, '.', '');
    }


}

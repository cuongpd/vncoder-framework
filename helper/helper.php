<?php

use Illuminate\Support\Facades\Log;
use JetBrains\PhpStorm\NoReturn;
use VnCoder\Core\Queue\SendEmailQueue;
use VnCoder\Helper\GeoIP\Reader;
use VnCoder\Helper\QRCode;
use VnCoder\Models\VnConfig;

if(!function_exists('dp')){
    #[NoReturn]
    function dp($data){
        echo '<pre>';
        print_r($data);
        echo '</pre>';
        exit(1);
    }
}

if (!function_exists('newObject')) {
    function newObject(): stdClass
    {
        return new stdClass();
    }
}

if (!function_exists('getConfig')) {
    function getConfig($key, $default, $description)
    {
        return VnConfig::getConfig($key, $default, $description);
    }
}

if (!function_exists('getSiteConfig')) {
    function getSiteConfig($key)
    {
        return VnConfig::getSiteConfig($key);
    }
}

if(!function_exists('sendMail')){
    function sendMail($toEmail, $subject, $message){
        return dispatch(new SendEmailQueue($toEmail, $subject, $message));
    }
}


if (!function_exists('session')) {
    function session($key = null)
    {
        if (is_null($key)) {
            return app('session');
        }

        if (is_array($key)) {
            return app('session')->put($key);
        }

        return app('session')->get($key);
    }
}


if (!function_exists('request')) {
    function request($key = null, $default = null)
    {
        if ($key === null) {
            return app('request');
        }

        if (is_array($key)) {
            return app('request')->only($key);
        }

        $value = app('request')->__get($key);
        if (!$value) {
            return $default;
        }
        return $value;
    }
}

if (!function_exists('cookie')) {
    function cookie($key, $value = null, $expires = 30 * 86400)
    {
        if (is_null($value)) {
            return $_COOKIE[$key] ?? null;
        }
        $timeExpires = $expires > 0 ? TIME_NOW + $expires : -1;
        setcookie($key, $value, $timeExpires, '/');
        return null;
    }
}

if (!function_exists('cache')) {
    function cache($key, $value = '[__cache_no_value__]', $minutes = 60)
    {
        $cache = app('cache');
        if ($value === '[__cache_no_value__]') {
            return $cache->get($key);
        }
        if ($value === null || $value === '') {
            return $cache->forget($key);
        }
        return $cache->put($key, $value, $minutes);
    }
}

if(!function_exists('logData')){
    function logData($name, $data){
        if(is_array($data) || is_object($data)){
            $data = json_encode($data);
        }
        $message = date('Y-m-d H:i:s', TIME_NOW) . PHP_EOL . $data . PHP_EOL;
        file_put_contents(storage_path('logs/vncoder-' . $name . '.txt'), $message, FILE_APPEND);
    }
}

if(!function_exists('logMessage')){
    function logMessage($name, $data){
        if(is_array($data) || is_object($data)){
            $data = json_encode($data);
        }
        file_put_contents(storage_path('logs/message-' . $name . '.txt'),  $data);
    }
}


if(!function_exists('logInfo')){
    function logInfo($message){
        Log::info($message);
    }
}

if(!function_exists('logError')){
    function logError($message, $context = []){
        Log::error($message, $context);
    }
}

if (!function_exists('minify_output')) {
    function minify_output($buffer)
    {
        $search = array('/>\s+/', '/\s+</', '/(\s)+/');
        $replace = array('> ', '<', '\\1');
        return preg_replace($search, $replace, $buffer);
    }
}

if (!function_exists('core_static')) {
    function core_static($path = ''): string
    {
        return BASE_URL . 'core/' . $path;
    }
}

if (!function_exists('flash_message')) {
    function flash_message($message = null, $type = 'success')
    {
        session()->flash('__message', $message);
        session()->flash('__message_type', $type);
    }
}

if (!function_exists('flash_message_status')) {
    function flash_message_status()
    {
        $flash_type = session('__message_type');
        if (!in_array($flash_type, ['warning', 'success', 'error', 'info'])) {
            $flash_type = 'success';
        }
        return $flash_type == 'error' ? -1 : 1;
    }
}

if (!function_exists('show_message')) {
    function show_message()
    {
        return session('__message', '');
    }
}

if (!function_exists('safe_text')) {
    function safe_text($str) {
        $str = trim(mb_strtolower($str, 'UTF-8'));
        $char_map = array(
            'à' => 'a', 'á' => 'a', 'ả' => 'a', 'ã' => 'a', 'ạ' => 'a',
            'ă' => 'a', 'ằ' => 'a', 'ắ' => 'a', 'ẳ' => 'a', 'ẵ' => 'a', 'ặ' => 'a',
            'â' => 'a', 'ầ' => 'a', 'ấ' => 'a', 'ẩ' => 'a', 'ẫ' => 'a', 'ậ' => 'a',
            'đ' => 'd',
            'è' => 'e', 'é' => 'e', 'ẻ' => 'e', 'ẽ' => 'e', 'ẹ' => 'e',
            'ê' => 'e', 'ề' => 'e', 'ế' => 'e', 'ể' => 'e', 'ễ' => 'e', 'ệ' => 'e',
            'ì' => 'i', 'í' => 'i', 'ỉ' => 'i', 'ĩ' => 'i', 'ị' => 'i',
            'ò' => 'o', 'ó' => 'o', 'ỏ' => 'o', 'õ' => 'o', 'ọ' => 'o',
            'ô' => 'o', 'ồ' => 'o', 'ố' => 'o', 'ổ' => 'o', 'ỗ' => 'o', 'ộ' => 'o',
            'ơ' => 'o', 'ờ' => 'o', 'ớ' => 'o', 'ở' => 'o', 'ỡ' => 'o', 'ợ' => 'o',
            'ù' => 'u', 'ú' => 'u', 'ủ' => 'u', 'ũ' => 'u', 'ụ' => 'u',
            'ư' => 'u', 'ừ' => 'u', 'ứ' => 'u', 'ử' => 'u', 'ữ' => 'u', 'ự' => 'u',
            'ỳ' => 'y', 'ý' => 'y', 'ỷ' => 'y', 'ỹ' => 'y', 'ỵ' => 'y',
            ' ' => '-',
        );
        $string = strtr($str, $char_map);
        $string = preg_replace('/[^a-z0-9\-]/', '', $string);
        $string = preg_replace('/-+/', '-', $string);
        return trim($string, " -\t\n\r\0\x0B");
    }
}

if(!function_exists('safe_name')){
    function safe_name($str){
        $str = safe_text($str);
        return ucwords(str_replace('-', ' ', $str));
    }
}


if (!function_exists('makeDir')) {
    function makeDir($path): bool
    {
        if (is_dir($path)) {
            return true;
        }
        $prev_path = substr($path, 0, strrpos($path, '/', -2) + 1);
        $return = makeDir($prev_path);
        return $return && is_writable($prev_path) && mkdir($path);
    }
}

if (!function_exists('str_limit')) {
    function str_limit($string, $max = 255)
    {
        if (mb_strlen($string, 'utf-8') >= $max) {
            $string = mb_substr($string, 0, $max - 5, 'utf-8') . '...';
        }
        return $string;
    }
}


if(!function_exists('str_ends_with')){
    function str_ends_with($haystack, $needle) {
        $length = strlen($needle);
        if ($length === 0) {
            return true;
        }
        return (substr($haystack, -$length) === $needle);
    }

}

if(!function_exists('str_starts_with')){
    function str_starts_with($haystack, $needle) {
        $length = strlen($needle);
        return substr($haystack, 0, $length) === $needle;
    }

}

if (!function_exists('csrf_field')) {
    function csrf_field(): string
    {
        return '<input type="hidden" name="__token" value="' . request()->session()->token() . '">';
    }
}

if (!function_exists('csrf_token')) {
    function csrf_token(): string
    {
        return request()->session()->token();
    }
}

if (!function_exists('getNameByController')) {
    function getNameByController($controller): string
    {
        $controller = ucwords(str_replace('-', ' ', $controller));
        $controller = str_replace(' ', '', $controller);
        return $controller . "Controller";
    }
}

if (!function_exists('getNameByAction')) {
    function getNameByAction($action = null, $postMethod = false): string
    {
        if ($action) {
            if (preg_match('/^[\d-]/', $action)) {
                $action = 'N' . $action;
            }
            $action = str_replace(" ", "_", trim(ucwords(str_replace("-", " ", $action))));
        } else {
            $action = 'Index';
        }
        return $postMethod ? $action . '_Action_Submit' : $action . '_Action';
    }
}

if (!function_exists('backend')) {
    function backend($slug = '/')
    {
        return BASE_URL . 'backend/' . preg_replace('/\.(?!html$)/', '/', trim(strtolower($slug), '/'));
    }
}

if (!function_exists('getStringBetween')) {
    function getStringBetween($str, $starting_word, $ending_word)
    {
        $subtring_start = strpos($str, $starting_word);
        $subtring_start += strlen($starting_word);
        $size = strpos($str, $ending_word, $subtring_start) - $subtring_start;
        return $size > 0 ? trim(substr($str, $subtring_start, $size)) : "";
    }
}

if (!function_exists('str_contains')) {
    function str_contains($haystack, $needle) {
        return $needle !== '' && mb_strpos($haystack, $needle) !== false;
    }
}

if(!function_exists('toKebabCase')){
    function toKebabCase($input){
        $output = strtolower(preg_replace('/([a-z])([A-Z])/', '$1-$2', $input));
        return str_replace(' ', '-', $output);
    }
}

if(!function_exists('toPascalCase')){
    function toPascalCase($input) {
        return str_replace(' ', '', ucwords(str_replace('-', ' ', $input)));
    }
}

if(!function_exists('random')){
    function random($length=10, $type='string', $convert=0)
    {
        $config = array(
            'number'=>'1234567890',
            'letter'=>'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',
            'string'=>'abcdefghjkmnpqrstuvwxyzABCDEFGHJKMNPQRSTUVWXYZ23456789',
            'all'=>'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890',
            'hex'=> 'ABCDEF1234567890'
        );

        if(!isset($config[$type])) $type = 'letter';
        $string = $config[$type];

        $code = '';
        $strlen = strlen($string) -1;
        for($i = 0; $i < $length; $i++){
            $code .= $string[mt_rand(0, $strlen)];
        }
        if(!empty($convert)){
            $code = ($convert > 0)? strtoupper($code) : strtolower($code);
        }
        return $code;
    }
}

if(!function_exists('getAmount')){
    function getAmount($amount, $length = 2) {
        $amount = round($amount ?? 0, $length);
        return $amount + 0;
    }
}

if(!function_exists('showAmount')){
    function showAmount($amount, $decimal = 2, $separate = true, $exceptZeros = false) {
        $separator = '';
        if ($separate) {
            $separator = ',';
        }
        $printAmount = number_format($amount, $decimal, '.', $separator);
        if ($exceptZeros) {
            $exp = explode('.', $printAmount);
            if ($exp[1] * 1 == 0) {
                $printAmount = $exp[0];
            } else {
                $printAmount = rtrim($printAmount, '0');
            }
        }
        return $printAmount;
    }
}

if(!function_exists('encryptPassword')){
    function encryptPassword($password){
        return md5('code-' . $password . '-198');
    }
}

if(!function_exists('checkPassword')){
    function checkPassword($password, $encrypt){
        return $encrypt == encryptPassword($password);
    }
}

if(!function_exists('qrCodeGenerator')){
    function qrCodeGenerator($data, $options = []){
        return (QRCode::init($data, $options))->base64();
    }
}


if(!function_exists('sendToWirePusher')){
    function sendToWirePusher($title, $message, $image_url = '', $action = '', $type = 'VIP' ){
        $pusherDeviceId = env('WIRE_PUSHER_DEVICE');
        $parameters = [
            'id'      => $pusherDeviceId,
            'title'   => $title,
            'message' => $message
        ];

        if($image_url){
            $parameters['image_url'] = $image_url;
        }
        if($action){
            $parameters['action'] = $action;
        }
        if($type){
            $parameters['type'] = $type;
        }

        $ch = curl_init("https://wirepusher.com/send");
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $parameters);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);

        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }
}

if(!function_exists('sendToTelegram')) {
    function sendToTelegram($message, $chat_id = null)
    {
        $botToken = env('TELEGRAM_BOT_TOKEN');
        $chat_id = $chat_id ?? env('TELEGRAM_CHAT_ID');
        if (!$botToken || !$chat_id) {
            return false;
        }
        $paramData = [
            "chat_id" => $chat_id,
            "parse_mode" => 'HTML',
            "text" => $message
        ];
        $uri = "https://api.telegram.org/bot". $botToken ."/sendMessage";
        $ch = curl_init($uri);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        if ($paramData) {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $paramData);
        }
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result ? json_decode($result, true) : false;
    }
}


if(!function_exists('encryptData')){
    function encryptData($data)
    {
        $data = json_encode($data, JSON_THROW_ON_ERROR);
        $aes_key = hash('sha256', env('AES_KEY_SECRET', 'a50d2601168f8028048167d423211819'), true);
        $aes_iv = hex2bin(env('AES_IV_SECRET', '21a977be498d223378a5d5c256f59125'));
        $ciphertext = openssl_encrypt($data, 'AES-256-CBC', $aes_key, OPENSSL_RAW_DATA, $aes_iv);
        return str_replace(['+', '/'], ['-', '_'], base64_encode($aes_iv . $ciphertext));
    }
}

if(!function_exists('decryptData')){
    function decryptData($payload)
    {
        $aes_key = hash('sha256', env('AES_KEY_SECRET', 'a50d2601168f8028048167d423211819'), true);
        $ciphertext = base64_decode(str_replace(['-', '_'], ['+', '/'], $payload));
        $aes_iv = substr($ciphertext, 0, 16);
        $ciphertext = substr($ciphertext, 16);
        $data = openssl_decrypt($ciphertext, 'AES-256-CBC', $aes_key, OPENSSL_RAW_DATA, $aes_iv);
        return json_decode($data, true);
    }
}

if(!function_exists("csvReader")){
    function csvReader($csvFile){
        $data = [];
        if (($handle = fopen($csvFile, 'r')) !== false) {
            if (($headers = fgetcsv($handle, 1000, ',')) !== false) {
                $headers = array_map('safe_text', $headers);
                while (($row = fgetcsv($handle, 1000, ',')) !== false) {
                    $data[] = array_combine($headers, $row);
                }
            }
            fclose($handle);
        }
        return $data;
    }
}

function convertNumber($number) {
    $number = str_replace(',', '', $number);
    return (int) floor($number);
}

if(!function_exists('ipInfo')){
    function ipInfo($ip = '127.0.0.1'){
        $countryCode = "Z0";
        $dbReader = new Reader();
        $info = $dbReader->get($ip);
        if ($info && isset($info['country']['iso_code'])) {
            $countryCode = $info['country']['iso_code'];
        }
        return $countryCode;
    }
}

if(!function_exists('isLocalDomain')){
    function isLocalDomain($url) {
        $host = parse_url($url, PHP_URL_HOST);
        if (!$host) return false;
        if ($host === 'localhost') return true;
        if (filter_var($host, FILTER_VALIDATE_IP)) {
            if (
                preg_match('/^127\./', $host) || // Loopback
                preg_match('/^192\.168\./', $host) || // Private range
                preg_match('/^10\./', $host) || // Private range
                preg_match('/^172\.(1[6-9]|2[0-9]|3[0-1])\./', $host) // 172.16.0.0 – 172.31.255.255
            ) {
                return true;
            }
        }
        if (str_ends_with($host, '.cm')) {
            return true;
        }
        return false;
    }
}

if(!function_exists('json_content')){
    function json_content($filename, $associative = true)
    {
        if(is_file($filename)){
            return json_decode(file_get_contents($filename), $associative);
        }
        return [];
    }
}

if (!function_exists('get_contents')) {
    function get_contents(string $filename): string
    {
        return is_file($filename) ? file_get_contents($filename) : '';
    }
}

if (!function_exists('put_contents')) {
    function put_contents(string $file, string $data): bool
    {
        $dir = dirname($file);
        if (!is_dir($dir) && !mkdir($dir, 0777, true) && !is_dir($dir)) {
            return false;
        }

        $fp = fopen($file, 'c+');
        if (!$fp) return false;
        $success = false;
        if (flock($fp, LOCK_EX)) {
            ftruncate($fp, 0);
            rewind($fp);
            $written = fwrite($fp, $data);
            fflush($fp);
            flock($fp, LOCK_UN);
            $success = ($written !== false);
        }
        fclose($fp);
        return $success;
    }
}

if(!function_exists('encryptData')){
    function encryptData($data){
        return base64_encode(serialize($data));
    }
}

if(!function_exists('decryptData')){
    function decryptData($data){
        if (empty($data)) {
            return [];
        }
        return unserialize(base64_decode($data));
    }
}

if(!function_exists('base64UrlDecode')){
    function base64UrlDecode($input){
        $replaced = strtr($input, '-_', '+/');
        $pad = strlen($replaced) % 4;
        if ($pad) { $replaced .= str_repeat('=', 4 - $pad); }
        return base64_decode($replaced) ?: '';
    }
}

if(!function_exists('parseSignedRequest')){
    function parseSignedRequest($signedRequest, $appSecret){
        $parts = explode('.', $signedRequest, 2);
        if (count($parts) !== 2) { return null; }
        [$encodedSig, $payload] = $parts;
        $sig     = base64UrlDecode($encodedSig);
        $dataRaw = base64UrlDecode($payload);
        $data    = json_decode($dataRaw, true);
        if (!is_array($data)) { return null; }
        $expected = hash_hmac('sha256', $payload, $appSecret, true);
        if (!hash_equals($expected, $sig)) { return null; }
        return $data;
    }
}

// Debugbar
if (!function_exists('debugbar')) {
    function debugbar()
    {
        return app(\VnCoder\Debugbar\LaravelDebugbar::class);
    }
}

if (!function_exists('debug')) {
    function debug($value)
    {
        $debugbar = debugbar();
        foreach (func_get_args() as $value) {
            $debugbar->addMessage($value, 'debug');
        }
    }
}

if (!function_exists('start_measure')) {
    function start_measure($name, $label = null)
    {
        debugbar()->startMeasure($name, $label);
    }
}

if (!function_exists('stop_measure')) {
    function stop_measure($name)
    {
        debugbar()->stopMeasure($name);
    }
}

if (!function_exists('add_measure')) {
    function add_measure($label, $start, $end)
    {
        debugbar()->addMeasure($label, $start, $end);
    }
}

if (!function_exists('measure')) {
    function measure($label, \Closure $closure)
    {
        return debugbar()->measure($label, $closure);
    }
}
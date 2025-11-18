<?php

if (!function_exists('getParam')) {
    function getParam($param, $default = ''): string
    {
        foreach ([&$_GET, &$_POST, &$_REQUEST] as &$source) {
            if (isset($source[$param])) {
                return trim((string)$source[$param]);
            }
        }
        return trim((string)$default);
    }
}

if (!function_exists('getParamInt')) {
    function getParamInt($aVarName, $aVarAlt = 0): int
    {
        return (int) getParam($aVarName, $aVarAlt);
    }
}

if(!function_exists('convertToInteger')){
    function convertToInteger($str) {
        return (int) floatval(str_replace(',', '', $str));
    }
}

if (!function_exists('formatFileSize')) {
    function formatFileSize($number)
    {
        $number = getNumber($number);
        if ($number > 999999999) return round($number / 1e9, 3) . ' GB';
        if ($number > 999999)    return round($number / 1e6, 3) . ' MB';
        if ($number > 999)       return round($number / 1e3, 3) . ' kB';
        return $number > 0 ? $number . ' Byte' : '';
    }
}

if (!function_exists('formatNumberText')) {
    function formatNumberText($number)
    {
        $number = getNumber($number);
        if ($number >= 1000000000) return round($number / 1000000000, 1) . 'B+';
        if ($number >= 1000000)    return round($number / 1000000, 1) . 'M+';
        if ($number >= 1000)       return round($number / 1000, 1) . 'k+';
        return $number > 0 ? $number . '+' : '';
    }

}

if (!function_exists('formatNumber')) {
    function formatNumber($number)
    {
        if (!is_numeric($number)) return '';
        $abs = abs($number);
        if($abs == 0) return '';
        $decimals = ($abs > 1000) ? 0 : (($abs > 100) ? 1 : (($abs > 10) ? 2 : 3));
        return number_format($number, $decimals);
    }
}


if (!function_exists('getNumber')) {
    function getNumber($str)
    {
        return (int) preg_replace('/\D/', '', $str);
    }
}

if (!function_exists('stripQuotes')) {
    function stripQuotes($expression)
    {
        return str_replace(["'", '"'], '', $expression);
    }
}


if (!function_exists('timeFormat')) {
    function timeFormat($duation = 0): string
    {
        if ($duation < 60) return $duation . ' phút';
        $h = floor($duation / 60);
        $p = $duation % 60;
        return $p === 0 ? $h . ' giờ' : $h . ' giờ ' . $p . ' phút';
    }
}

if (!function_exists('hashInfo')) {
    function hashInfo($input, $num = 2)
    {
        return substr(md5('vncoder-' . $input . '-2022'), 0, $num);
    }
}

// Chuyển đổi ID thành chuỗi 32 ký tự
if (!function_exists('encryptNumber')) {
    function encryptNumber($uid)
    {
        $uid = (int)$uid;
        if ($uid > 17592186044415 || $uid < 0) return -1;
        $md5Data = md5($uid);
        $uidHexa = $uid + 17592186044416 + 12021990;
        $uidToHex = dechex($uidHexa);
        return substr($md5Data, 0, 4) . substr($uidToHex, 0, 4) . substr($md5Data, 6, 4) . substr($uidToHex, 4, 4) . substr($md5Data, 12, 4) . substr($uidToHex, -4) . substr($md5Data, 18, 4) . substr($md5Data, 24, 4);
    }
}

if (!function_exists('decryptNumber')) {
    function decryptNumber($data = '')
    {
        if (strlen($data) == 32) {
            $uidHex = substr($data, 4, 4) . substr($data, 12, 4) . substr($data, 20, 4);
            $decData = hexdec($uidHex);
            $uid = $decData - 17592186044416 - 12021990;
            $md5Input = md5($uid);
            if (substr($md5Input, 0, 4) == substr($data, 0, 4) && substr($md5Input, 6, 4) == substr($data, 8, 4) && substr($md5Input, 12, 4) == substr($data, 16, 4) && substr($md5Input, 18, 4) == substr($data, 24, 4) && substr($md5Input, 24, 4) == substr($data, 28, 4)) {
                return $uid;
            }
        }
        return 0;
    }
}

if (!function_exists('formBuilder')) {
    function formBuilder($formData, $csrf_token = false)
    {
        return formBuilderManager($formData, $csrf_token);
    }
}

if (!function_exists('formHorizontalBuilder')) {
    function formHorizontalBuilder($formData, $csrf_token = false)
    {
        return formBuilderManager($formData, $csrf_token, true);
    }
}

if (!function_exists('formBuilderManager')) {
    function formBuilderManager($formData, $csrf_token = false, $isHorizontal = false)
    {
        if (!is_array($formData) || count($formData) == 0) return '';
        $submitUrlAction = request()->url();
        $hasFileInput = false;
        $html = '<form class="form-data" id="form-data" method="POST" accept-charset="utf-8" enctype="multipart/form-data" action="'.$submitUrlAction.'">';
        $html .= '<div class="row align-items-stretch">';
        if ($csrf_token) {
            $html .= "\n" . csrf_field() . "\n";
        }

        foreach ($formData as $key => $item) {
            $item['col'] = $item['col'] ?? 12;
            $item['value'] = $item['value'] ?? '';
            $item['helper'] = $item['helper'] ?? '';
            $item['type'] = $item['type'] ?? 'text';
            $item['label'] = $item['label'] ?? ucfirst($key);
            $item['placeholder'] = $item['placeholder'] ?? $item['label'];
            $item['required'] = isset($item['required']) && $item['required'] ? ' required' : '';
            $item['options'] = $item['options'] ?? [];
            if($item['type'] == 'file' || $item['type'] == 'photo' || $item['type'] == 'video' || $item['type'] == 'audio') $hasFileInput = true;

            if($item['type'] == 'header'){
                $html .= '<div class="col-12"><h4 class="form-header">'.$item['label'].'</h4></div>';
                continue;
            }

            if ($item['required']) $item['label'] .= ' (*)';
            if ($item['type'] == 'hidden') {
                $html .= '<input type="hidden" name="' . $key . '" value="' . $item['value'] . '">';
                continue;
            }

            if($item['type'] == 'checkbox'){
                if($isHorizontal){
                    $html .= '<div class="row form-group mb-3">';
                    $html .= '<div class="col-sm-12">';
                }else{
                    $html .= '<div class="col-md-' . $item['col'] . ' d-flex">';
                    $html .= '<div class="form-group flex-fill">';
                }
                $html .= '<input type="hidden" name="' . $key . '" value="0">';
                $checked = $item['value'] ? 'checked' : '';
                $html .= '<div class="form-check"><input type="checkbox" class="form-control form-check-input input-primary" id="' . $key . '" name="' . $key . '" value="1" ' . $checked . '> <label class="form-control-sm form-check-label" for="' . $key . '">' . $item['placeholder'] . '</label></div>';
                $html .= '</div>';
                $html .= '</div>';
                continue;
            }
            if($isHorizontal){
                $html .= '<div class="row form-group mb-3">';
                $html .= '<label class="col-sm-12" for="' . $key . '">' . $item['label'] . '</label>';
                $html .= '<div class="col-sm-12">';
            }else{
                $html .= '<div class="col-md-' . $item['col'] . '">';
                $html .= '<div class="form-group">';
                $html .= '<label for="' . $key . '">' . $item['label'] . '</label>';
            }

            switch ($item['type']) {
                case 'number':
                    $min = isset($item['min']) ? 'min="' . $item['min'] . '"' : '';
                    $max = isset($item['max']) ? 'max="' . $item['max'] . '"' : '';
                    $step = isset($item['step']) ? 'step="' . $item['step'] . '"' : '';
                    $html .= '<input type="number" class="form-control form-control-sm border" id="' . $key . '" name="' . $key . '" value="' . $item['value'] . '" ' . $min . ' ' . $max . ' ' . $step . ' ' . $item['required'] . '>';
                    break;
                case 'textarea':
                    $maxlength = isset($item['maxlength']) ? (int)$item['maxlength'] : 0;
                    $textarea_rows = isset($item['rows']) ? 'rows=' . (int)$item['rows'] : 'rows=2';
                    $htmlExtra = $maxlength ? 'class="form-control form-control-sm form-textarea border input-maxlength" maxlength="' . $maxlength . '"' : 'class="form-control form-textarea border"';
                    $html .= '<textarea ' . $htmlExtra . ' ' . $textarea_rows . ' id="' . $key . '" name="' . $key . '" ' . $item['required'] . '>' . $item['value'] . '</textarea>';
                    break;
                case 'editor':
                    $textarea_rows = isset($item['rows']) ? 'rows=' . (int)$item['rows'] : 'rows=20';
                    $html .= '<textarea class="form-control form-control-sm form-textarea border tinymce" ' . $textarea_rows . ' id="' . $key . '" name="' . $key . '" ' . $item['required'] . '>' . $item['value'] . '</textarea>';
                    break;
                case 'select':
                    if(isset($item['multiple']) && $item['multiple']){
                        $html .= '<select class="form-select form-select-sm js-choices" id="' . $key . '" name="' . $key . '[]" multiple ' . $item['required'] . '>';
                    }else{
                        $js_choices = isset($item['choices']) && $item['choices'] ? ' js-choices' : '';
                        $html .= '<select class="form-select form-select-sm '.$js_choices.'" id="' . $key . '" name="' . $key . '" ' . $item['required'] . '>';
                        $html .= '<option value="" disabled selected>'.$item['placeholder'].'</option>';
                    }
                    foreach ($item['options'] as $o_value => $o_label) {
                        $selected = $o_value == $item['value'] ? 'selected' : '';
                        $html .= '<option value="' . $o_value . '" ' . $selected . '>' . $o_label . '</option>';
                    }
                    $html .= '</select>';
                    break;
                case 'photo':
                    $hasFileInput = true;
                    $onchange_key = 'input-photo-' . $key;
                    $onchange_action = 'onchange="previewImageUpload(event, \'' . $onchange_key . '\')"';
                    $img_src = $item['value'] ? url($item['value']) : 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=';
                    $html .= '<div class="input-group mb-3">';
                    $html .= '<input type="file" class="form-control form-control-sm border input-photo" id="' . $key . '" name="' . $key . '" ' . $onchange_action . ' value="' . $item['value'] . '" ' . $item['required'] . ' accept="image/*" max-file-size="2048">';
                    $html .= '<span class="input-group-text" style="padding:0;"><img id="' . $onchange_key . '" src="' . $img_src . '" style="max-height:36px;width:auto;text-align: center;"></span>';
                    $html .= '</div>';
                    break;
                case 'video':
                    $hasFileInput = true;
                    $html .= '<input type="file" class="form-control form-control-sm border" id="' . $key . '" name="' . $key . '" value="' . $item['value'] . '" ' . $item['required'] . ' accept="video/*">';
                    break;
                case 'audio':
                    $hasFileInput = true;
                    $html .= '<input type="file" class="form-control form-control-sm border" id="' . $key . '" name="' . $key . '" value="' . $item['value'] . '" ' . $item['required'] . ' accept="audio/*">';
                    break;
                case 'file':
                    $hasFileInput = true;
                    $html .= '<input type="file" class="form-control form-control-sm border" id="' . $key . '" name="' . $key . '" value="' . $item['value'] . '" ' . $item['required'] . ' accept=".doc, .docx, .pdf, .ppt, .pptx, .xls, .xlsx, .zip, .rar, .txt">';
                    break;
                case 'date':
                case 'date-range':
                case 'date-month':
                case 'date-time':
                    $input_class = 'class="form-control form-control-sm border air-'.$item['type'].'"';
                    $html .= '<input type="text" '.$input_class.' id="' . $key . '" name="' . $key . '" value="' . $item['value'] . '" ' . $item['required'] . ' readonly>';
                    break;
                case 'readonly':
                    $html .= '<input type="text" class="form-control form-control-sm border" id="' . $key . '" value="' . $item['value'] . '" readonly>';
                    break;
                case 'password':
                    $html .= '<input type="password" class="form-control form-control-sm border" id="' . $key . '" name="' . $key . '" value="' . $item['value'] . '" ' . $item['required'] . '>';
                    break;
                case 'checkbox2':
                    foreach ($item['options'] as $o_value => $o_label) {
                        $checked = in_array($o_value, (array)$item['value']) ? 'checked' : '';
                        $html .= '<div class="form-check form-check-inline"><input type="checkbox" class="form-control form-check-input input-primary" id="' . $key . '-' . $o_value . '" name="' . $key . '[]" value="' . $o_value . '" ' . $checked . '> <label class="form-control-sm form-check-label" for="' . $key . '-' . $o_value . '">' . $o_label . '</label></div>';
                    }
                    break;
                default:
                    $maxlength = isset($item['maxlength']) ? (int)$item['maxlength'] : 0;
                    $htmlExtra = $maxlength ? 'class="form-control form-control-sm border input-maxlength" maxlength="' . $maxlength . '"' : 'class="form-control form-control-sm border"';
                    $html .= '<input type="text" ' . $htmlExtra . ' id="' . $key . '" name="' . $key . '" ' . $maxlength . ' value="' . $item['value'] . '" ' . $item['required'] . '>';
                    if($item['helper']){
                        $html .= '<small id="'.$key.'-help" class="form-text text-muted">'.$item['helper'].'</small>';
                    }
                    break;
            }
            $html .= '</div>';
            $html .= '</div>';
        }
        $html .= '</div>';
        $html .= '<div class="row mt-2"><div class="col-12"><button class="btn btn-success d-inline-flex align-item-center"><i class="fa-duotone fa-solid fa-floppy-disks fa-24"></i>&nbsp;Submit</button></div></div>';
        $html .= '</form>';

        if (!$hasFileInput) {
            $html = str_replace('enctype="multipart/form-data"', '', $html);
        }
        return $html;
    }
}

if (!function_exists('display_form_errors')) {
    function display_form_errors($error = '')
    {
        return '<div class="invalid-feedback" style="display: block;">' . $error . '</div>';
    }
}

if(!function_exists('getCsvData')){
    function getCsvData($csvString, $delimiter = ','){
        $csvString = mb_convert_encoding($csvString, 'UTF-8', 'auto');
        $lines = explode("\n", $csvString);
        $headers = str_getcsv(array_shift($lines), $delimiter);
        $array = [];
        foreach ($lines as $line) {
            if (!empty($line)) {
                $row = str_getcsv($line, $delimiter);
                $array[] = array_combine($headers, $row);
            }
        }
        return $array;
    }
}


if(!function_exists('showCaptcha')){
    function showCaptcha($width = 100){
        $allowedDigits = [0, 2, 3, 4, 5, 6, 8, 9];
        shuffle($allowedDigits);
        $randomDigits = array_slice($allowedDigits, 0, 5);
        $code = implode('', $randomDigits);
        $height = round($width * 0.36);
        $fontSize = round($width * 0.24);
        $ret = '<div class="me-captcha-wrapper shine" style="width: '.$width.'px; height: '.$height.'px;">';
        $ret .= '<div class="me-captcha-background" style="font-size:'.$fontSize.'px">';
        foreach (str_split($code) as $n) {
            $rotateNumber = rand(-30, 45);
            $hexColor = sprintf("#%02x%02x%02x", rand(128, 255), rand(128, 255), rand(128, 255)) ;
            $ret .= '<span style="-webkit-transform: rotate(' . $rotateNumber . 'deg); color: '.$hexColor .';">' . $n . '</span>';
        }
        $captchaSecret = hash_hmac('sha256', $code, env('CAPTCHA_SECRET', 'SecureString'));
        $ret .= '<input type="hidden" name="vn_captcha_secret" value="' . $captchaSecret . '">';
        $ret .= '</div>';
        $ret .= '<div class="me-captcha-mask"></div>';
        $ret .= '</div>';
        return $ret;
    }
}

if(!function_exists('showCaptchaInput')){
    function showCaptchaInput($label = 'Nhập mã xác nhận', $width = 100){
        $inputId = rand(1000, 9999);
        $input = '<label class="label-input" for="captcha-'.$inputId.'">'.$label.'</label>';
        $input .= '<div class="input-group">';
        $input .= '<input type="text" style="font-weight: bold;" class="form-control form-control-sm" id="captcha-'.$inputId.'" name="vn_captcha" placeholder="" oninput="this.value = this.value.replace(/[^0-9]/g, \'\').slice(0, 5)" required="required">';
        $input .= '<div class="input-group-text2">';
        $input .= showCaptcha($width);
        $input .= '</div>';
        $input .= '</div>';
        return $input;
    }
}

if(!function_exists('verifyCaptcha')){
    function verifyCaptcha($captcha = null){
        if(!$captcha) $captcha = request()->vn_captcha;
        $captchaSecret = hash_hmac('sha256', $captcha, env('CAPTCHA_SECRET', 'SecureString'));
        return $captchaSecret == request()->vn_captcha_secret;
    }
}

if (!function_exists('uploadFile')) {
    function uploadFile($request, $key, $path = 'uploads')
    {
        $file = $request->file($key);
        $fileName = time() . '-' . $file->getClientOriginalName();
        $file->move($path, $fileName);
        return $path . '/' . $fileName;
    }
}


if (!function_exists('generateText')) {
    function generateText($size)
    {
        $words = explode(
            ' ',
            'a ab ad accusamus adipisci alias aliquam amet animi aperiam architecto asperiores aspernatur assumenda at atque aut beatae ' .
            'blanditiis cillum commodi consequatur corporis corrupti culpa cum cupiditate debitis delectus deleniti deserunt dicta ' .
            'dignissimos distinctio dolor ducimus duis ea eaque earum eius eligendi enim eos error esse est eum eveniet ex excepteur ' .
            'exercitationem expedita explicabo facere facilis fugiat harum hic id illum impedit in incidunt ipsa iste itaque iure iusto ' .
            'laborum laudantium libero magnam maiores maxime minim minus modi molestiae mollitia nam natus necessitatibus nemo neque ' .
            'nesciunt nihil nisi nobis non nostrum nulla numquam occaecati odio officia omnis optio pariatur perferendis perspiciatis ' .
            'placeat porro possimus praesentium proident quae quia quibus quo ratione recusandae reiciendis rem repellat reprehenderit repudiandae rerum ' .
            'saepe sapiente sequi similique sint soluta suscipit tempora tenetur totam ut ullam unde vel veniam vero vitae voluptas'
        );
        $lorem = '';
        while ($size > 0) {
            $randomWord = array_rand($words);
            $lorem .= $words[$randomWord] . ' ';
            $size = $size - strlen($words[$randomWord]);
        }
        return ucfirst(trim($lorem));
    }
}

if(!function_exists('separateCamelCase')){
    function separateCamelCase($input) {
        $output = preg_replace('/([a-z])([A-Z])/', '$1 $2', $input);
        $output = preg_replace('/([A-Z])([A-Z][a-z])/', '$1 $2', $output);
        return ucfirst(trim($output));
    }
}

if (!function_exists('relativeTime')) {
    function relativeTime($time)
    {
        if (!ctype_digit($time)) $time = strtotime($time);
        $d[0] = array(1, "second");
        $d[1] = array(60, "minute");
        $d[2] = array(3600, "hour");
        $d[3] = array(86400, "day");
        $d[4] = array(604800, "week");
        $d[5] = array(2592000, "month");
        $d[6] = array(31104000, "year");
        $w = array();
        $return = "";
        $now = time();
        $diff = ($now - $time);
        $secondsLeft = $diff;
        for ($i = 6; $i > -1; $i--) {
            $w[$i] = intval($secondsLeft / $d[$i][0]);
            $secondsLeft -= ($w[$i] * $d[$i][0]);
            if ($w[$i] != 0) {
                $return .= abs($w[$i]) . " " . $d[$i][1] . (($w[$i] > 1) ? 's' : '') . " ";
            }
        }
        $return .= ($diff > 0) ? "ago" : "left";
        return $return;
    }
}
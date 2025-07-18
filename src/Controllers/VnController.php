<?php

namespace VnCoder\Controllers;

use VnCoder\Models\VnConfig;
use Laravel\Lumen\Routing\Controller;

class VnController extends Controller
{
    protected object $metaData;
    protected array $setData = [];
    protected $isBackend = false;
    protected string $layout = 'default';
    protected string $version = APP_VERSION;
    protected string $extraHeader = '';
    protected string $extraFooter = '';
    protected string $extraHeaderCSS = '';
    protected string $extraHeaderJS = '';
    protected string $extraFooterJS = '';
    public function __construct()
    {
        $this->metaData = VnConfig::getWebConfig();
        $this->metaData->baseUrl = url();
        $this->metaData->version = $this->version;
        if (method_exists($this, 'siteInit')) {
            $this->siteInit();
        }
        $this->initAssetsCore();
    }

    public function views($bladeName, $isBackendCore = false)
    {
        if(!$this->isBackend){
            if($this->metaData->gdpr_status && !cookie('cc-bar-cookies')){
                $this->initCookieBar($this->metaData->gdpr_message);
            }
        }

        $this->setData['__currentUrl'] = request()->url();
        $this->setData['__metaData'] = $this->metaData;
        $this->setData['__extraHeader'] = $this->extraHeader;
        $this->setData['__extraHeaderCSS'] = $this->extraHeaderCSS;
        $this->setData['__extraHeaderJS'] = $this->extraHeaderJS;
        $this->setData['__extraFooter'] = $this->extraFooter;
        $this->setData['__extraFooterJS'] = $this->extraFooterJS;
        $this->setData['__webLayout'] = $this->layout;

        if ($this->isBackend) {
            $this->setData['__bladeExtendsLayout'] = 'backend::layouts.' . $this->layout;
        } else {
            $this->setData['__bladeExtendsLayout'] = 'frontend::layouts.' . $this->layout;
            $this->setData['__bladeYieldRender'] =  'frontend::pages.' . $bladeName;
        }
        if(getParamInt('__debug') == 1){
            dd($this->setData);
        }

        return view('core::website', $this->setData);
    }

    public function header($text)
    {
        $this->extraHeader .= $text . "\n";
    }

    public function footer($text)
    {
        $this->extraFooter .= $text . "\n";
    }

    public function linkCSS($linkFile, $addon = '')
    {
        $stylesheet = $this->extraStylesheet($linkFile, $addon);
        if (!str_contains($this->extraHeaderCSS, $stylesheet)) {
            $this->extraHeaderCSS .= $stylesheet."\n";
        }
    }

    public function linkJS($linkFile, $header = false)
    {
        $script = $this->extraScript($linkFile);
        if ($header) {
            if (!str_contains($this->extraHeaderJS, $script)) {
                $this->extraHeaderJS .= $script . "\n";
            }
        } else {
            if (!str_contains($this->extraFooter, $script)) {
                $this->extraFooterJS .= $script . "\n";
            }
        }
    }

    public function extraScript($linkFile)
    {
        if (!preg_match('/^((http(s?):)?\/\/)/i', $linkFile)) {
            $linkFile = BASE_URL . $linkFile . '?v=' . $this->version;
        }
        return '<script type="text/javascript" src="' . $linkFile . '"></script>';
    }

    public function extraStylesheet($linkFile, $addon = '')
    {
        if (!preg_match('/^((http(s?):)?\/\/)/i', $linkFile)) {
            $linkFile = BASE_URL . $linkFile . '?v=' . $this->version;
        }
        return '<link rel="stylesheet" type="text/css" href="' . $linkFile . '" '.$addon.'>';
    }

    public function redirect404()
    {
        return redirect()->to('404.html');
    }

    protected function initAirDatepicker()
    {
        $this->linkCSS('core/libraries/air-datepicker/air-datepicker.css');
        $this->linkJS('core/libraries/air-datepicker/air-datepicker.js');
    }

    protected function initSimpleNotify()
    {
        $this->linkCSS('core/libraries/simple-notify/simple-notify.css');
        $this->linkJS('core/libraries/simple-notify/simple-notify.js');
    }

    protected function initAssetsCore(){
        $this->linkCSS('core/libraries/fontawesome/css/fontawesome.min.css');
        $this->linkJS('core/libraries/jquery/jquery.min.js', true);
        $this->linkCSS('core/libraries/vncoder/core.min.css');
        $this->linkJS('core/libraries/vncoder/core.js', true);
    }

    protected function initCookieBar($message){
        $this->linkJS('core/libraries/cookie/cookieBar.min.js');
        $this->footer('<div id="cookieBar"></div><script>if (typeof cookieBar === "function") {new cookieBar('.json_encode(['message' => $message]).');}</script>');
    }

    protected function toJsonData($data){
        return response()->json(['status' => 1, 'message' => '', 'data' => $data]);
    }

    protected function toJsonError($message = ''){
        return response()->json(['status' => -1, 'message' => $message]);
    }

    protected function activeXmas(){
        $this->footer('<div id="xmas"><div class="xmas_tree"></div><div class="xmas_snow"></div></div>');
    }

    protected function turnOffDebugbar()
    {
        if (app()->bound('debugbar')) {
            app('debugbar')->disable();
        }
    }
}

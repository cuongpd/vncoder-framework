<?php

namespace VnCoder\Debugbar;

use DebugBar\DebugBar;
use DebugBar\JavascriptRenderer as BaseJavascriptRenderer;

class JavascriptRenderer extends BaseJavascriptRenderer
{
    // Use XHR handler by default, instead of jQuery
    protected $ajaxHandlerBindToJquery = false;
    protected $ajaxHandlerBindToXHR = true;

    public function __construct(DebugBar $debugBar, $baseUrl = null, $basePath = null)
    {
        parent::__construct($debugBar, $baseUrl, $basePath);
        $this->jsFiles['laravel-cache'] = __DIR__ . '/Resources/cache/widget.js';
        $this->cssFiles['laravel'] = __DIR__ . '/Resources/laravel-debugbar.css';
        $this->cssFiles['laravel-dark'] = __DIR__ . '/Resources/laravel-debugbar-dark-mode.css';
    }

    public function renderHead()
    {
        $cssRoute = route('debugbar.assets.css', ['v' => $this->getModifiedTime('css'), 'theme' => config('debugbar.theme', 'auto'),], true);
        $jsRoute = route('debugbar.assets.js', ['v' => $this->getModifiedTime('js')], true);

        $nonce = $this->getNonceAttribute();

        $html  = "<link rel='stylesheet' type='text/css' property='stylesheet' href='{$cssRoute}' data-turbolinks-eval='false' data-turbo-eval='false'>";
        $html .= "<script{$nonce} src='{$jsRoute}' data-turbolinks-eval='false' data-turbo-eval='false'></script>";

        if ($this->isJqueryNoConflictEnabled()) {
            $html .= "<script{$nonce} data-turbo-eval='false'>jQuery.noConflict(true);</script>" . "\n";
        }

        $inlineHtml = $this->getInlineHtml();
        if ($nonce != '') {
            $inlineHtml = preg_replace("/<(script|style)>/", "<$1{$nonce}>", $inlineHtml);
        }
        $html .= $inlineHtml;
        return $html;
    }

    protected function getInlineHtml()
    {
        $html = '';
        foreach (['head', 'css', 'js'] as $asset) {
            foreach ($this->getAssets('inline_' . $asset) as $item) {
                $html .= $item . "\n";
            }
        }

        return $html;
    }

    protected function getModifiedTime(string $type)
    {
        $files = $this->getAssets($type);
        $latest = 0;
        foreach ($files as $file) {
            $mtime = filemtime($file);
            if ($mtime > $latest) {
                $latest = $mtime;
            }
        }
        return $latest;
    }

    public function dumpAssetsToString(string $type)
    {
        $files = $this->getAssets($type);

        $content = '';
        foreach ($files as $file) {
            $content .= file_get_contents($file) . "\n";
        }

        return $content;
    }

    protected function makeUriRelativeTo($uri, $root)
    {
        if (!$root) {
            return $uri;
        }

        if (is_array($uri)) {
            $uris = [];
            foreach ($uri as $u) {
                $uris[] = $this->makeUriRelativeTo($u, $root);
            }
            return $uris;
        }

        if (str_starts_with($uri ?? '', '/') || preg_match('/^([a-zA-Z]+:\/\/|[a-zA-Z]:\/|[a-zA-Z]:\\\)/', $uri ?? '')) {
            return $uri;
        }
        return rtrim($root, '/') . "/$uri";
    }
}

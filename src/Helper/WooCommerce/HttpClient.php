<?php

namespace VnCoder\Helper\WooCommerce;

use VnCoder\Helper\WooCommerce\BasicAuth;
use VnCoder\Helper\WooCommerce\HttpClientException;
use VnCoder\Helper\WooCommerce\HttpRequest;
use VnCoder\Helper\WooCommerce\HttpResponse;
use VnCoder\Helper\WooCommerce\OAuth;
use VnCoder\Helper\WooCommerce\Options;
use VnCoder\Helper\WooCommerce\WooCommerce;

class HttpClient
{
    protected $ch;
    protected $url;

    protected $consumerKey;
    protected $consumerSecret;
    protected $options;
    private $customCurlOptions = [];

    private $request;
    private $response;
    private $responseHeaders;

    public function __construct($url, $consumerKey, $consumerSecret, $options)
    {
        if (!function_exists('curl_version')) {
            throw new HttpClientException('cURL is NOT installed on this server', -1, new HttpRequest(), new HttpResponse());
        }

        $this->options        = new Options($options);
        $this->url            = $this->buildApiUrl($url);
        $this->consumerKey    = $consumerKey;
        $this->consumerSecret = $consumerSecret;
    }

    protected function isSsl()
    {
        return str_starts_with($this->url, 'https://');
    }

    protected function buildApiUrl($url)
    {
        $api = $this->options->isWPAPI() ? $this->options->apiPrefix() : '/wc-api/';

        return rtrim($url, '/') . $api . $this->options->getVersion() . '/';
    }

    protected function buildUrlQuery($url, $parameters = [])
    {
        if (!empty($parameters)) {
            if (str_contains($url, '?')) {
                $url .= '&' . http_build_query($parameters);
            } else {
                $url .= '?' . http_build_query($parameters);
            }
        }

        return $url;
    }

    protected function authenticate($url, $method, $parameters = [])
    {
        // Setup authentication.
        if (!$this->options->isOAuthOnly() && $this->isSsl()) {
            $basicAuth = new BasicAuth(
                $this->ch,
                $this->consumerKey,
                $this->consumerSecret,
                $this->options->isQueryStringAuth(),
                $parameters
            );
            $parameters = $basicAuth->getParameters();
        } else {
            $oAuth = new OAuth(
                $url,
                $this->consumerKey,
                $this->consumerSecret,
                $this->options->getVersion(),
                $method,
                $parameters,
                $this->options->oauthTimestamp()
            );
            $parameters = $oAuth->getParameters();
        }

        return $parameters;
    }

    protected function setupMethod($method)
    {
        if ('POST' == $method) {
            curl_setopt($this->ch, CURLOPT_POST, true);
        } elseif (in_array($method, ['PUT', 'DELETE', 'OPTIONS'])) {
            curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, $method);
        }
    }

    protected function getRequestHeaders($sendData = false)
    {
        $headers = [
            'Accept'     => 'application/json',
            'User-Agent' => $this->options->userAgent() . '/' . WooCommerce::VERSION,
        ];

        if ($sendData) {
            $headers['Content-Type'] = 'application/json;charset=utf-8';
        }

        return $headers;
    }

    protected function createRequest($endpoint, $method, $data = [], $parameters = [])
    {
        $body    = '';
        $url     = $this->url . $endpoint;
        $hasData = !empty($data);
        $headers = $this->getRequestHeaders($hasData);

        if (!in_array($method, ['GET', 'POST'])) {
            $usePostMethod = false;
            if ($this->options->isMethodOverrideQuery()) {
                $parameters = array_merge(['_method' => $method], $parameters);
                $usePostMethod = true;
            }
            if ($this->options->isMethodOverrideHeader()) {
                $headers['X-HTTP-Method-Override'] = $method;
                $usePostMethod = true;
            }
            if ($usePostMethod) {
                $method = 'POST';
            }
        }

        // Setup authentication.
        $parameters = $this->authenticate($url, $method, $parameters);

        // Setup method.
        $this->setupMethod($method);

        // Include post fields.
        if ($hasData) {
            $body = json_encode($data);
            curl_setopt($this->ch, CURLOPT_POSTFIELDS, $body);
        }

        $this->request = new HttpRequest(
            $this->buildUrlQuery($url, $parameters),
            $method,
            $parameters,
            $headers,
            $body
        );

        return $this->getRequest();
    }

    protected function getResponseHeaders()
    {
        $headers = [];
        $lines   = explode("\n", $this->responseHeaders);
        $lines   = array_filter($lines, 'trim');

        foreach ($lines as $index => $line) {
            // Remove HTTP/xxx params.
            if (!str_contains($line, ': ')) {
                continue;
            }

            list($key, $value) = explode(': ', $line);
            $headers[$key] = isset($headers[$key]) ? $headers[$key] . ', ' . trim($value) : trim($value);
        }

        return $headers;
    }

    protected function createResponse()
    {
        $this->responseHeaders = '';
        curl_setopt($this->ch, CURLOPT_HEADERFUNCTION, function ($_, $headers) {
            $this->responseHeaders .= $headers;
            return strlen($headers);
        });

        curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false);

        // Get response data.
        $body    = curl_exec($this->ch);
        $code    = curl_getinfo($this->ch, CURLINFO_HTTP_CODE);
        $headers = $this->getResponseHeaders();

        // Register response.
        $this->response = new HttpResponse($code, $headers, $body);

        return $this->getResponse();
    }

    protected function setDefaultCurlSettings()
    {
        $verifySsl       = $this->options->verifySsl();
        $timeout         = $this->options->getTimeout();
        $followRedirects = $this->options->getFollowRedirects();

        curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, $verifySsl);
        if (!$verifySsl) {
            curl_setopt($this->ch, CURLOPT_SSL_VERIFYHOST, $verifySsl);
        }
        if ($followRedirects) {
            curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, true);
        }
        curl_setopt($this->ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($this->ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->ch, CURLOPT_HTTPHEADER, $this->request->getRawHeaders());
        curl_setopt($this->ch, CURLOPT_URL, $this->request->getUrl());

        foreach ($this->customCurlOptions as $customCurlOptionKey => $customCurlOptionValue) {
            curl_setopt($this->ch, $customCurlOptionKey, $customCurlOptionValue);
        }
    }

    protected function lookForErrors($parsedResponse)
    {
        // Any non-200/201/202 response code indicates an error.
        if (!in_array($this->response->getCode(), ['200', '201', '202'])) {
            $errors = $parsedResponse->errors ?? $parsedResponse;
            $errorMessage = '';
            $errorCode = '';

            if (is_array($errors)) {
                $errorMessage = $errors[0]->message;
                $errorCode    = $errors[0]->code;
            } elseif (isset($errors->message, $errors->code)) {
                $errorMessage = $errors->message;
                $errorCode    = $errors->code;
            }

            throw new HttpClientException(
                sprintf('Error: %s [%s]', $errorMessage, $errorCode),
                $this->response->getCode(),
                $this->request,
                $this->response
            );
        }
    }

    protected function processResponse()
    {
        $body = $this->response->getBody();
        // Look for UTF-8 BOM and remove.
        if (str_starts_with(bin2hex(substr($body, 0, 4)), 'efbbbf')) {
            $body = substr($body, 3);
        }

        $parsedResponse = json_decode($body);

        // Test if return a valid JSON.
        if (JSON_ERROR_NONE !== json_last_error()) {
            $message = function_exists('json_last_error_msg') ? json_last_error_msg() : 'Invalid JSON returned';
            throw new HttpClientException(
                sprintf('JSON ERROR: %s', $message),
                $this->response->getCode(),
                $this->request,
                $this->response
            );
        }

        $this->lookForErrors($parsedResponse);

        return $parsedResponse;
    }

    public function request($endpoint, $method, $data = [], $parameters = [])
    {
        // Initialize cURL.
        $this->ch = curl_init();
        // Set request args.
        $request = $this->createRequest($endpoint, $method, $data, $parameters);
        // Default cURL settings.
        $this->setDefaultCurlSettings();
        // Get response.
        $response = $this->createResponse();
        // Check for cURL errors.
        if (curl_errno($this->ch)) {
            throw new HttpClientException('cURL Error: ' . \curl_error($this->ch), 0, $request, $response);
        }
        curl_close($this->ch);
        return $this->processResponse();
    }

    public function getRequest()
    {
        return $this->request;
    }

    public function getResponse()
    {
        return $this->response;
    }

    public function setCustomCurlOptions(array $curlOptions)
    {
        $this->customCurlOptions = $curlOptions;
    }
}

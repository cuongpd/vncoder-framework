<?php

namespace VnCoder\Helper\WooCommerce;

class OAuth
{

    public const HASH_ALGORITHM = 'SHA256';
    protected string $url;
    protected string $consumerKey;
    protected string $consumerSecret;
    protected string $apiVersion;
    protected string $method;
    protected $parameters;
    protected $timestamp;

    public function __construct( $url, $consumerKey, $consumerSecret, $apiVersion, $method, $parameters = [], $timestamp = '') {
        $this->url            = $url;
        $this->consumerKey    = $consumerKey;
        $this->consumerSecret = $consumerSecret;
        $this->apiVersion     = $apiVersion;
        $this->method         = $method;
        $this->parameters     = $parameters;
        $this->timestamp      = $timestamp;
    }

    protected function encode($value)
    {
        if (is_array($value)) {
            return array_map([$this, 'encode'], $value);
        } else {
            return str_replace(['+', '%7E'], [' ', '~'], rawurlencode($value));
        }
    }

    protected function normalizeParameters($parameters)
    {
        $normalized = [];

        foreach ($parameters as $key => $value) {
            // Percent symbols (%) must be double-encoded.
            $key   = $this->encode($key);
            $value = $this->encode($value);

            $normalized[$key] = $value;
        }

        return $normalized;
    }

    protected function processFilters($parameters)
    {
        if (isset($parameters['filter'])) {
            $filters = $parameters['filter'];
            unset($parameters['filter']);
            foreach ($filters as $filter => $value) {
                $parameters['filter[' . $filter . ']'] = $value;
            }
        }

        return $parameters;
    }

    protected function getSecret()
    {
        $secret = $this->consumerSecret;
        // Fix secret for v3 or later.
        if (!\in_array($this->apiVersion, ['v1', 'v2'])) {
            $secret .= '&';
        }

        return $secret;
    }

    protected function generateOauthSignature($parameters)
    {
        $baseRequestUri = rawurlencode($this->url);
        // Extract filters.
        $parameters = $this->processFilters($parameters);
        // Normalize parameter key/values and sort them.
        $parameters = $this->normalizeParameters($parameters);
        $parameters = $this->getSortedParameters($parameters);
        // Set query string.
        $queryString  = implode('%26', $this->joinWithEqualsSign($parameters)); // Join with ampersand.
        $stringToSign = $this->method . '&' . $baseRequestUri . '&' . $queryString;
        $secret       = $this->getSecret();
        return base64_encode(hash_hmac(self::HASH_ALGORITHM, $stringToSign, $secret, true));
    }

    protected function joinWithEqualsSign($params, $queryParams = [], $key = '')
    {
        foreach ($params as $paramKey => $paramValue) {
            if ($key) {
                $paramKey = $key . '%5B' . $paramKey . '%5D'; // Handle multi-dimensional array.
            }
            if (is_array($paramValue)) {
                $queryParams = $this->joinWithEqualsSign($paramValue, $queryParams, $paramKey);
            } else {
                $string = $paramKey . '=' . $paramValue; // Join with equals sign.
                $queryParams[] = $this->encode($string);
            }
        }

        return $queryParams;
    }

    protected function getSortedParameters($parameters)
    {
        uksort($parameters, 'strcmp');
        foreach ($parameters as $key => $value) {
            if (is_array($value)) {
                uksort($parameters[$key], 'strcmp');
            }
        }

        return $parameters;
    }

    public function getParameters()
    {
        $parameters = array_merge($this->parameters, [
            'oauth_consumer_key'     => $this->consumerKey,
            'oauth_timestamp'        => $this->timestamp,
            'oauth_nonce'            => \sha1(\microtime()),
            'oauth_signature_method' => 'HMAC-' . self::HASH_ALGORITHM,
        ]);
        $parameters['oauth_signature'] = $this->generateOauthSignature($parameters);
        return $this->getSortedParameters($parameters);
    }
}

<?php

namespace VnCoder\Helper\WooCommerce;

use VnCoder\Helper\WooCommerce\HttpClient;

class WooCommerce
{
    public const VERSION = '3.1.0';
    public HttpClient $http;

    public function __construct($url, $consumerKey, $consumerSecret, $options = [])
    {
        $this->http = new HttpClient($url, $consumerKey, $consumerSecret, $options);
    }

    public function post($endpoint, $data)
    {
        return $this->http->request($endpoint, 'POST', $data);
    }

    public function put($endpoint, $data)
    {
        return $this->http->request($endpoint, 'PUT', $data);
    }

    public function get($endpoint, $parameters = [])
    {
        return $this->http->request($endpoint, 'GET', [], $parameters);
    }

    public function delete($endpoint, $parameters = [])
    {
        return $this->http->request($endpoint, 'DELETE', [], $parameters);
    }

    public function options($endpoint)
    {
        return $this->http->request($endpoint, 'OPTIONS', [], []);
    }

}

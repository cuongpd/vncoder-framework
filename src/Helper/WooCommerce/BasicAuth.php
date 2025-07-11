<?php

namespace VnCoder\Helper\WooCommerce;

class BasicAuth
{

    protected $ch;
    protected string $consumerKey;
    protected string $consumerSecret;
    protected $doQueryString;
    protected $parameters;

    public function __construct($ch, $consumerKey, $consumerSecret, $doQueryString, $parameters = [])
    {
        $this->ch             = $ch;
        $this->consumerKey    = $consumerKey;
        $this->consumerSecret = $consumerSecret;
        $this->doQueryString  = $doQueryString;
        $this->parameters     = $parameters;

        $this->processAuth();
    }

    protected function processAuth()
    {
        if ($this->doQueryString) {
            $this->parameters['consumer_key']    = $this->consumerKey;
            $this->parameters['consumer_secret'] = $this->consumerSecret;
        } else {
            curl_setopt($this->ch, CURLOPT_USERPWD, $this->consumerKey . ':' . $this->consumerSecret);
        }
    }

    public function getParameters()
    {
        return $this->parameters;
    }
}

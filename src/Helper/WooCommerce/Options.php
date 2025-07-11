<?php

namespace VnCoder\Helper\WooCommerce;

class Options
{

    public const VERSION = 'wc/v3';
    public const TIMEOUT = 15;
    public const WP_API_PREFIX = '/wp-json/';
    public const USER_AGENT = 'WooCommerce API Client-PHP';

    private array $options;

    public function __construct($options)
    {
        $this->options = $options;
    }

    public function getVersion()
    {
        return $this->options['version'] ?? self::VERSION;
    }

    public function verifySsl()
    {
        return !isset($this->options['verify_ssl']) || $this->options['verify_ssl'];
    }

    public function isOAuthOnly()
    {
        return isset($this->options['oauth_only']) && $this->options['oauth_only'];
    }

    public function getTimeout()
    {
        return isset($this->options['timeout']) ? (int) $this->options['timeout'] : self::TIMEOUT;
    }

    public function isQueryStringAuth()
    {
        return isset($this->options['query_string_auth']) && $this->options['query_string_auth'];
    }

    public function isWPAPI()
    {
        return !isset($this->options['wp_api']) || $this->options['wp_api'];
    }

    public function apiPrefix()
    {
        return $this->options['wp_api_prefix'] ?? self::WP_API_PREFIX;
    }

    public function oauthTimestamp()
    {
        return $this->options['oauth_timestamp'] ?? time();
    }

    public function userAgent()
    {
        return $this->options['user_agent'] ?? self::USER_AGENT;
    }

    public function getFollowRedirects()
    {
        return isset($this->options['follow_redirects']) && $this->options['follow_redirects'];
    }

    public function isMethodOverrideQuery()
    {
        return isset($this->options['method_override_query']) && $this->options['method_override_query'];
    }

    public function isMethodOverrideHeader()
    {
        return isset($this->options['method_override_header']) && $this->options['method_override_header'];
    }
}

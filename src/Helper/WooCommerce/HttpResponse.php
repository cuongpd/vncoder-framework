<?php

namespace VnCoder\Helper\WooCommerce;

class HttpResponse
{
    private $code;
    private $headers;
    private $body;

    public function __construct($code = 0, $headers = [], $body = '')
    {
        $this->code    = $code;
        $this->headers = $headers;
        $this->body    = $body;
    }

    public function __toString()
    {
        return \json_encode([
            'code'    => $this->code,
            'headers' => $this->headers,
            'body'    => $this->body,
        ]);
    }

    public function setCode($code)
    {
        $this->code = (int) $code;
    }

    public function setHeaders($headers)
    {
        $this->headers = $headers;
    }

    public function setBody($body)
    {
        $this->body = $body;
    }

    public function getCode()
    {
        return $this->code;
    }

    public function getHeaders()
    {
        return $this->headers;
    }

    public function getBody()
    {
        return $this->body;
    }
}

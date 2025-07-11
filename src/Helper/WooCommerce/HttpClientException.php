<?php

namespace VnCoder\Helper\WooCommerce;

use VnCoder\Helper\WooCommerce\HttpRequest;
use VnCoder\Helper\WooCommerce\HttpResponse;

class HttpClientException extends \Exception
{

    private $request;
    private $response;

    public function __construct($message, $code, HttpRequest $request, HttpResponse $response){
        parent::__construct($message, $code);
        $this->request  = $request;
        $this->response = $response;
    }

    public function getRequest()
    {
        return $this->request;
    }

    public function getResponse()
    {
        return $this->response;
    }
}

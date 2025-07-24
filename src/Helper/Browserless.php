<?php

namespace VnCoder\Helper;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class Browserless
{
    public static function getContent($url){
        $client = new Client();
        try {
            $response = $client->post(env('BROWSERLESS_BASE_URL', '') . '/chromium/content', [
                'query' => [
                    'token' => env('BROWSERLESS_API_TOKEN')
                ],
                'json' => [
                    'url' => $url,
                    'waitForTimeout' => 3000,
                    'setJavaScriptEnabled' => true,
                    'rejectResourceTypes' => ['image', 'font'],
                    'userAgent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36',
                ]
            ]);
            return $response->getBody()->getContents();
        } catch (RequestException $e) {
            logError('Browserless request failed: ' . $e->getMessage(), [
                'url' => $url,
                'response' => optional($e->getResponse())->getBody()?->getContents()
            ]);
            return '';
        } catch (Exception $e) {
            logError('General error during browserless call: ' . $e->getMessage(), [
                'url' => $url
            ]);
            return '';
        }
    }

}
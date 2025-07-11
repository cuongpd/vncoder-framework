<?php

namespace VnCoder\Helper;

class BingTranslator {

    public static function translate($text, $to = "en"){
        $bing_translator_api_key = env('BING_TRANSLATOR_API_KEY', "77361d6bb49348069b66bb1d02d23431");
        $bing_translator_region = env('BING_TRANSLATOR_REGION', "southeastasia");
        $bing_translator_endpoint = env('BING_TRANSLATOR_ENDPOINT', "https://gocit-trans.cognitiveservices.azure.com/translator/text/v3.0/translate");

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $bing_translator_endpoint . '?to=' . $to,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode([['Text' => $text]]),
            CURLOPT_HTTPHEADER => array(
                'Ocp-Apim-Subscription-Key: ' . $bing_translator_api_key,
                'Ocp-Apim-Subscription-Region: ' . $bing_translator_region,
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        $data = json_decode($response, true);
        return $data[0]['translations'][0]['text'] ?? $text;
    }


}



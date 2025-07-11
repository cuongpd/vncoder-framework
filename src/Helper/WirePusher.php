<?php

namespace VnCoder\Helper;

class WirePusher
{
    static function send($title, $message, $image_url = '', $action = '', $type = 'VIP' ){
        $pusherDeviceId = getConfig('wire_pusher_device', 'wL8tmpGwC', 'Pusher Device Token');
        $parameters = [
            'id'      => $pusherDeviceId,
            'title'   => $title,
            'message' => $message
        ];

        if($image_url){
            $parameters['image_url'] = $image_url;
        }
        if($action){
            $parameters['action'] = $action;
        }
        if($type){
            $parameters['type'] = $type;
        }

        $ch = curl_init("https://wirepusher.com/send");
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $parameters);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);

        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
    }

}

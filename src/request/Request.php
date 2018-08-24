<?php
/**
 * Created by PhpStorm.
 * User: garming
 * Date: 04/06/2017
 * Time: 15:15
 */

namespace NxLib\Request;


class Request
{

    public static function redirect($url,$refreshTime = 1)
    {
        if(headers_sent()){
            header("Refresh: {$refreshTime}; url={$url}");
            return;
        }
        header("Location: {$url}");
        return;
    }
}
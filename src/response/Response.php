<?php
/**
 * Created by PhpStorm.
 * User: garming
 * Date: 04/06/2017
 * Time: 15:16
 */

namespace NxLib\Response;


class Response
{
    public static function sendJsonResponse($data, $status_code = 200)
    {
        http_response_code($status_code);
        header('Content-type: application/json; charset=utf-8');
        echo json_encode($data);
        exit;
    }
}
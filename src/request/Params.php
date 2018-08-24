<?php
/**
 * Created by PhpStorm.
 * User: garming
 * Date: 04/06/2017
 * Time: 15:16
 */

namespace NxLib\Request;


class Params
{
    private static $noParamMsg = 'Param do not exist';
    
    private static $allParams;
    private static $rawParams;

    public static function getParam($key, $default = '', $msg = '')
    {
        if (isset($_GET[$key])) {
            return static::typeResult($_GET[$key],$default);
        }
        static::throwPramsNotExist($msg);
        return $default;
    }

    public static function postParam($key, $default = '', $msg = '')
    {
        if (isset($_POST[$key])) {
            return static::typeResult($_POST[$key],$default);
        }
        return static::putParam($key,$default,$msg);
        static::throwPramsNotExist($msg);
        return $default;
    }

    public static function putParam($key, $default = '', $msg = '')
    {
        static::$rawParams = ParamsInputData::init();
        if(isset(static::$rawParams[$key])){
            return static::typeResult(static::$rawParams[$key],$default);
        }
        static::throwPramsNotExist($msg);
        return $default;
    }

    public static function deleteParam($key, $default = '', $msg = '')
    {
        return static::putParam($key,$default,$msg);
    }

    public static function params()
    {
        if (!is_null(static::$allParams)) {
            return static::$allParams;
        }
        switch ($_SERVER['REQUEST_METHOD']){
            case 'GET':
                return $_GET;
            default:
                static::initRawParams();
                $rawParams = is_array(static::$rawParams) ?? [];
                return array_merge($_GET,$_POST,$rawParams);
        }
    }

    public static function param($key, $default = '', $msg = '')
    {
        $params = static::params();
        if (isset($params[$key])) {
            return $params[$key];
        }
        static::throwPramsNotExist($msg);
        return $default;
    }

    public static function header($key)
    {
        $key = 'http_' . $key;
        $key = str_replace('-', '_', $key);
        $key = strtoupper($key);
        return isset($_SERVER[$key]) ? $_SERVER[$key] : null;
    }

    private static function throwPramsNotExist($msg)
    {
        if ($msg === true) {
            throw new ParamException(static::$noParamMsg);
        }
        if (!empty($msg)) {
            throw new ParamException($msg);
        }
    }

    private static function typeResult($value, $var)
    {
        switch (gettype($var)) {
            case 'boolean':
                return boolval($value);
            case 'integer':
                return intval($value);
            case 'double':
                return doubleval($value);
            case 'string':
                return strval($value);
            default:
                return $value;
        }
    }

    private static function initRawParams(){
        if(!is_null(static::rawParams)){
            return;
        }
        static::$rawParams = ParamsInputData::init();
    }
}
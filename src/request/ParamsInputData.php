<?php
/**
 * Created by PhpStorm.
 * User: garming
 * Date: 04/06/2017
 * Time: 16:02
 */

namespace NxLib\Request;


class ParamsInputData
{
    private static $rawParams;

    public static function init(){
        if(!is_null(static::$rawParams)){
            return static::$rawParams;
        }
        $raw_data = file_get_contents('php://input', 'r');
        if(empty($raw_data)){
            return;
        }
        if(isset($_SERVER['HTTP_CONTENT_TYPE'])){
            if(strpos($_SERVER['HTTP_CONTENT_TYPE'],'multipart/form-data;') === 0){
                //content-type=multipart/form-data;
                static::$rawParams = static::raw_multipart_form_data_handler($raw_data);
            }
            if(strpos($_SERVER['HTTP_CONTENT_TYPE'],'application/json;') === 0){
                //content-type=application/json;
                static::$rawParams = json_decode($raw_data,1);
            }
        }else{
            //other content-type
            parse_str($raw_data,static::$rawParams);
        }
        return static::$rawParams;
    }
    private static function raw_multipart_form_data_handler($raw_data)
    {
        $handler_data = [];
        $boundary = substr($raw_data, 0, strpos($raw_data, "\r\n"));
        $parts = array_slice(explode($boundary, $raw_data), 1);
        foreach ($parts as $part) {
            // If this is the last part, break
            if ($part == "--\r\n") break;

            // Separate content from headers
            $part = ltrim($part, "\r\n");
            list($raw_headers, $body) = explode("\r\n\r\n", $part, 2);

            // Parse the headers list
            $raw_headers = explode("\r\n", $raw_headers);
            $headers = array();
            foreach ($raw_headers as $header) {
                list($name, $value) = explode(':', $header);
                $headers[strtolower($name)] = ltrim($value, ' ');
            }

            // Parse the Content-Disposition to get the field name, etc.
            if (isset($headers['content-disposition'])) {
                $filename = null;
                preg_match(
                    '/^(.+); *name="([^"]+)"(; *filename="([^"]+)")?/',
                    $headers['content-disposition'],
                    $matches
                );
                list(, $type, $name) = $matches;
                isset($matches[4]) and $filename = $matches[4];

                // handle your fields here
                switch ($name) {
                    // this is a file upload
                    case 'userfile':
                        file_put_contents($filename, $body);
                        break;

                    // default for all other files is to populate $data
                    default:
                        $handler_data[$name] = substr($body, 0, strlen($body) - 2);
                        break;
                }
            }
        }
        return $handler_data;
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: pablo.juarez
 * Date: 4/29/2019
 * Time: 1:37 PM
 */

namespace App\Http;


class Request
{
    public static function queryParams() {
        return implode('&', $_SERVER['QUERY_STRING']);
    }

    /**
     * Parse input as json, any other format will break :C
     *
     * @param bool $asArray
     * @return bool|string
     */
    public static function input($asArray = false) {
        if ($asArray) {
            return json_decode(file_get_contents("php://input"), true);
        }
        return json_decode(file_get_contents("php://input"));
    }

}
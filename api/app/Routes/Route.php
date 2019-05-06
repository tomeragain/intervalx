<?php
/**
 * Created by PhpStorm.
 * User: pablo.juarez
 * Date: 4/29/2019
 * Time: 10:32 AM
 */

namespace App\Routes;


class Route
{
    /**
     * @param $path
     * @param $action
     * @return bool
     */
    public static function dispatch($path, $action)
    {
        $server = $_SERVER;
        $rawPath = explode('index.php/api/', $server['PHP_SELF']);
        if ($rawPath[1] == $path) {
            $action();
            die();
        }
        return false;
    }
}
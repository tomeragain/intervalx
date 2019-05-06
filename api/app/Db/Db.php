<?php
/**
 * Created by PhpStorm.
 * User: pablo.juarez
 * Date: 4/29/2019
 * Time: 2:15 PM
 */

namespace App\Db;


use mysqli;

class Db
{
    protected static $instance;
    public static function instance() {
        if (!isset(self::$instance)) {
            return new mysqli(Config::$host, Config::$username, Config::$password, Config::$dbName);
        }
        return self::$instance;
    }

}
<?php
/**
 * Created by PhpStorm.
 * User: pablo.juarez
 * Date: 4/29/2019
 * Time: 2:28 PM
 */

namespace App\Model;


use App\Db\Db;
use Exception;
use ReflectionClass;

class Model
{
    public function getTableName() {
        try {
            $table = (new ReflectionClass($this))->getShortName();
            $table = strtolower($table);
            return $table;
        } catch (Exception $exception) {
            return false;
        }
    }

    public function sanitize() {
        $vars = get_object_vars($this);
        $db = Db::instance();
        foreach ($vars as $key => $var) {
            $this->$key = $db->escape_string($var);
        }
    }

    public function sanitizeVars($data) {
        $db = Db::instance();
        return array_map(function ($val) use ($db) {
            return $db->escape_string($val);
        }, $data);
    }

}
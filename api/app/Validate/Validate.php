<?php


namespace App\Validate;

use Exception;

class Validate
{
    /**
     * @param $data
     * @return bool
     * @throws Exception
     */
    static function hasData($data) {
        if (empty($data)) {
            throw new Exception('Will not attended empty requests');
        }
        if (!isset($data['data'])) {
            throw new Exception('Request has not the required format');
        }
        return true;
    }

    /**
     * @param $keys
     * @param $data
     * @return bool
     * @throws Exception
     */
    static function hasKeys($keys, $data) {
        foreach ($keys as $key => $keyValue ) {
            if (!isset($data[$keyValue])) {
                throw new Exception('Expected param: ' . $keyValue . ', missing value at request');
            }
        }
        return true;
    }

    /**
     * @param $input
     * @param bool $biggerThanZero
     * @param string $tagValue
     * @return bool
     * @throws Exception
     */
    static function numeric($input, $biggerThanZero = false, $tagValue = '') {
        if (!is_numeric($input)) {
            throw new Exception("The param request $tagValue must be a numeric value");
        }
        if ($biggerThanZero) {
            if ($input <= 0) {
                throw new Exception("the param request $tagValue must be bigger than zero");
            }
        }
        return true;
    }

    static function date($input, $tagValue) {
        if (!strtotime($input)) {
            throw new Exception("the param request $tagValue must be valid date");
        }
    }

}
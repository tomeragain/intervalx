<?php
/**
 * Created by PhpStorm.
 * User: pablo.juarez
 * Date: 4/29/2019
 * Time: 1:43 PM
 */

namespace App\Http;


class Response
{
    public static function json($response) {
        header('Content-type: application/json');
        echo json_encode($response);
    }

}
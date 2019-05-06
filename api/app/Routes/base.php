<?php
/**
 * Created by PhpStorm.
 * User: pablo.juarez
 * Date: 4/29/2019
 * Time: 10:17 AM
 */

use App\Routes\Route;
use App\Http\Request;
use App\Http\Response;
use App\Validate\RentalValidate;
use App\Controllers\RentalController;

Route::dispatch('get', function () {
    $response = RentalController::index();
    Response::json($response);
});

Route::dispatch('add', function () {
    $input = Request::input();
    try {
        RentalValidate::hasData(Request::input(true));
        RentalValidate::interval(Request::input(true));
        $response = RentalController::store($input);
        Response::json($response);
    } catch (Exception $exception) {
        Response::json(['errors' => true, 'errorMessage' => $exception->getMessage()]);
    }
});

Route::dispatch('flush', function () {
    $response = RentalController::flush();
    Response::json($response);
});

Route::dispatch('edit', function () {
    echo json_encode(['result' => true], JSON_PRETTY_PRINT);
});


Route::dispatch('delete', function () {
    $input = Request::input();
    try {
        RentalValidate::hasData(Request::input(true));
        $response = RentalController::delete($input);
        Response::json($response);
    } catch (Exception  $exception) {
        Response::json(['errors' => true, 'errorMessage' => $exception->getMessage()]);
    }
});
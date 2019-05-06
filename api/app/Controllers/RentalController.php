<?php
/**
 * Created by PhpStorm.
 * User: pablo.juarez
 * Date: 4/29/2019
 * Time: 10:34 AM
 */

namespace App\Controllers;

use App\Model\Rental;
use App\Responses\RentalResponse;
use Exception;
use mysqli_result;
use DateTime;
use App\Helpers\Interval;

class RentalController
{

    /**
     * retrieve all rental intervals
     *
     * @return bool|mysqli_result
     */
    public static function index() {
        $rental = new Rental;
        return $rental->getAll();
    }

    /**
     * Set new rental interval
     *
     * @param $input
     * @return array
     * @throws Exception
     */
    public static function store($input) {
        $dateStart = Interval::getInputDate($input->data->start);
        $dateEnd = Interval::getInputDate($input->data->end);
        $price = $input->data->price;

        $rental = new Rental();
        $rental->price = $price;
        $rental->start_date = $dateStart;
        $rental->end_date = $dateEnd;


        /**
         * if input is not correct interval
         */
        $dateStartT = new DateTime($dateStart);
        $dateEndT = new DateTime($dateEnd);
        if ($dateStartT > $dateEndT) {
            return RentalResponse::invalidInterval();
        }
        /**
         * Avoid duplicated intervals
         */
        if (!empty($rental->exists())) {
            return RentalResponse::alreadyExists();
        }

        /**
         * if same interval exists but with different price, overwrite with new price
         *
         */
        if (!empty($intervalExists = $rental->intervalExists())) {
            $rental->update($intervalExists['id'], $rental->price, $rental->start_date, $rental->end_date);
            return RentalResponse::Ok(__LINE__);
        }

        $brakeInterval = $rental->brakeInterval();

        /**
         * check for merge above intervals
         * Price exists, but interval collides with another price interval
         */
        $priceExists = $rental->priceExists();
        if (!empty($priceExists) && !empty($brakeInterval)) {
            # check if same price intervals can be merged
            $contiguousInterval = Interval::priceOverlap($rental, $priceExists);
            if (!empty($contiguousInterval)) {
                $intervalAbove = Interval::intervalAbove($rental, $contiguousInterval, $brakeInterval);
                if ($intervalAbove) {
                    return RentalResponse::Ok(__LINE__);
                }
                return  RentalResponse::basicFailure(__LINE__);
            }
        }

        /**
         * Price does not exists but interfere with existing intervals
         * Check for brake interval for non existing prices
         */
        if (!empty($brakeInterval)) {
            $resultBrakeInterval = Interval::brakeInterval($rental, $brakeInterval);
            if ($resultBrakeInterval) {
                return RentalResponse::Ok(__LINE__);
            }
            return RentalResponse::basicFailure(__LINE__);
        }

        /**
         * Check if there are intervals with the same price and could be merged
         */

        $intervalsToMerge = $rental->checkForMerge();
        if (!empty($intervalsToMerge)) {
            # proceed to update interval fetched with their new values
            $simpleMerge = Interval::simpleMerge($rental, $intervalsToMerge);
            if ($simpleMerge) {
                return RentalResponse::Ok(__LINE__);
            }
            return RentalResponse::basicFailure(__LINE__);
        }

        /**
         * no overlaps, then just save the new entry :)
         */
        if ($rental->save()) {
            return RentalResponse::Ok(__LINE__);
        }
        return RentalResponse::basicFailure();
    }

    /**
     * edit rental interval
     */
    public static function edit() {

    }

    /**
     * delete given rental interval
     * @param $input
     * @return array
     */
    public static function  delete($input) {
        $intervalId = $input->data->id;
        $rental = new Rental();
        $rental->deleteById($intervalId);
        return RentalResponse::OkDelete(__LINE__);
    }

    public static function flush() {
        $rental = new Rental();
        $rental->flush();
        return RentalResponse::OkFlush(__LINE__);
    }
}
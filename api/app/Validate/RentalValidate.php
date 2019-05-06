<?php


namespace App\Validate;

use Exception;

/**
 * Validate incoming requests format and content to interact with Rental model
 *
 * Class RentalValidate
 * @package App\Validate
 */
class RentalValidate extends Validate
{

    /**
     * @param $data
     * @return bool
     * @throws Exception
     */
    static function interval($data) {
        $mustHave = ['start', 'end', 'price'];
        self::hasKeys($mustHave, $data['data']);
        foreach ($data['data'] as $key => $value) {
            # all data must be numeric and bigger than zero
            self::numeric($value, true, $key);
        }
        $startDate = self::__assembleDate($data['data']['start']);
        $endDate = self::__assembleDate($data['data']['end']);
        self::date($startDate, 'start_date');
        self::date($endDate, 'end_date');

        return true;
    }

    static function __assembleDate($dateValue) {
        return date('Y-m') . '-' . $dateValue;
    }

}
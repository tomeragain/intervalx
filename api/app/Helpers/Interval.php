<?php


namespace App\Helpers;


use App\Model\Rental;
use DateInterval;
use DatePeriod;
use DateTime;
use Exception;


/**
 * Class Interval
 * Helper methods to handle Rental intervals storage
 *
 * @package App\Helpers
 */
class Interval
{

    /**
     * check if same price intervals can be merged
     *
     * @param Rental $rental
     * @param array $priceExists
     * @return array
     */
    static function priceOverlap(Rental $rental, $priceExists) {
        $contiguousInterval = [];
        $periodStart = (int) date('d', strtotime($rental->start_date));
        $periodEnd = (int) date('d', strtotime($rental->end_date));
        foreach ($priceExists as $key => $valueExists ) {
            $existsStart = (int) date('d', strtotime($valueExists['start_date']));
            $existsEnd = (int) date('d', strtotime($valueExists['end_date']));
            $contiguousBelow = ($periodStart - $existsEnd);
            $contiguousAbove = ($existsStart - $periodEnd);
            if ($contiguousBelow == 1 || $contiguousBelow == 0) {
                array_push($contiguousInterval, $valueExists);
            }
            if ($contiguousAbove == 1 || $contiguousAbove == 0) {
                array_push($contiguousInterval, $valueExists);
            }
        }
        return $contiguousInterval;
    }

    /**
     * @param Rental $rental
     * @param array $samePriceInterval
     * @param array $intersectInterval
     * @return bool
     * @throws Exception
     */
    static function intervalAbove(Rental $rental, $samePriceInterval, $intersectInterval) {
        # delete affected intervals
        foreach ($intersectInterval as $key => $range) {
            $rental->deleteById($intersectInterval[$key]['id']);
        }
        foreach ($samePriceInterval as $key => $range) {
            $rental->deleteById($samePriceInterval[$key]['id']);
        }

        $newStart = new DateTime($rental->start_date);
        $newEnd = new DateTime($rental->end_date);
        $newPrice = $rental->price;
        $priceStart = new DateTime($samePriceInterval[0]['start_date']);
        $endKey = (count($samePriceInterval) > 1) ? (count($samePriceInterval) - 1) : 0;
        $priceEnd = new DateTime($samePriceInterval[$endKey]['end_date']);

        $limitStart = ($newStart < $priceStart ) ? $newStart : $priceStart;
        $limitEnd = ($newEnd > $priceEnd ) ? $newEnd : $priceEnd;
        $newPeriod = new DatePeriod($limitStart, new DateInterval('P7D'), $limitEnd);

        # insert merge interval with the same price
        $rental->price = $newPrice;
        $rental->start_date = $newPeriod->getStartDate()->format('Y-m-d');
        $rental->end_date = $newPeriod->getEndDate()->format('Y-m-d');
        $rental->save();

        # insert remaining interval
        $endKey = (count($intersectInterval) > 1) ? (count($intersectInterval) - 1) : 0;
        $add = $limitEnd->add(new DateInterval('P1D'));
        $oldEnd = new DateTime($intersectInterval[$endKey]['end_date']);
        $remainingPeriod = new DatePeriod($add, new DateInterval('P7D'), $oldEnd);
        $rental->price = $intersectInterval[0]['price'];
        $rental->start_date = $remainingPeriod->getStartDate()->format('Y-m-d');
        $rental->end_date = $remainingPeriod->getEndDate()->format('Y-m-d');
        if ($rental->end_date > $rental->start_date) {
            # if interval isn't valid then skip insert
            $rental->save();
        }
        return true;

    }

    /**
     * @param Rental $rental
     * @param array $rangeData
     * @return bool
     * @throws Exception
     */
    static function brakeInterval(Rental $rental, $rangeData) {
        $endKey = (count($rangeData) > 1) ? (count($rangeData) - 1) : 0;
        $oldPrice = $rangeData[0]['price'];
        $oldStart = new DateTime($rangeData[0]['start_date']);
        $oldEnd = new DateTime($rangeData[$endKey]['end_date']);

        $newStart = new DateTime($rental->start_date);
        $newEnd = new DateTime($rental->end_date);
        $newPrice = $rental->price;

        if ($newStart < $oldStart) {
            # delete old periods
            foreach ($rangeData as $key => $range) {
                $rental->deleteById($rangeData[$key]['id']);
            }

            # insert new interval
            $rental->price = $newPrice;
            $rental->start_date = $newStart;
            $rental->end_date = $newEnd;
            $rental->save();

            # get residue and set remaining interval
            $oldPeriod = new DatePeriod($newEnd, new DateInterval('P7D'), $oldEnd);
            $add = new DateTime($oldPeriod->getStartDate()->format('Y-m-d'));
            $rental->price = $oldPrice;
            $rental->start_date = $add->add(new DateInterval('P1D'))->format('Y-m-d');
            $rental->end_date = $oldPeriod->getEndDate();
            $rental->save();

            return true;

        } else {
            # delete remaining periods
            foreach ($rangeData as $key => $range) {
                $rental->deleteById($rangeData[$key]['id']);
            }
            # insert first part from old interval
            $oldPeriod = new DatePeriod($oldStart, new DateInterval('P7D'), $newStart);
            $minus = new DateTime($oldPeriod->getEndDate()->format('Y-m-d'));
            $rental->price = $oldPrice;
            $rental->start_date = $oldPeriod->getStartDate()->format('Y-m-d');
            $rental->end_date = $minus->sub(new DateInterval('P1D'))->format('Y-m-d');
            if ($rental->end_date >= $rental->start_date) {
                # if interval isn't valid then skip insert
                $rental->save();
            }

            # insert new interval
            $rental->price = $newPrice;
            $rental->start_date = $newStart->format('Y-m-d');
            $rental->end_date = $newEnd->format('Y-m-d');
            $rental->save();

            # insert remaining from old interval, only if date intervals are valid
            $remainingPeriod = new DatePeriod($newEnd, new DateInterval('P7D'), $oldEnd);
            $add = new DateTime($remainingPeriod->getStartDate()->format('Y-m-d'));
            $rental->price = $oldPrice;
            $rental->start_date = $add->add(new DateInterval('P1D'))->format('Y-m-d');
            $rental->end_date = $remainingPeriod->getEndDate()->format('Y-m-d');
            if ($rental->end_date >= $rental->start_date) {
                # if interval isn't valid then skip insert
                $rental->save();
            }

            return true;
        }
    }

    /**
     * @param Rental $rental
     * @param $intervalsToMerge
     * @return bool
     * @throws Exception
     */
    static function simpleMerge(Rental $rental, $intervalsToMerge) {
        # proceed to update interval fetched with their new values
        $oldStart = new DateTime($intervalsToMerge['start_date']);
        $newStart = new DateTime($rental->start_date);
        if ($newStart < $oldStart) {
            $update = $rental->update($intervalsToMerge['id'], $intervalsToMerge['price'], $rental->start_date, $rental->end_date);
        } else {
            $update = $rental->update($intervalsToMerge['id'], $intervalsToMerge['price'], $intervalsToMerge['start_date'], $rental->end_date);
        }
        if ($update) {
            return true;
        }
        return false;
    }


    /**
     * @param $dateValue
     * @return false|string
     */
    public static function getInputDate($dateValue) {
        $uglyDate = date('Y-m') . '-' . $dateValue;
        return date('Y-m-d', strtotime($uglyDate));
    }

}
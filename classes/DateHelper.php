<?php

/**
 * Class to help out with common mysql tasks
 */
class DateHelper
{

    static function isWeekend($date)
    {
        return (date('N', strtotime($date)) >= 6);
    }

    function validateDate($date, $format = 'Y-m-d')
    {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }

}

?>
<?php

/**
 * Class to help out with common mysql tasks
 */
class DateHelper
{

    function __construct()
    {
    }


    static function isWeekend($date)
    {
        return (date('N', strtotime($date)) >= 6);
//        $date = strtotime($date);
//        $date = date("l", $date);
//        $date = strtolower($date);
//
//        if ($date == "saturday" || $date == "sunday") {
//            return true;
//        } else {
//            return "false";
//        }
    }


}

?>
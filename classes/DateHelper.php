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


}

?>
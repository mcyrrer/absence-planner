<?php


/**
 * Class to help out with common mysql tasks
 */
class DbHelper
{
    private $logger;

    function __construct()
    {
    }

    public function connectToMainDb()
    {
        $con = mysqli_connect(DB_HOST, DB_USER, DB_PASS,DB_NAME) or die("cannot connect");
        return $con;
    }


    static function escape($con,$toEscape)
    {
        return mysqli_real_escape_string($con,$toEscape);
    }

//    static function sw_mysqli_fetch_all($mysqli_result) {
//        $resultAsArray = array();
//        while ($row = mysqli_fetch_assoc($mysqli_result)) {
//            $resultAsArray[]=$row;
//        }
//        return $resultAsArray;
//    }



}

?>
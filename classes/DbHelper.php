<?php


/**
 * Class to help out with common mysql tasks
 */
class DbHelper
{
    private $logger;

    function __construct()
    {
        $l = new Logging();
        $this->logger = $l->getLogger();
    }

    public function connectToMainDb()
    {
        $con = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME) or die("cannot connect");
        return $con;
    }


    static function escape($con, $toEscape)
    {
        return mysqli_real_escape_string($con, $toEscape);
    }

    public function execQuery($con, $sql)
    {
        $callingInfo[0] = debug_backtrace()[1]['function'];
        $callingInfo[1] = debug_backtrace()[0]['file'];
        $callingInfo[2] = debug_backtrace()[0]['line'];


        $before = microtime(true);
        $result = mysqli_query($con, $sql);
        $after = microtime(true);
        $timeTaken = ($after - $before) * 1000;

        if ($result == false) {
            // @codeCoverageIgnoreStart
            $this->logger->addError('Error in sql: ' . $sql);
        } else {
            if (SQL_PROFILING) {
                if ($result != false) {
                    $rows = mysqli_num_rows($result);
                } else {
                    $rows = 0;
                }
                $this->logger->addDebug("SQL [" . $sql . "] [rows:" . $rows . " " . mysqli_info($con) . "] [" . $timeTaken . "ms]", $callingInfo);
            }
        }
        return $result;
    }
}
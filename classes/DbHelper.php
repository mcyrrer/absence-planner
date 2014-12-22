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
        $before = microtime(true);
        $result = mysqli_query($con, $sql);
        $rows = mysqli_num_rows($result);
        $after = microtime(true);
        $timeTaken = ($after - $before) * 1000;
        if (SQL_PROFILING) {
            $this->logger->addDebug("SQL [" . $sql . "] [rows:" . $rows . " " . mysqli_info($con) . "] [" . $timeTaken . "ms]");
        }
        return $result;
    }
}

?>
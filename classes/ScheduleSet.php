<?php
use Underscore\Types\String;

require_once BASEPATH.'/vendor/autoload.php';
require_once BASEPATH.'/classes/autoloader.php';


//TODO: all requests has none as type!? fix it
//[2014-12-22 22:54:07] name.INFO: type is none but there is no record of this date in db so nothing will be done. (AlecDickinson 2014-12-08) ["C:\MyMagicFolder\SourceCode\Web\vacation\classes\ScheduleSet.php",126] []

/**
 * Class to help out with common mysql tasks
 */
class ScheduleSet
{
    protected static $logger;
    protected static $user;
    var $dbM;

    function __construct()
    {
        $l = new Logging();
        self::$logger = $l->getLogger();
        $this->dbM = new DbHelper();
    }

    public function setUserSchedule($user)
    {
        self::$user = $_SESSION['user'];

        $dateH = new DateHelper();
        $con = $this->dbM->connectToMainDb();


        list($date, $from, $to) = $this->parseRequestParameters($con);

        $datesToCheck = array();
        if (isset($date)) {
            $datesToCheck[] = $date;
        }
        if (isset($from)) {
            $datesToCheck[] = $from;
        }
        if (isset($to))
        {
            $datesToCheck[] = $to;
        }

        foreach ($datesToCheck as $aDateToCheck) {
            if (!$dateH->validateDate($aDateToCheck, "Y-m-d")) {
                self::$logger->addError('Date has incorrect format: ' . $date, array(__FILE__, __LINE__));
                if (!UNIT_TEST_SERVER) {
                    // @codeCoverageIgnoreStart
                    header("HTTP/1.0 500 Internal Server Error");
                    return;
                } // @codeCoverageIgnoreEnd
                else {
                    return TEST_EVENT_PARAM_STATE_NOT_SET;
                }
            }
        }

        if (isset($_REQUEST['state'])) {
            $stateUnsecure = $_REQUEST['state'];
            $state = $this->dbM->escape($con, $stateUnsecure);
            unset($stateUnsecure);
        } else {
            self::$logger->addError('state is no set: ', array(__FILE__, __LINE__));
            if (!UNIT_TEST_SERVER) {
                // @codeCoverageIgnoreStart
                header("HTTP/1.0 500 Internal Server Error");
                return;
            } // @codeCoverageIgnoreEnd
            else {
                return TEST_EVENT_PARAM_STATE_NOT_SET;
            }

        }

        if (isset($date) && $date != null && isset($state)) {
            $result = $this->insertOneDayToDb(self::$user, $date, $con, $state);
            self::$logger->addDebug('Insert of new event: ' . self::$user . ' ' . $date, array(__FILE__, __LINE__));

        } elseif (isset($from) && $from != null && isset($to) && $to != null && isset($state)) {
            $begin = new DateTime($from);
            $end = new DateTime($to);
            $end = $end->modify('+1 day');

            $interval = new DateInterval('P1D');
            //TODO: DatePeriodDoesNot Work!!!!! Fix it
            $daterange = new DatePeriod($begin, $interval, $end);
            if(count($daterange)==0)
            {
                self::$logger->addError('Date range has '. count($daterange). ' objects = no days', array(__FILE__, __LINE__));

            }

            foreach ($daterange as $aDate) {
                $this->insertOneDayToDb(self::$user, $aDate->format("Ymd"), $con, $state);
            }
        } else {
            self::$logger->addError('Parameter error', array(__FILE__, __LINE__));
            if (!UNIT_TEST_SERVER) {
                // @codeCoverageIgnoreStart
                header("HTTP/1.0 500 Internal Server Error");
            } // @codeCoverageIgnoreEnd
            else {
                return TEST_EVENT_PARAM_DATE_NOT_SET;
            }
        }


        mysqli_close($con);
    }

    /**
     * @param $user
     * @param $date
     * @param $con
     * @param $state
     * @return array
     */
    public function insertOneDayToDb($user, $date, $con, $state)
    {
        $result = $this->getAllEventsForADay($user, $date, $con);

        if (mysqli_num_rows($result) > 0) {
            $sql = $this->eventAlreadyExistInDb($user, $date, $state);
        } elseif (String::is('none',$state)) {
            self::$logger->addInfo('type is none but there is no record of this date in db so nothing will be done. ('. $user . " " . $date.")", array(__FILE__, __LINE__));
            return;
        } else {
            $sql = $this->getInsertEventSql($user, $date, $state);
        }
        $this->evaluateSqlResult($con, $sql);
    }

    /**
     * @param $user
     * @param $date
     * @return string
     */
    private function deleteEvent($user, $date)
    {
        self::$logger->addDebug('Delete of event: ' . $user . ' ' . $date, array(__FILE__, __LINE__));
        $sql = "DELETE FROM events WHERE user = '$user' AND eventDate = '$date'";
        return $sql;
    }

    /**
     * @param $user
     * @param $date
     * @param $state
     * @return string
     */
    private function getUpdateEventSql($user, $date, $state)
    {
        $sql = "UPDATE events  SET type = '$state', approved=0 WHERE user = '$user' AND eventDate = '$date'";
        return $sql;
    }

    /**
     * @param $user
     * @param $date
     * @param $state
     * @return string
     */
    private function getInsertEventSql($user, $date, $state)
    {

        $sql = "INSERT INTO events (type, user, eventDate) VALUES ('$state','$user','$date') ";
        return $sql;
    }

    /**
     * @param $user
     * @param $date
     * @param $state
     * @return string
     */
    private function eventAlreadyExistInDb($user, $date, $state)
    {
        if (strcmp($state, 'none') == 0) {
            $sql = $this->deleteEvent($user, $date);
            return $sql;
        } else {
            $sql = $this->getUpdateEventSql($user, $date, $state);
            return $sql;
        }
    }

    /**
     * @param $con
     * @param $sql
     * @return int
     */
    private function evaluateSqlResult($con, $sql)
    {
        if (isset($sql)) {
            $result = $this->dbM->execQuery($con, $sql);
            if ($result == false) {
                // @codeCoverageIgnoreStart
                self::$logger->addError('Error in sql: ' . $sql, array(__FILE__, __LINE__));
                if (!UNIT_TEST_SERVER) {
                    header("HTTP/1.0 500 Internal Server Error");
                }
                else {
                    return TEST_INVALID_SQL;
                }
            }
        }
        // @codeCoverageIgnoreEnd
    }

    /**
     * @param $user
     * @param $date
     * @param $con
     * @return bool|mysqli_result
     */
    private function getAllEventsForADay($user, $date, $con)
    {
        $sqlCheck = "SELECT * FROM vacation.events WHERE user='$user' and eventDate='$date'";
        $result = $this->dbM->execQuery($con, $sqlCheck);
        return $result;
    }

    /**
     * @param $con
     * @return array|int
     * @internal param $dbM
     * @internal param $dateUnsecure
     */
    private function parseRequestParameters($con)
    {
        $date = null;
        $from = null;
        $to = null;
        if (isset($_REQUEST['date'])) {
            $dateUnsecure = $_REQUEST['date'];
            $date = $this->dbM->escape($con, $dateUnsecure);
            self::$logger->addInfo('Is single insert', array(__FILE__, __LINE__));
            return array($date, $from, $to);
        } elseif (isset($_REQUEST['from'], $_REQUEST['to']) && strcmp($_REQUEST['to'], "") != 0 && strcmp($_REQUEST['from'], "") != 0) {
            $fromUnsecure = $_REQUEST['from'];
            $toUnsecure = $_REQUEST['to'];
            $from = $this->dbM->escape($con, $fromUnsecure);
            $to = $this->dbM->escape($con, $toUnsecure);
            unset($dateUnsecure);
            unset($toUnsecure);
            self::$logger->addInfo('Is multidate insert', array(__FILE__, __LINE__));
            return array($date, $from, $to);
        } else {
            if (!UNIT_TEST_SERVER) {
                // @codeCoverageIgnoreStart
                header("HTTP/1.0 500 Internal Server Error");
            } // @codeCoverageIgnoreEnd
            else {
                return TEST_EVENT_PARAM_STATE_NOT_SET;
            }
            self::$logger->addError('Parameter error', array(__FILE__, __LINE__));
            exit();
        }
    }


}

?>
<?php
require_once BASEPATH.'/vendor/autoload.php';
require_once BASEPATH.'/classes/autoloader.php';

/**
 * Class to help out with common mysql tasks
 */
class ScheduleSet
{
    protected static $logger;
    protected static $user;

    function __construct()
    {
        $l = new Logging();
        self::$logger = $l->getLogger();
    }

    public function setUserSchedule($user)
    {
        self::$user = $_SESSION['user'];
        $dbM = new DbHelper();
        $dateH = new DateHelper();
        $con = $dbM->connectToMainDb();

        list($date, $from, $to) = $this->parseRequestParameters($dbM, $con);

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
            $state = $dbM->escape($con, $stateUnsecure);
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
            $daterange = new DatePeriod($begin, $interval, $end);

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
    private function insertOneDayToDb($user, $date, $con, $state)
    {
        $result = $this->getAllEventsForADay($user, $date, $con);

        if (mysqli_num_rows($result) > 0) {
            $sql = $this->eventAlreadyExistInDb($user, $date, $state);
        } elseif (strcmp($state, 'none') == 0) {
            self::$logger->addDebug('type is none but there is no record of this date in db so nothing will be done.: ', array(__FILE__, __LINE__));
            return;
        } else {
            $sql = $this->insertEvent($user, $date, $state);
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
        self::$logger->addDebug('Delete of event: ' . self::$user . ' ' . $date, array(__FILE__, __LINE__));
        $sql = "DELETE FROM events WHERE user = '$user' AND eventDate = '$date'";
        return $sql;
    }

    /**
     * @param $user
     * @param $date
     * @param $state
     * @return string
     */
    private function updateEvent($user, $date, $state)
    {
        self::$logger->addDebug('Update of event: ' . self::$user . ' ' . $date, array(__FILE__, __LINE__));

        $sql = "UPDATE events  SET type = '$state', approved=0 WHERE user = '$user' AND eventDate = '$date'";
        return $sql;
    }

    /**
     * @param $user
     * @param $date
     * @param $state
     * @return string
     */
    private function insertEvent($user, $date, $state)
    {

        $sql = "INSERT INTO events (type, user, eventDate) VALUES ('$state','$user','$date') ";
        self::$logger->addDebug($sql, array(__FILE__, __LINE__));

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
            $sql = $this->updateEvent($user, $date, $state);
            return $sql;
        }
    }

    /**
     * @param $con
     * @param $sql
     */
    private function evaluateSqlResult($con, $sql)
    {
        if (isset($sql)) {
            $result = mysqli_query($con, $sql);
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
        $sqlCheck = "SELECT * FROM `vacation`.`events` WHERE user='$user' and eventDate='$date'";
        $result = mysqli_query($con, $sqlCheck);
        return $result;
    }

    /**
     * @param $dbM
     * @param $con
     * @param $dateUnsecure
     */
    private function parseRequestParameters($dbM, $con)
    {
        $date = null;
        $from = null;
        $to = null;
        if (isset($_REQUEST['date'])) {
            $dateUnsecure = $_REQUEST['date'];
            $date = $dbM->escape($con, $dateUnsecure);
            self::$logger->addInfo('Is single insert', array(__FILE__, __LINE__));
            return array($date, $from, $to);
        } elseif (isset($_REQUEST['from'], $_REQUEST['to']) && strcmp($_REQUEST['to'], "") != 0 && strcmp($_REQUEST['from'], "") != 0) {
            $fromUnsecure = $_REQUEST['from'];
            $toUnsecure = $_REQUEST['to'];
            $from = $dbM->escape($con, $fromUnsecure);
            $to = $dbM->escape($con, $toUnsecure);
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
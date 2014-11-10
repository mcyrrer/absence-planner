<?php
require '../../../vendor/autoload.php';
require '../../../settings.inc';
require '../../../classes/DbHelper.php';
require '../../../classes/Logging.php';

$c = new ApiSetSchedule();
$c->parseApi();

class ApiSetSchedule
{
    protected static $logger;
    protected static $user;

    function __construct()
    {
        $l = new Logging();
        self::$logger = $l->getLogger();

        if (isset($_SERVER['AUTHENTICATE_SAMACCOUNTNAME'])) {
            self::$user = $_SERVER['AUTHENTICATE_SAMACCOUNTNAME'];
        } else {
            self::$user = 'testuser';
        }
    }

    public function parseApi()
    {
        $dbM = new DbHelper();
        $con = $dbM->connectToMainDb();

        if (isset($_REQUEST['date'])) {
            $dateUnsecure = $_REQUEST['date'];
            $date = $dbM->escape($con, $dateUnsecure);
            self::$logger->addInfo('Is single insert', array(__FILE__, __LINE__));
        } elseif (isset($_REQUEST['from'], $_REQUEST['to']) && strcmp($_REQUEST['to'],"")!=0 && strcmp($_REQUEST['from'],"")!=0) {
            $fromUnsecure = $_REQUEST['from'];
            $toUnsecure = $_REQUEST['to'];
            $from = $dbM->escape($con, $fromUnsecure);
            $to = $dbM->escape($con, $toUnsecure);
            unset($dateUnsecure);
            unset($toUnsecure);
            self::$logger->addInfo('Is multidate insert', array(__FILE__, __LINE__));
        } else {
            header("HTTP/1.0 500 Internal Server Error");
            self::$logger->addError('Parameter error', array(__FILE__, __LINE__));
            exit();
        }

        if (isset($_REQUEST['state'])) {
            $stateUnsecure = $_REQUEST['state'];
            $state = $dbM->escape($con, $stateUnsecure);
            unset($stateUnsecure);
        }
        else
        {
            self::$logger->addError('state is no set: ', array(__FILE__, __LINE__));
            header("HTTP/1.0 500 Internal Server Error");
        }

        if (isset($date) && isset($state)) {
            $result = $this->insertOneDayToDb(self::$user, $date, $con, $state);
            self::$logger->addDebug('Insert of new event: '.self::$user.' '.$date, array(__FILE__, __LINE__));

        } elseif (isset($from) && isset($to) && isset($state)) {
            $begin = new DateTime($from);
            $end = new DateTime($to);
            $end = $end->modify('+1 day');

            $interval = new DateInterval('P1D');
            $daterange = new DatePeriod($begin, $interval, $end);

            foreach ($daterange as $aDate) {
                $this->insertOneDayToDb(self::$user, $aDate->format("Ymd"), $con, $state);
            }
        } else {
            header("HTTP/1.0 500 Internal Server Error");
            self::$logger->addError('Parameter error', array(__FILE__, __LINE__));
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
        $sqlCheck = "SELECT * FROM `vacation`.`events` WHERE user='$user' and eventDate='$date'";
        $result = mysqli_query($con, $sqlCheck);

        if (mysqli_num_rows($result) > 0) {
            if (strcmp($state, 'none') == 0) {
                self::$logger->addDebug('Delete of event: '.self::$user.' '.$date, array(__FILE__, __LINE__));
                $sql = "DELETE
            FROM
                events
            WHERE
                user = '$user'
            AND eventDate = '$date'";
            } else {
                self::$logger->addDebug('Update of event: '.self::$user.' '.$date, array(__FILE__, __LINE__));

                $sql = "UPDATE
                        events
                    SET
                        type = '$state'
                    WHERE
                        user = '$user'
                        AND eventDate = '$date'
                        ";
            }

        } elseif (strcmp($state, 'none') == 0) {
            self::$logger->addDebug('type is none but there is no record of this date in db so nothin will be done.: ', array(__FILE__, __LINE__));

        } else {

            self::$logger->addDebug('type is NOT none: ', array(__FILE__, __LINE__));

            $sql = "INSERT
                 INTO
                    events
                    (
                        type,
                        user,
                        eventDate
                    )
                    VALUES
                    (
                        '$state',
                        '$user',
                        '$date'
                    ) ";
        }
        if (isset($sql)) {
            $result = mysqli_query($con, $sql);
            if ($result == false) {
                self::$logger->addError('Error in sql: ' . $sql, array(__FILE__, __LINE__));
                header("HTTP/1.0 500 Internal Server Error");
            } else {
//            self::$logger->addDebug('Sql insert ok: '.$sql,array(__FILE__,__LINE__));
            }
        }
    }
}







<?php
require_once BASEPATH . '/vendor/autoload.php';
require_once BASEPATH . '/classes/DbHelper.php';
require_once BASEPATH . '/classes/DateHelper.php';
require_once BASEPATH . '/classes/ScheduleObject.php';



/**
 * Class to help out with common mysql tasks
 */
class OverviewGet
{
    private $logger;

    function __construct()
    {
        $l = new Logging();
        $this->logger = $l->getLogger();
    }

    public function getOverviewView()
    {
        $dbM = new DbHelper();
        $con = $dbM->connectToMainDb();

        if (isset($_REQUEST['from'])) {
            $from = $dbM::escape($con, $_REQUEST['from']);
        } else {
            $from = date('Y-m-d', time());
        }
        $begin = new DateTime($from);
        $end = new DateTime($from);

        if (isset($_REQUEST['range'])) {
            $range = $dbM::escape($con, $_REQUEST['range']);

            $end = $end->modify('+' . $range);
        } else {
            $end = $end->modify('+2 month');
        }

        return $this->getOverviewViewJson($con, $begin, $end);
    }


    private function getOverviewViewJson($con, $begin, $end)
    {
        $interval = new DateInterval('P1D');
        $daterange = new DatePeriod($begin, $interval, $end);

        $dates = array();
        $userScheduleInformation = array();


        foreach ($daterange as $aDate) {
            if (DateHelper::isWeekend($aDate->format("Y-m-d")) == false) {
                $dates[] = $aDate->format("Y m d");
            }
        }

        $resultUser = $this->getAllUsers($con);

        while ($rowUser = mysqli_fetch_array($resultUser, MYSQLI_ASSOC)) {
            $user = array();
            $useSchedule = $this->getUserScheduleFromDb($rowUser, $begin, $end, $con);

            $user['fullname'] = $rowUser['fullname'];
            $user['team'] = $rowUser['team'];
            $user['manager'] = $rowUser['manager'];
            $user['id'] = $rowUser['id'];
            $user['schedule'] = $this->iterateAllDaysInViewJson($daterange, $useSchedule);
            $userScheduleInformation[$user['fullname']] = $user;
        }
        $scheduleIncDateRange['dates'] = $dates;
        $scheduleIncDateRange['schedules'] = $userScheduleInformation;
        if (version_compare(phpversion(), '5.3.10', '<')) {
            return json_encode($scheduleIncDateRange);
        } else {
            return json_encode($scheduleIncDateRange, JSON_PRETTY_PRINT);
        }
    }

    private function getUserScheduleFromDb($rowUser, $begin, $end, $con)
    {
        $sqlUserSchedule = "SELECT * FROM events WHERE user='" . $rowUser['username'] . "' AND eventDate > '" . $begin->format("Y-m-d") . "' AND eventDate < '" . $end->format("Y-m-d") . "'";
        $resultUseSchedule = mysqli_query($con, $sqlUserSchedule);
        $useSchedule = mysqli_fetch_all($resultUseSchedule, MYSQLI_ASSOC);
        return $useSchedule;
    }

    private function iterateAllDaysInViewJson($daterange, $useSchedule)
    {
        $userSchedule = array();
        foreach ($daterange as $aDate) {
            if (DateHelper::isWeekend($aDate->format("Y-m-d")) == false) {
                foreach ($useSchedule as $aDay) {
                    if (in_array($aDate->format("Y-m-d"), $aDay)) {
                        $day = array();
                        $day['type'] = $aDay['type'];
                        $day['date'] = $aDate->format("Y m d");
                        $day['approved'] = $aDay['approved'];
                        $day['id'] = $aDay['id'];
                        $userSchedule[$aDate->format("Y m d")] = $day;
                    }
                }
            }
        }
        return $userSchedule;
    }

    /**
     * @param $con
     * @return bool|mysqli_result
     */
    private function getAllUsers($con)
    {
        $sqlUser = 'SELECT * FROM users';
        $resultUser = mysqli_query($con, $sqlUser);
        return $resultUser;
    }
}

?>
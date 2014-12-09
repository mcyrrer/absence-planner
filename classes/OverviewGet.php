<?php
require_once BASEPATH . '/vendor/autoload.php';
require_once BASEPATH . '/classes/DbHelper.php';
require_once BASEPATH . '/classes/DateHelper.php';
require_once BASEPATH . '/classes/ScheduleObject.php';
require_once BASEPATH . '/classes/Logging.php';


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

        if (isset($_REQUEST['json'])) {
            return $this->getOverviewViewJson($dbM, $con, $begin, $end);
        } else {
            $this->getOverviewViewHTML($dbM, $con, $begin, $end);
        }
    }


    private function getOverviewViewJson($dbM, $con, $begin, $end)
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
        return json_encode($userScheduleInformation, JSON_PRETTY_PRINT);
    }

    private function getOverviewViewHTML($dbM, $con, $begin, $end)
    {
        echo '<table class="fancyTable" id="myTable01">';
        echo '<thead>';
        echo '<tr>';
        echo '<th class="thItem">AName</th>';
        echo '<th class="thItem">Team</th>';
        echo '<th class="thItem">Manager</th>';


        $interval = new DateInterval('P1D');
        $daterange = new DatePeriod($begin, $interval, $end);

        foreach ($daterange as $aDate) {
            if (DateHelper::isWeekend($aDate->format("Y-m-d")) == false) {
                echo '<th>' . $aDate->format("\wW Y m d") . "</th>";
            }
        }

        foreach ($daterange as $aDate) {
            if (DateHelper::isWeekend($aDate->format("Y-m-d")) == false) {
//                echo '<th>' . $aDate->format("W") . "</th>";
            }
        }
        echo '</thead>';
        echo '<tbody>';


        $resultUser = $this->getAllUsers($con);

        while ($rowUser = mysqli_fetch_array($resultUser, MYSQLI_ASSOC)) {
            $useSchedule = $this->getUserScheduleFromDb($rowUser, $begin, $end, $con);

            echo '<tr>';
            echo '<td class="thItem">' . $rowUser['fullname'] . '</td>';
            echo '<td class="thItem">' . $rowUser['team'] . '</td>';
            echo '<td class="thItem">' . $rowUser['manager'] . '</td>';
            $this->iterateAllDaysInViewHtml($daterange, $useSchedule);
            echo '</tr>';
        }
        echo '</tbody>';
        echo '</table>';


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
                        $day['type']=$aDay['type'];
                        $day['approved']=$aDay['approved'];
                        $day['id']=$aDay['id'];
                        $userSchedule[$aDate->format("Y-m-d")] = $day;
                    }
                }
            }
        }
        return $userSchedule;
    }

    private function iterateAllDaysInViewHtml($daterange, $useSchedule)
    {
        foreach ($daterange as $aDate) {
            if (DateHelper::isWeekend($aDate->format("Y-m-d")) == false) {
                $cellStr = '<td>.</td>';
                $cellStr = $this->iterateAllDaysInViewAndEchoTypeToCellIfDateExistHTML($useSchedule, $aDate, $cellStr);
                echo $cellStr;
            }
        }
    }

    private function iterateAllDaysInViewAndEchoTypeToCellIfDateExistHTML($useSchedule, $aDate, $cellStr)
    {
        foreach ($useSchedule as $aDay) {
            if (in_array($aDate->format("Y-m-d"), $aDay)) {
                $cellStr = '<td class="' . $aDay['type'] . '"></td>';
//                $cellStr = '<td>.</td>';
            }
        }
        return $cellStr;
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
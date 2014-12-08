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

        echo '<table class="fancyTable" id="myTable01">';
        echo '<thead>';
        echo '<tr>';
        echo '<th class="thItem">AName</th>';
        echo '<th class="thItem">Team</th>';
        echo '<th class="thItem">Manager</th>';

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
        $interval = new DateInterval('P1D');
        $daterange = new DatePeriod($begin, $interval, $end);

        foreach ($daterange as $aDate) {
            if (DateHelper::isWeekend($aDate->format("Y-m-d")) == false) {
                echo '<th>' . $aDate->format("Y m d") . "</th>";
            }
        }

        foreach ($daterange as $aDate) {
            if (DateHelper::isWeekend($aDate->format("Y-m-d")) == false) {
//                echo '<th>' . $aDate->format("W") . "</th>";
            }
        }
        echo '</thead>';
        echo '<tbody>';


        $sqlUser = 'SELECT * FROM users';
        $resultUser = mysqli_query($con, $sqlUser);


        while ($rowUser = mysqli_fetch_array($resultUser, MYSQLI_ASSOC)) {
            $useSchedule = $this->getUserScheduleFromDb($rowUser, $begin, $end, $con);

            echo '<tr>';
            echo '<td class="thItem">' . $rowUser['fullname'].'</td>';
            echo '<td class="thItem">' . $rowUser['team'].'</td>';
            echo '<td class="thItem">' . $rowUser['manager'].'</td>';
            $this->iteratAllDaysInView($daterange, $useSchedule);
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

    private function iteratAllDaysInView($daterange, $useSchedule)
    {
        foreach ($daterange as $aDate) {
            if (DateHelper::isWeekend($aDate->format("Y-m-d")) == false) {
                $cellStr = '<td>.</td>';
                $cellStr = $this->iterateAllDaysInViewAndEchoTypeToCellIfDateExist($useSchedule, $aDate, $cellStr);
                echo $cellStr;
            }
        }
    }

    private function iterateAllDaysInViewAndEchoTypeToCellIfDateExist($useSchedule, $aDate, $cellStr)
    {
        foreach ($useSchedule as $aDay) {
            if (in_array($aDate->format("Y-m-d"), $aDay)) {
                $cellStr = '<td class="' . $aDay['type'] . '"></td>';
//                $cellStr = '<td>.</td>';
            }
        }
        return $cellStr;
    }
}

?>
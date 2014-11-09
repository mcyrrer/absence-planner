<?php
require_once BASEPATH.'/vendor/autoload.php';
require_once BASEPATH.'/classes/DbHelper.php';
require_once BASEPATH.'/classes/DateHelper.php';
require_once BASEPATH.'/classes/Logging.php';


/**
 * Class to help out with common mysql tasks
 */
class ScheduleGet
{
    private $logger;

    function __construct()
    {
        $l = new Logging();
        $this->logger = $l->getLogger();
    }

    /**
     * @param $user
     * @param $resultArray
     */
    public function getUserSchedule($user)
    {
        $resultArray = array();
        $dbM = new DbHelper();
        $con = $dbM->connectToMainDb();

        $sql = "SELECT * FROM events WHERE user='$user'";
        $result = mysqli_query($con, $sql);
        $resultAllRowsArray = array();

        if ($result != false) {
            $i = 0;
            while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
                 $resultAllRowsArray[$i] = $this->getScheduleInformationForOneDay($resultArray, $row);
                $i = $i + 1;
            }
        }
        return $resultAllRowsArray;
    }

    /**
     * Get the color associated with the absence reason
     * @param $row
     * @return string
     */
    private function getAbsenceReasonColor($row)
    {
        switch ($row['type']) {
            case 'vacation':
                $color = 'red';
                break;
            case 'course':
                $color = 'blue';
                break;
            case 'parental':
                $color = 'orange';
                break;
        }
        return $color;
    }

    /**
     * @param $resultArray
     * @param $row
     * @return array
     */
    private function getScheduleInformationForOneDay($resultArray, $row)
    {
        $resultArray['allDay'] = 'true';
        $resultArray['title'] = $row['type'];
        $resultArray['id'] = $row['id'];
        $resultArray['user'] = $row['user'];
        $resultArray['start'] = $row['eventDate'] . ' 02:00:00';
        $resultArray['end'] = $row['eventDate'] . ' 23:00:00';
        $color = $this->getAbsenceReasonColor($row);
        $resultArray['backgroundColor'] = $color;
        return $resultArray;
    }
}

?>
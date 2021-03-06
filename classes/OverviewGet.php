<?php

use Carbon\Carbon;
use Underscore\Types\Arrays;
use Underscore\Types\String;


/**
 * Class to help out with common mysql tasks
 */
class OverviewGet
{
    private $logger;
    private $managers;

    /*Overview page settings*/
    const DEFAULT_DATE_RANGE = 40;
    const OVERVIEW_ROW_LIMIT = 20;

    function __construct()
    {
        $l = new Logging();
        $this->logger = $l->getLogger();
    }

    public function getOverviewView()
    {
        $dbM = new DbHelper();
        $con = $dbM->connectToMainDb();

        $cFrom = $this->getFromDate($dbM, $con);

        $cEnd = Carbon::createFromFormat('Y-m-d', $cFrom->toDateString());

        //
        if (isset($_REQUEST['offset'])) {
            $offset = $dbM::escape($con, $_REQUEST['offset']);
            if($offset<0)
            {
                $this->logger->addWarning("Offset is less then 0 (is ".$offset."), setting it to 0  " . $offset, array(__FILE__, __LINE__));
                $offset=0;
            }
        } else {
            $offset = 0;
        }

        $offset = $offset*self::OVERVIEW_ROW_LIMIT;
        $this->logger->addInfo("Generating overview based on offset  " . $offset, array(__FILE__, __LINE__));


        //
        $this->setRangeForDatePeriod($cEnd, $dbM, $con);

        list($type, $typedata) = $this->getTypeOfDataToReturn($dbM, $con);

        return $this->getOverviewViewArray($con, $cFrom, $cEnd, $type, $typedata, $offset, $dbM);
    }


    /**
     * @param $con
     * @param Carbon $begin
     * @param Carbon $end
     * @param $subOverviewKind [ALL][TEAM][MANAGER]
     * @param $subValue
     * @return mixed
     */
    private function getOverviewViewArray($con, Carbon $begin, Carbon $end, $subOverviewKind, $subValue, $offset, DbHelper $dbM)
    {
        $this->logger->addInfo("Get overview schedule from " . $begin->toDateString() . " to " . $end->toDateString() . " for " . $subOverviewKind, array(__FILE__, __LINE__));

        $interval = new DateInterval('P1D');
        $b = new DateTime($begin->toDateString());
        $e = new DateTime($end->toDateString());
        $daterange = new DatePeriod($b, $interval, $e);

        $dates = array();
        $userScheduleInformation = array();


        foreach ($daterange as $aDate) {
            if (DateHelper::isWeekend($aDate->format("Y-m-d")) == false) {
                $dates[] = $aDate->format("Y m d");
            }
        }

        if (String::is("ALL", $subOverviewKind)) {
            $resultUser = $this->getAllUsers($con, $dbM, $offset);
        } elseif (String::is("TEAM", $subOverviewKind)) {
            $resultUser = $this->getAllUsersInTeam($con, $dbM, $subValue, $offset);
        } elseif (String::is("MANAGER", $subOverviewKind)) {
            $resultUser = $this->getAllUsersBasedOnManager($con, $dbM, $subValue, $offset);
        } else {
            $this->logger->addError("ERROR: wrong kind of subquery kind, should be one of ALL,TEAM,MANAGER but was " . $subOverviewKind, array(__FILE__, __LINE__));
        }

        $managers =  $this->getManagersFullName($con, $dbM);

        while ($rowUser = mysqli_fetch_array($resultUser, MYSQLI_ASSOC)) {
            $user = array();
            $useSchedule = $this->getUserScheduleFromDb($rowUser, $begin, $end, $con, $dbM);

            $user['fullname'] = $rowUser['fullname'];
            $user['username'] = $rowUser['username'];
            $user['team'] = $rowUser['team'];

            if(array_key_exists($rowUser['manager'],$managers))
            {
            $user['manager'] = $managers[$rowUser['manager']];
            $user['managerusername'] = $rowUser['manager'];
            }
            else
            {
                $user['manager'] = 'None';
            }
            $user['id'] = $rowUser['id'];
            $user['schedule'] = $this->iterateAllDaysInViewJson($daterange, $useSchedule);
            $userScheduleInformation[$user['fullname']] = $user;
        }
        $scheduleIncDateRange['dates'] = $dates;
        $scheduleIncDateRange['schedules'] = $userScheduleInformation;
        $this->logger->debug("Get overview schedule from " . $begin->format("Y m d") . " to " . $end->addDays(-1)->format("Y m d") . " done!", array(__FILE__, __LINE__));

        return $scheduleIncDateRange;
    }

    private function getManagersFullName($con, DbHelper $dbM)
    {
        $returnArray = array();
        $sql = "SELECT users.id, users.username as username, users.fullname as fullname, users.team, users.manager FROM users as users right join mangers as managers on users.username = managers.manager_user_id";
        $resultUseSchedule = $dbM->execQuery($con, $sql);
        $managersResult = mysqli_fetch_all($resultUseSchedule, MYSQLI_ASSOC);

        foreach($managersResult as $aManager)
        {
            $returnArray[$aManager['username']]=$aManager['fullname'];
        }
        return $returnArray;
    }


    private function getUserScheduleFromDb($rowUser, $begin, $end, $con, DbHelper $dbM)
    {

        $sqlUserSchedule = "SELECT * FROM events WHERE user='" . $rowUser['username'] . "' AND eventDate >= '" . $begin->format("Y-m-d") . "' AND eventDate < '" . $end->format("Y-m-d") . "'";

        $resultUseSchedule = $dbM->execQuery($con, $sqlUserSchedule);
        $useSchedule = mysqli_fetch_all($resultUseSchedule, MYSQLI_ASSOC);
        return $useSchedule;
    }

    private function iterateAllDaysInViewJson($daterange, $useSchedule)
    {
        $userSchedule = array();
        foreach ($daterange as $aDate) {
            if (DateHelper::isWeekend($aDate->format("Y-m-d")) == false) {
                foreach ($useSchedule as $aDay) {
                    if (Arrays::contains($aDay, $aDate->format("Y-m-d"))) {
                        $day = array();
                        $day['type'] = $aDay['type'];
                        $day['date'] = $aDate->format("Y m d");
                        $day['approved'] = $aDay['approved'];
                        $day['id'] = $aDay['id'];
                        $userSchedule[$aDate->format("Y m d")] = $day;
//                        $userSchedule[$day['id']] = $day;
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
    private function getAllUsers($con,DbHelper $dbM,$offset)
    {
        $sqlUser = 'SELECT * FROM users LIMIT '.$offset. ',' . self::OVERVIEW_ROW_LIMIT . '';
        $resultUser =  $dbM->execQuery($con, $sqlUser);
        return $resultUser;

    }

    private function getAllUsersInTeam($con, DbHelper $dbM, $team, $offset)
    {
        $sqlUser = 'SELECT * FROM users WHERE team="' . $team . '"  LIMIT '.$offset. ',' . self::OVERVIEW_ROW_LIMIT . '';
        $resultUser =  $dbM->execQuery($con, $sqlUser);
        return $resultUser;
    }

    private function getAllUsersBasedOnManager($con,DbHelper $dbM, $manager, $offset)
    {
        $sqlUser = 'SELECT * FROM users WHERE manager="' . $manager . '"  LIMIT '.$offset. ',' . self::OVERVIEW_ROW_LIMIT . '';
        $resultUser =  $dbM->execQuery($con, $sqlUser);
        return $resultUser;
    }

    /**
     * @param $dbM
     * @param $con
     * @return Carbon date
     */
    private function getFromDate($dbM, $con)
    {
        if (isset($_REQUEST['from'])) {
            $from = $dbM::escape($con, $_REQUEST['from']);
            $fromSlice = explode("-", $from);
            $cFrom = Carbon::createFromDate($fromSlice[0], $fromSlice[1], $fromSlice[2]);
            $this->logger->addDebug("from is  " . $_REQUEST['from'], array(__FILE__, __LINE__));
            $this->logger->addDebug("Setting start date to  " . $cFrom->toFormattedDateString(), array(__FILE__, __LINE__));

        } elseif (isset($_REQUEST['fromend'])) {
            $from = $dbM::escape($con, $_REQUEST['fromend']);
            $fromSlice = explode("-", $from);
            $cFrom = Carbon::createFromDate($fromSlice[0], $fromSlice[1], $fromSlice[2]);
            $this->logger->addDebug("from is  " . $_REQUEST['fromend'], array(__FILE__, __LINE__));
            $this->logger->addDebug("Setting start date to  " . $cFrom->toFormattedDateString(), array(__FILE__, __LINE__));

        } else {
            $from = date('Y-m-d', time());
            $fromSlice = explode("-", $from);
            $cFrom = Carbon::createFromDate($fromSlice[0], $fromSlice[1], $fromSlice[2]);
            $cFrom->addDays(-3);
            $this->logger->addDebug("Setting start date to  " . $cFrom->toFormattedDateString(), array(__FILE__, __LINE__));
        }
        if (isset($_REQUEST['fromend'])) {
            $this->logger->addDebug("set date to -60 days from " . $cFrom->toFormattedDateString(), array(__FILE__, __LINE__));
            $cFrom = $cFrom->addDays(-self::DEFAULT_DATE_RANGE);
            $this->logger->addDebug("Date is now " . $cFrom->toFormattedDateString(), array(__FILE__, __LINE__));
            return $cFrom;
        }
        return $cFrom;
    }

    /**
     * @param $cEnd
     * @param $dbM
     * @param $con
     */
    private function setRangeForDatePeriod($cEnd, $dbM, $con)
    {
        if (isset($_REQUEST['range'])) {
            $this->logger->addDebug("End was " . $cEnd->toDateString(), array(__FILE__, __LINE__));
            $range = $dbM::escape($con, $_REQUEST['range']);
            $cEnd->addDays($range);
            $this->logger->addDebug("End is " . $cEnd->toDateString(), array(__FILE__, __LINE__));

        } else {
            $this->logger->addDebug("End was " . $cEnd->toDateString(), array(__FILE__, __LINE__));
            $cEnd->addDays(self::DEFAULT_DATE_RANGE + 2);
            $this->logger->addDebug("End is " . $cEnd->toDateString(), array(__FILE__, __LINE__));
        }
    }

    /**
     * @param $dbM
     * @param $con
     * @return array
     */
    private function getTypeOfDataToReturn($dbM, $con)
    {
        if (isset($_REQUEST['type'])) {
            if (isset($_REQUEST['typedata'])) {
                $type = $dbM::escape($con, $_REQUEST['type']);
                $typedata = $dbM::escape($con, $_REQUEST['typedata']);
                return array($type, $typedata);
            } else {
                $this->logger->addError("typedata not set in request, will return ALL", array(__FILE__, __LINE__));
                $type = "ALL";
                $typedata = null;
                return array($type, $typedata);
            }
        } else {
            $type = "ALL";
            $typedata = null;
            return array($type, $typedata);
        }
    }
}

?>
<?php
/**
 * Created by IntelliJ IDEA.
 * User: matgus
 * Date: 2014-11-12
 * Time: 7:53
 */

class ScheduleObject {

    public function createScheduleInformationFromDbResponse($row)
    {
        $resultArray['allDay'] = 'true';
        $resultArray['title'] = $row['type'];
        $resultArray['id'] = $row['id'];
        $resultArray['user'] = $row['user'];
        $resultArray['manager'] = $row['manager'];
        $resultArray['approvalStatus'] = $row['approved'];
        $resultArray['start'] = $row['eventDate'] . ' 02:00:00';
        $resultArray['end'] = $row['eventDate'] . ' 23:00:00';
        $color = $this->getAbsenceReasonColor($row);
        $resultArray['backgroundColor'] = $color;
        return $resultArray;
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
            default:
                $color = 'white';

        }
        return $color;
    }
} 
<?php
require_once BASEPATH.'/vendor/autoload.php';
require_once BASEPATH.'/classes/autoloader.php';


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

        $dbM = new DbHelper();
        $con = $dbM->connectToMainDb();
        $so = new ScheduleObject();
        $sql = "SELECT * FROM events events LEFT JOIN users users ON events.user = users.username WHERE events.user='".$user."'";
        $result = mysqli_query($con, $sql);
        $resultAllRowsArray = array();

        if ($result != false) {
            $i = 0;
            while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
                 $resultAllRowsArray[$i] = $so->createScheduleInformationFromDbResponse($row);
                $i = $i + 1;
            }
        }
        return $resultAllRowsArray;
    }



}

?>
<?php
require_once BASEPATH.'/vendor/autoload.php';
require_once BASEPATH.'/classes/DbHelper.php';
require_once BASEPATH.'/classes/DateHelper.php';
require_once BASEPATH.'/classes/ScheduleObject.php';
require_once BASEPATH.'/classes/Logging.php';


/**
 * Class to help out with common mysql tasks
 */
class ApprovalGet
{
    private $logger;

    function __construct()
    {
        $l = new Logging();
        $this->logger = $l->getLogger();
    }


    public function getApprovalToDoForSingleUser($user,$manager)
    {
        $dbM = new DbHelper();
        $con = $dbM->connectToMainDb();
        $so = new ScheduleObject();


        $sql = "SELECT * FROM events events LEFT JOIN users users ON events.user = users.username WHERE users.manager='".$manager."' AND events.user='".$user."'";
        $result= mysqli_query($con,$sql);
        $resultArray = mysqli_fetch_all($result,MYSQLI_ASSOC);
        $allEvents = array();
        foreach($resultArray as $aEvent)
        {

            $allEvents[]=$so->createScheduleInformationFromDbResponse($aEvent);
        }
        return $allEvents;
    }

    public function getApprovalToDoUserList()
    {

    }

    //TODO: add api to approve/disapporve date,

 }

?>
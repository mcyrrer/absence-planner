<?php
require_once BASEPATH.'/vendor/autoload.php';
require_once BASEPATH.'/classes/autoloader.php';



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


        $sql = "SELECT * FROM events events LEFT JOIN users users ON events.user = users.username WHERE users.manager='".$manager."' AND events.user='".$user."' AND approved=0";
        $result= mysqli_query($con,$sql);
        $resultArray = mysqli_fetch_all($result,MYSQLI_ASSOC);
        $allEvents = array();
        foreach($resultArray as $aEvent)
        {

            $allEvents[]=$so->createScheduleInformationFromDbResponse($aEvent);
        }
        return $allEvents;
    }

    public function getApprovalToDoForAllUsers($manager)
    {
        $dbM = new DbHelper();
        $con = $dbM->connectToMainDb();
        $so = new ScheduleObject();


        $sql = "SELECT * FROM events events LEFT JOIN users users ON events.user = users.username WHERE users.manager='".$manager."' AND approved=0 ORDER BY events.eventDate ASC";
        $result= mysqli_query($con,$sql);
        $resultArray = mysqli_fetch_all($result,MYSQLI_ASSOC);
        $allEvents = array();
        foreach($resultArray as $aEvent)
        {
           // print_r($resultArray);
            $aSo = $so->createScheduleInformationFromDbResponse($aEvent);
            $allEvents[$aSo['user']][]=$aSo;
        }

        return $allEvents;
    }

//    public function getApprovalToDoUserList()
//    {
//
//    }

    //TODO: add api to approve/disapporve date,

 }

?>
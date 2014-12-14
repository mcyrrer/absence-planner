<?php
require_once BASEPATH.'/vendor/autoload.php';
require_once BASEPATH.'/classes/autoloader.php';


/**
 * Class to help out with common mysql tasks
 */
class ApprovalSet
{
    private $logger;

    function __construct()
    {
        $l = new Logging();
        $this->logger = $l->getLogger();
    }


    public function setApprovalForSingleEvent($manager, $eventId,$state)
    {

        $dbM = new DbHelper();
        $con = $dbM->connectToMainDb();
        $eventId=$dbM->escape($con,$eventId);
        $state=$dbM->escape($con,$state);

        $sql = "UPDATE events SET approved = ".$state.",approvedBy='" . $manager . "' WHERE id = " . $eventId;
        $this->logger->addInfo("Updated event ".$eventId." for user Id ".$userId." to ". $state,array(__FILE__, __LINE__));


        $result = mysqli_query($con, $sql);

        return $this->evaluateSqlResult($result,$sql);
    }

    public function setApprovalForAllEvent($manager, $userName,$state)
    {

        $dbM = new DbHelper();
        $con = $dbM->connectToMainDb();
        $userName=$dbM->escape($con,$userName);
        $state=$dbM->escape($con,$state);

        $sql = "UPDATE events SET approved = ".$state.",approvedBy='" . $manager . "' WHERE user = '" . $userName."'";
        $this->logger->addInfo("Updated ALL events for user Id ".$userName." to ". $state,array(__FILE__, __LINE__));

        $result = mysqli_query($con, $sql);

        return $this->evaluateSqlResult($result,$sql);
    }


    private function evaluateSqlResult($result,$sql)
    {

        if ($result == false) {
            $this->logger->addError("Sql Error at set approval: ".$sql,array(__FILE__, __LINE__));
            return false;
        }
        else
        {
            return true;
        }
    }

}

?>
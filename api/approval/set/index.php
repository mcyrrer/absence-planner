<?php
require '../../../vendor/autoload.php';
require '../../../settings.inc';
require '../../../classes/autoloader.php';
$l = new Logging();
$logger = $l->getLogger();

$session = new UserSession();

$actual_link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
$logger->addDebug($actual_link, array(__FILE__, __LINE__));

$approvalSet = new ApprovalSet();


if (isset($_REQUEST['id'], $_REQUEST['state'])) {
    $id = $_REQUEST['id'];
    $state = $_REQUEST['state'];
} elseif (isset($_REQUEST['user'], $_REQUEST['state'])) {
    $user = $_REQUEST['user'];
    $state = $_REQUEST['state'];
}


if (isset($_SESSION['manager'], $id, $state) && $_SESSION['manager'] > 0) {
    if (!$approvalSet->setApprovalForSingleEvent($_SESSION['user'], $id, $state)) {
        $logger->addError('Could not update event', array(__FILE__, __LINE__));
        header("HTTP/1.0 500 Internal Server Error");
    }
} elseif (isset($_SESSION['manager'], $user, $state) && $_SESSION['manager'] > 0) {
    if (!$approvalSet->setApprovalForAllEvent($_SESSION['user'], $user, $state)) {
        $logger->addError('Could not update event', array(__FILE__, __LINE__));
        header("HTTP/1.0 500 Internal Server Error");
    }
} else {
    $logger->addError('Parameter error', array(__FILE__, __LINE__));
    header("HTTP/1.0 500 Internal Server Error");
}






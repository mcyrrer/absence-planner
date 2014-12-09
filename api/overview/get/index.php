<?php
require '../../../vendor/autoload.php';
require '../../../settings.inc';
require '../../../classes/OverviewGet.php';
require '../../../classes/Logging.php';

$l = new Logging();
$logger = $l->getLogger();
if (UNIT_TEST_SERVER && isset($_REQUEST['user'])) {
    $user = $_REQUEST['user'];
} elseif (isset($_SERVER['AUTHENTICATE_SAMACCOUNTNAME'])) {
    $user = $_SERVER['AUTHENTICATE_SAMACCOUNTNAME'];
} else {
    $user = 'testuser';
}

$logger->addDebug($user ." overview api start",array(__FILE__,__LINE__));
$overviewGet = new OverviewGet();
echo $overviewGet->getOverviewView();
$logger->addDebug($user ." overview api end",array(__FILE__,__LINE__));





<?php
require '../../../vendor/autoload.php';
require '../../../settings.inc';
require '../../../classes/autoloader.php';
new UserSession();
$l = new Logging();
$logger = $l->getLogger();

    if (UNIT_TEST_SERVER && isset($_REQUEST['user'])) {
        $user = $_REQUEST['user'];
    } elseif (isset($_SERVER['AUTHENTICATE_SAMACCOUNTNAME'])) {
        $user = $_SERVER['AUTHENTICATE_SAMACCOUNTNAME'];
    } else {
        $user = 'testuser';
    }

//$logger->addDebug($user . " overview api start", array(__FILE__, __LINE__));
    $overviewGet = new OverviewGet();
    $scheduleIncDateRange = $overviewGet->getOverviewView();

    if (version_compare(phpversion(), '5.3.10', '<')) {
        echo json_encode($scheduleIncDateRange);
    } else {
        echo json_encode($scheduleIncDateRange, JSON_PRETTY_PRINT);
    }


//$logger->addDebug($user . " overview api end", array(__FILE__, __LINE__));





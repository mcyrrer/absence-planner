<?php
require '../../../vendor/autoload.php';
require '../../../settings.inc';
require '../../../classes/DbHelper.php';
require '../../../classes/Logging.php';
require '../../../classes/ScheduleSet.php';


if (UNIT_TEST_SERVER && isset($_REQUEST['user'])) {
    $user = $_REQUEST['user'];
}elseif (isset($_SERVER['AUTHENTICATE_SAMACCOUNTNAME'])) {
    $user = $_SERVER['AUTHENTICATE_SAMACCOUNTNAME'];
} else {
    $user = 'testuser';
}

$c = new ScheduleSet();
$c->setUserSchedule($user);






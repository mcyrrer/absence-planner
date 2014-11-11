<?php
//require '../../../vendor/autoload.php';
require '../../../settings.inc';
require '../../../classes/ScheduleGet.php';


if (UNIT_TEST_SERVER && isset($_REQUEST['user'])) {
    $user = $_REQUEST['user'];
}elseif (isset($_SERVER['AUTHENTICATE_SAMACCOUNTNAME'])) {
    $user = $_SERVER['AUTHENTICATE_SAMACCOUNTNAME'];
} else {
    $user = 'testuser';
}

$scheduleGet = new ScheduleGet();

echo json_encode($scheduleGet->getUserSchedule($user), JSON_PRETTY_PRINT);


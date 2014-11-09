<?php
//require '../../../vendor/autoload.php';
require '../../../settings.inc';
require '../../../classes/ScheduleGet.php';


if (UNIT_TEST_SERVER && isset($_REQUEST['user'])) {
    $user = $_REQUEST['user'];
} else {
    $user = "matgus";
}
$scheduleGet = new ScheduleGet();

echo json_encode($scheduleGet->getUserSchedule($user), JSON_PRETTY_PRINT);


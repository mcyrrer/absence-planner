<?php
//require '../../../vendor/autoload.php';
require '../../../settings.inc';
require '../../../classes/autoloader.php';
new UserSession();


$scheduleGet = new ScheduleGet();
$user = $_SESSION['user'];
echo json_encode($scheduleGet->getUserSchedule($user), JSON_PRETTY_PRINT);


<?php
require '../../../vendor/autoload.php';
require '../../../settings.inc';
require '../../../classes/autoloader.php';
new UserSession();


$c = new ScheduleSet();

$c->setUserSchedule($_SESSION['user']);






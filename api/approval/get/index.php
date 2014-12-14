<?php
require '../../../vendor/autoload.php';
require '../../../settings.inc';
require '../../../classes/autoloader.php';

new UserSession();


if (UNIT_TEST_SERVER && isset($_REQUEST['user'])) {
    $user = $_REQUEST['user'];
}elseif (isset($_SESSION['user'])) {
    $user = $_SESSION['user'];
} else {
    $user = 'testuser';
}

$ag = new ApprovalGet();

echo json_encode($ag->getApprovalToDoForAllUsers($user));






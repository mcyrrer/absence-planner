<?php
//require '../../../vendor/autoload.php';
require '../../../settings.inc';
require '../../../classes/DbHelper.php';
//$log = new Monolog\Logger('name');
//$log->pushHandler(new Monolog\Handler\StreamHandler('app.log', Monolog\Logger::WARNING));

$userA = array();
//$userA[] = 'matgus';
$userA[]='matgus2';
//$userA[]='matgus3';


$dbM = new DbHelper();
$con = $dbM->connectToMainDb();

if (isset($_REQUEST['date'])) {
    $dateUnsecure = $_REQUEST['date'];
    $date = $dbM->escape($con, $dateUnsecure);

}

if (isset($_REQUEST['from'], $_REQUEST['to'])) {
    $fromUnsecure = $_REQUEST['from'];
    $toUnsecure = $_REQUEST['to'];
    $from = $dbM->escape($con, $fromUnsecure);
    $to = $dbM->escape($con, $toUnsecure);
    unset($dateUnsecure);
    unset($toUnsecure);
}

if (isset($_REQUEST['state'])) {
    $stateUnsecure = $_REQUEST['state'];
    $state = $dbM->escape($con, $stateUnsecure);
    unset($stateUnsecure);
}

//$user = $userA[rand(0, count($userA))];
$user = $userA[0];


if (isset($date) && isset($state)) {
    $result = insertOneDayToDb($user, $date, $con, $state);
    if ($result == false) {
        header("HTTP/1.0 500 Internal Server Error");
    }
}
elseif (isset($from) && isset($to) && isset($state)) {
    $begin = new DateTime( $from );
    $end = new DateTime( $to );
    $end = $end->modify( '+1 day' );

    $interval = new DateInterval('P1D');
    $daterange = new DatePeriod($begin, $interval ,$end);

    foreach($daterange as $aDate){
        $result = insertOneDayToDb($user, $aDate->format("Ymd"), $con, $state);
        if ($result == false) {
            header("HTTP/1.0 500 Internal Server Error");
        }
    }
}
else
{
    header("HTTP/1.0 500 Internal Server Error");
    echo 'Parameter error';
}




mysqli_close($con);


/**
 * @param $user
 * @param $date
 * @param $con
 * @param $state
 * @return array
 */
function insertOneDayToDb($user, $date, $con, $state)
{
    $sqlCheck = "SELECT * FROM `vacation`.`events` WHERE user='$user' and eventDate='$date'";
    $result = mysqli_query($con, $sqlCheck);
    if (mysqli_num_rows($result) > 0) {
        echo '.' . $state . '.';
        if (strcmp($state, 'none') == 0) {
            $sql = "DELETE
            FROM
                events
            WHERE
                user = '$user'
            AND eventDate = '$date'";
        } else {
            $sql = "UPDATE
                        events
                    SET
                        type = '$state'
                    WHERE
                        user = '$user'
                        AND eventDate = '$date'
                        ";
        }

    } else {


        $sql = "INSERT
                 INTO
                    events
                    (
                        type,
                        user,
                        eventDate
                    )
                    VALUES
                    (
                        '$state',
                        '$user',
                        '$date'
                    ) ";
    }
    $result = mysqli_query($con, $sql);
    return $result;
}

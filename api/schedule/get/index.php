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
$user = $userA[0];


$dbM = new DbHelper();
$con = $dbM->connectToMainDb();

$sql="SELECT * FROM events WHERE user='$user'";
$result = mysqli_query($con, $sql);

if($result!=false)
{
    $i=0;
    $resultAllRowsArray=array();
    while ( $row = mysqli_fetch_array($result, MYSQLI_ASSOC) ) {
        $resultArray['allDay']='true';
        $resultArray['title']=$row['type'];
        $resultArray['id']=$row['id'];
        $resultArray['start']=$row['eventDate'].' 02:00:00';
        $resultArray['end']=$row['eventDate'].' 23:00:00';
        switch ($row['type']) {
            case 'vacation':
                $color = 'red';
                break;
            case 'course':
                $color = 'blue';
                break;
            case 'parental':
                $color = 'orange';
                break;
        }
        $resultArray['backgroundColor']=$color;
        $resultAllRowsArray[$i]=$resultArray;
        $i=$i+1;
    }
    echo json_encode($resultAllRowsArray,JSON_PRETTY_PRINT);
}

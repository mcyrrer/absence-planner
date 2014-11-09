<?php
require 'vendor/autoload.php';
require 'classes/HtmlIncludes.php';
require 'classes/DbHelper.php';
require 'classes/DateHelper.php';
require 'settings.inc';

$log = new Monolog\Logger('name');
$log->pushHandler(new Monolog\Handler\StreamHandler('app.log', Monolog\Logger::WARNING));
$dbM = new DbHelper();
$con = $dbM->connectToMainDb();

HtmlIncludes::header();

if (isset($_REQUEST['from'])) {
    $from = $dbM::escape($con, $_REQUEST['from']);
} else {
    $from = date('Y-m-d', time());
}
$begin = new DateTime($from);
$end = new DateTime($from);

if (isset($_REQUEST['range'])) {
    $range = $dbM::escape($con, $_REQUEST['range']);

    $end = $end->modify('+'.$range);
} else {
    $end = $end->modify('+2 month');
}



$interval = new DateInterval('P1D');
$daterange = new DatePeriod($begin, $interval, $end);
?>
<div class="explainBoxes">
    <span class="info vacation">Vacation</span>
    <span class="info course">Course</span>
    <span class="info parental">Parental leave</span>
    <span class="info none">Will work</span>
</div>
<div class="table-wrapper">
<?php
echo '<table class="mt"><tr>';
echo '<th class="mt">Name</th>';
echo '<th class="mt">Team</th>';
echo '<th class="mt">Manager</th>';

foreach ($daterange as $aDate) {
    if (DateHelper::isWeekend($aDate->format("Y-m-d")) == false) {
        echo '<th>' . $aDate->format("Y m d") . "</th>";
    }
}
echo '</tr>';
echo '<tr>';
echo '<th class="mt"></th>';
echo '<th class="mt"></th>';
echo '<th class="mt"></th>';

foreach ($daterange as $aDate) {
    if (DateHelper::isWeekend($aDate->format("Y-m-d")) == false) {
        echo '<th>' . $aDate->format("W") . "</th>";
    }
}

echo '</tr>';

$sqlUser = 'SELECT * FROM users';
$resultUser = mysqli_query($con, $sqlUser);


while ($rowUser = mysqli_fetch_array($resultUser, MYSQLI_ASSOC)) {
    $useSchedule = getUserScheduleFromDb($rowUser, $begin, $end, $con);

    echo '<tr>';
    echo '<td class="mt">' . $rowUser['fullname'];
    echo '<td class="mt">' . $rowUser['team'];
    echo '<td class="mt">' . $rowUser['manager'];
    echo '</td>';
    iteratAllDaysInView($daterange, $useSchedule);
    echo '</tr>';
}


echo '</tr>';
echo '</table>';
echo '</div>';
echo '<div>Possible get parameters to use on this page:<br>from=YYYY-MM-DD<br>range=n%20[day|week|month|year]<br>e.g: from=2014-01-01&range=1%20year</div>';
HtmlIncludes::footer();

/**
 * @param $rowUser
 * @param $begin
 * @param $end
 * @param $con
 * @return array|null
 */
function getUserScheduleFromDb($rowUser, $begin, $end, $con)
{
    $sqlUserSchedule = "SELECT * FROM events WHERE user='" . $rowUser['username'] . "' AND eventDate > '" . $begin->format("Y-m-d") . "' AND eventDate < '" . $end->format("Y-m-d") . "'";
    $resultUseSchedule = mysqli_query($con, $sqlUserSchedule);
    $useSchedule = mysqli_fetch_all($resultUseSchedule, MYSQLI_ASSOC);
    return $useSchedule;
}

/**
 * @param $daterange
 * @param $useSchedule
 */
function iteratAllDaysInView($daterange, $useSchedule)
{
    foreach ($daterange as $aDate) {
        if (DateHelper::isWeekend($aDate->format("Y-m-d")) == false) {
            $cellStr = '<td class="mt none fixedcell"></td>';
            $cellStr = iterateAllDaysInViewAndEchoTypeToCellIfDateExist($useSchedule, $aDate, $cellStr);
            echo $cellStr;
        }
    }
}

/**
 * @param $useSchedule
 * @param $aDate
 * @return string
 */
function iterateAllDaysInViewAndEchoTypeToCellIfDateExist($useSchedule, $aDate, $cellStr)
{
    foreach ($useSchedule as $aDay) {
        if (in_array($aDate->format("Y-m-d"), $aDay)) {
            $cellStr = '<td class="mt ' . $aDay['type'] . ' fixedcell"></td>';
        }
    }
    return $cellStr;
}

?>

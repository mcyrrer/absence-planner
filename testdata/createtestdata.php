<?php
require '../vendor/autoload.php';
require '../classes/HtmlIncludes.php';
require '../classes/DbHelper.php';
require '../classes/DateHelper.php';
require '../settings.inc';

$c = new createtestdata();
$c->createTestData(10, 100);


class createtestdata
{
    var $log;
    var $dbM;
    var $con;
    var $userNames;

    function __construct()
    {
        $this->log = new Monolog\Logger('name');
        $this->log->pushHandler(new Monolog\Handler\StreamHandler(LOGFILE, Monolog\Logger::DEBUG));

        $this->dbM = new DbHelper();
        $this->con = $this->dbM->connectToMainDb();
        $this->userNames = array();

    }

    public function createTestData($managecount, $userCount)
    {
        $this->createManager($managecount);
        $this->createUsers($userCount, $managecount);
        $this->createTestEvents();

    }



    private function createTestEvents()
    {
        $stateArray = array();
        $stateArray[] = "vacation";
        $stateArray[] = "course";
        $stateArray[] = "parental";
        $stateArray[] = "none";

        $begin = new DateTime(date('Y-m-d', time()));
        $end = new DateTime(date('Y-m-d', time()));
        $end = $end->modify('+3 month');

        $interval = new DateInterval('P1D');
        $daterange = new DatePeriod($begin, $interval, $end);

        foreach ($daterange as $aDate) {
            for ($i = 0; $i < 5; $i++) {
                $state = $stateArray[rand(0, count($stateArray) - 1)];
                $user = $this->userNames[rand(0, count($this->userNames) - 1)];
                $result = $this->insertOneDayToDbMocker($user, $aDate->format("Ymd"), $state);
                echo "Created event ".$user." ".$aDate->format("Ymd")." ".$state."\n";
            }
        }
    }

    private function createManager($numberOfManager)
    {
        for ($i = 0; $i <= $numberOfManager; $i++) {
            $sName = $this->generateRandomString(rand(5, 10));
            $lName = $this->generateRandomString(rand(5, 10));
            $name = $sName . ' ' . $lName;
            $uName = $this->generateRandomString(4);
            $this->createOneUser($i, $uName, $name, 'Mangers', '-');

            $sql = "INSERT
                INTO
                    mangers
                    (
                        manager_user_id,
                        accesslevel
                    )
                    VALUES
                    (
                        '" . $i . "',
                        1
                    )";
            $result = mysqli_query($this->con, $sql);
            if ($result == false) {
                $this->log->addError('could not execute sql: ' . $sql);
            }
            echo "Created manager ".$name."\n";
        }
    }

    private function createUsers($numberOfUses, $managecount)
    {

        $managecount = $managecount + 1;
        for ($i = 0; $i <= $numberOfUses; $i++) {
            $sName = $this->generateRandomString(rand(5, 10));
            $lName = $this->generateRandomString(rand(5, 10));
            $name = $sName . ' ' . $lName;
            $uName = $this->generateRandomString(4);
            $manager = rand(0, $managecount - 1);
            $team = "A-team";
            $id = $managecount + $i;

            $this->createOneUser($id, $uName, $name, $team, $manager);
            echo "Created user ".$name."\n";

        }

    }

    private function createOneUser($id, $username, $fullname, $team, $manager)
    {
        $this->userNames[] = $username;
        $sql = "INSERT
                INTO
                    users
                    (
                        id,
                        username,
                        fullname,
                        team,
                        manager
                    )
                    VALUES
                    (
                        " . $id . ",
                        '" . $username . "',
                        '" . $fullname . "',
                        '" . $team . "',
                        '" . $manager . "'
                    )";
        $result = mysqli_query($this->con, $sql);
        if ($result == false) {
            $this->log->addError('could not execute sql: ' . $sql);
        }
    }


    private function generateRandomString($length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $randomString;
    }


    /**
     * @param $user
     * @param $date
     * @param $con
     * @param $state
     * @return array
     */
    function insertOneDayToDbMocker($user, $date, $state)
    {
        $sqlCheck = "SELECT * FROM `vacation`.`events` WHERE user='$user' and eventDate='$date'";
        $result = mysqli_query($this->con, $sqlCheck);
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
        $result = mysqli_query($this->con, $sql);
        if ($result == false) {
            $this->log->addError('could not execute sql: ' . $sql);
        }
    }
}
<?php
require '../../vendor/autoload.php';
require '../../classes/HtmlIncludes.php';
require '../../classes/DbHelper.php';
require '../../classes/DateHelper.php';
require '../../settings.inc';

$c = new createtestdata();
$c->createTestData(100, 5000);


class createtestdata
{
    var $log;
    var $dbM;
    var $con;
    var $userNames;
    var $names;
    protected static $managerArray;


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
        $faker = Faker\Factory::create();
        echo 'Truncate all database tables';
        $this->names = $this->getNames();
        mysqli_query($this->con, 'truncate table users;');
        mysqli_query($this->con, 'truncate table teams;');
        mysqli_query($this->con, 'truncate table mangers;');
        mysqli_query($this->con, 'truncate table events;');
        $this->createManager($managecount,$faker);
        $this->createUsers($userCount, $managecount,$faker);
        $this->createTestEvents();
        $this->createOneUser(9999, 'testuser', 'Surname Lastname', '', '1');

    }


    private function createTestEvents()
    {
        $stateArray = array();
        $stateArray[] = "vacation";
        $stateArray[] = "course";
        $stateArray[] = "parental";

        $begin = new DateTime(date('Y-m-d', time()));
        $end = new DateTime(date('Y-m-d', time()));
        $end = $end->modify('+48 month');

        $interval = new DateInterval('P1D');
        $daterange = new DatePeriod($begin, $interval, $end);

        foreach ($daterange as $aDate) {
            for ($i = 0; $i < 50; $i++) {
                $state = $stateArray[rand(0, count($stateArray) - 1)];
                $user = $this->userNames[rand(0, count($this->userNames) - 1)];
                $result = $this->insertOneDayToDbMocker($user, $aDate->format("Ymd"), $state);
                echo "Created event " . $user . " " . $aDate->format("Ymd") . " " . $state . "\n";
            }
        }
    }

    private function createManager($numberOfManager, $faker)
    {

        for ($i = 0; $i <= $numberOfManager; $i++) {
            $lName = $faker->lastName;
            $fName = $faker->firstName;
            $uName = $faker->uuid;

            $name = $fName . ' ' . $lName . '(MANAGER)';
            $name = mysqli_real_escape_string($this->con,$name);
            $this->createOneUser($i, $uName, $name, 'Mangers', '-');

            self::$managerArray[] = $uName;

            $sql = "INSERT
                INTO
                    mangers
                    (
                        manager_user_id,
                        accesslevel
                    )
                    VALUES
                    (
                        '" . $uName . "',
                        1
                    )";
            $result = mysqli_query($this->con, $sql);
            if ($result == false) {
                $this->log->addError('could not execute sql: ' . $sql);
            }
            echo "Created manager " . $uName . "\n";
        }
    }


    private function createUsers($numberOfUses, $managecount, $faker)
    {

        $managecount = $managecount + 1;
        for ($i = 0; $i <= $numberOfUses; $i++) {

            $lName = $faker->lastName;
            $fName = $faker->firstName;
            $uName = $faker->uuid;
            $managerID = rand(0, count(self::$managerArray) - 1);
            $manager = self::$managerArray[$managerID];
            $name = $fName . ' ' . $lName . '(' . $manager . ')';

            $name = mysqli_real_escape_string($this->con,$name);

           // echo "Manager : " . $manager;
            $team = "A-team";
            $id = $i + 100;

            $this->createOneUser($id, $uName, $name, $team, $manager);
            echo "Created user " . $name . "\n";

        }

    }

    private function createOneUser($id, $username, $fullname, $team, $manager)
    {
        $this->userNames[] = $username;
        $sql = "INSERT
                INTO
                    users
                    (

                        username,
                        fullname,
                        team,
                        manager
                    )
                    VALUES
                    (

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

    private function generateRandomName()
    {
        $len = count($this->names);
        return $this->names[rand(0, $len - 1)];
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


    private function getNames()
    {
        $file = fopen("names.csv","r");
        $nameArray = array();
// for each line in the file, until EOF
        while( ($line = fgets($file)) !== false) {
            // split out the tab char:
            $beforeTab = explode( " ", $line)[0];
            // now, parse the CSV part
            $parsedCSV = str_getcsv( $beforeTab);
            $nameArray[] = $parsedCSV[0];
        }
    return $nameArray;
    }
}
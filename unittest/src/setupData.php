<?php

/**
 * Created by IntelliJ IDEA.
 * User: matgus
 * Date: 2014-11-09
 * Time: 20:10
 */
class setupData
{
    protected static $username;
//    protected  $username;
    protected static $manager;
    protected static $team;
    public $day1;
    public $day2;
    public $day3;


    public function insertScheduleToDb($dbh)
    {
        self::$username = self::generateRandomString(4);
        self::$team = self::generateRandomString(4);
        self::$manager = self::generateRandomString(4);

        //setup user
        $sql = "DELETE FROM events";
        mysqli_query($dbh, $sql);

        $sql = "DELETE FROM users";
        mysqli_query($dbh, $sql);

        $sql = "INSERT INTO users (username, fullname, team, manager ) VALUES ('" . self::$username . "', 'TEST USER', '" . self::$team . "', '" . self::$manager . "' )";
        mysqli_query($dbh, $sql);

        //setup testevent

        $this->generateDays();


        $sql = "INSERT INTO events ( type, user, eventDate ) VALUES ( 'vacation', '" . self::$username . "', '" . $this->day1 . "' ), ( 'course', '" . self::$username . "', '" . $this->day2 . "' ), ( 'parental', '" . self::$username . "', '" . $this->day3 . "' )";
        mysqli_query($dbh, $sql);

        //setup manager
        $sql = "INSERT INTO users (username, fullname, team, manager ) VALUES ('" . self::$manager . "', 'Test Manager', 'Management', '-' )";
        mysqli_query($dbh, $sql);

        return self::$username;
    }

    private function getNextDay(DateTime $date)
    {
        $date = $date->modify("+1 day");
        if ($this->isWeekend($date->format("Y-m-d")) == false) {
            return $date;
        } else {
            return self::getNextDay($date);
        }

    }

    private function isWeekend($date)
    {
        return (date('N', strtotime($date)) >= 6);
    }

    private static function generateRandomString($length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $randomString;
    }

    /**
     * @return mixed
     */
    public static function getManager()
    {
        return self::$manager;
    }

    /**
     * @return mixed
     */
    public static function getTeam()
    {
        return self::$team;
    }

    /**
     * @return mixed
     */
    public static function getUsername()
    {
        return self::$username;
    }

    public function generateDays()
    {
        $now = date('Y-m-d', time());
        $nowDT = new DateTime($now);

        $day1DT = self::getNextDay($nowDT);
        $this->day1 = $day1DT->format("Y-m-d");

        $day2DT = self::getNextDay($day1DT);
        $this->day2 = $day2DT->format("Y-m-d");

        $day3DT = self::getNextDay($day2DT);
        $this->day3 = $day3DT->format("Y-m-d");
    }

    /**
     * @return mixed
     */
    public function getDay1()
    {
        return str_replace('-',' ',$this->day1);
    }

    /**
     * @return mixed
     */
    public function getDay3()
    {
        return str_replace('-',' ',$this->day2);

    }

    /**
     * @return mixed
     */
    public function getDay2()
    {
        return str_replace('-',' ',$this->day3);

    }


} 
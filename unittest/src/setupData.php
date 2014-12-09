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
        $now = date('Y-m-d', time());

        $nowDT = new DateTime($now);
        $tomorrowDT = new DateTime($now);
        $dayaftertomorrowDT = new DateTime($now);

        $tomorrowDT = $tomorrowDT->modify('+1 day');
        $dayaftertomorrowDT = $dayaftertomorrowDT->modify('+2 day');

        $tomorrow = $tomorrowDT->format("Y-m-d");
        $dayaftertomorrow = $dayaftertomorrowDT->format("Y-m-d");



        $sql = "INSERT INTO events ( type, user, eventDate ) VALUES ( 'vacation', '" . self::$username . "', '".$now."' ), ( 'course', '" . self::$username . "', '".$tomorrow."' ), ( 'parental', '" . self::$username . "', '".$dayaftertomorrow."' )";
        mysqli_query($dbh, $sql);

        //setup manager
        $sql = "INSERT INTO users (username, fullname, team, manager ) VALUES ('" . self::$manager . "', 'Test Manager', 'Management', '-' )";
        mysqli_query($dbh, $sql);

        return self::$username;
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


} 
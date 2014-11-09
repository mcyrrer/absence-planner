<?php

/**
 * Created by IntelliJ IDEA.
 * User: matgus
 * Date: 2014-11-09
 * Time: 20:10
 */
class setupData
{

    public function insertScheduleToDb($dbh)
    {
       $username = self::generateRandomString(4);
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
                        'vacation',
                        '" . $username . "',
                        '2014-11-01'
                    ),
                    (
                        'course',
                        '" .$username . "',
                        '2014-11-02'
                    ),
                    (
                        'parental',
                        '" . $username . "',
                        '2014-11-03'
                    )
                     ";
        mysqli_query($dbh, $sql);
        return $username;
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
} 
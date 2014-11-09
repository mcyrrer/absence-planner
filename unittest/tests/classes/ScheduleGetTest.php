<?php
/**
 * Created by IntelliJ IDEA.
 * User: matgus
 * Date: 2014-11-09
 * Time: 14:06
 */

require_once '../vendor/autoload.php';
require_once '../settings.inc';
require_once '../classes/ScheduleGet.php';
require_once 'src/setupData.php';

class ScheduleGetTest extends PHPUnit_Framework_TestCase {

    protected static $dbh;
    protected static $username;

    public static function setUpBeforeClass()
    {
        self::$dbh = mysqli_connect(DB_HOST,DB_USER,DB_PASS,DB_NAME);
        $sd = new setupData();
        self::$username = $sd->insertScheduleToDb(self::$dbh);

    }

    public static function tearDownAfterClass()
    {
        $sql="DELETE FROM events WHERE user=".self::$username;
        mysqli_query(self::$dbh,$sql);

        mysqli_close(self::$dbh);
    }



    public function testGetUserSchedule()
    {
        $scheduleGet = new ScheduleGet();
        $scheduleArray = $scheduleGet->getUserSchedule(self::$username);

        $this->assertCount(3, $scheduleArray);

        foreach($scheduleArray as $aDaySchedule)
        {
            $this->assertEquals(self::$username,$aDaySchedule['user']);
        }
    }


}
 
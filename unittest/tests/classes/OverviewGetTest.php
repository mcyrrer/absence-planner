<?php
/**
 * Created by IntelliJ IDEA.
 * User: matgus
 * Date: 2014-11-09
 * Time: 14:06
 */

require_once '../vendor/autoload.php';
require_once '../settings.inc';
require_once '../classes/autoloader.php';
require_once 'src/setupData.php';

class OverviewGetTest extends PHPUnit_Framework_TestCase {

    protected static $dbh;
    protected static $username;
    public $sd;

    public static function setUpBeforeClass()
    {
        self::$dbh = mysqli_connect(DB_HOST,DB_USER,DB_PASS,DB_NAME);
        $sql="DELETE FROM events WHERE user=".self::$username;
        mysqli_query(self::$dbh,$sql);

        $sd = new setupData();
        self::$username = $sd->insertScheduleToDb(self::$dbh);

    }

    public static function tearDownAfterClass()
    {
        $sql="DELETE FROM events WHERE user=".self::$username;
        mysqli_query(self::$dbh,$sql);
        mysqli_close(self::$dbh);
    }



    public function testGetOverviewViewDateRanges()
    {
        $overviewGet = new OverviewGet();
        $overviewArray = $overviewGet->getOverviewView();
        //We have a range of 60 days but do not return weekends so 45 is ok.
        $this->assertGreaterThan(43, $overviewArray['dates']);

    }

    public function testGetOverviewViewScheduleCount()
    {
        $overviewGet = new OverviewGet();
        $overviewArray = $overviewGet->getOverviewView();
        $this->assertCount(3, $overviewArray['schedules']['TEST USER']['schedule']);
    }

    public function testGetOverviewViewUserKeys()
    {
        $overviewGet = new OverviewGet();
        $overviewArray = $overviewGet->getOverviewView();
        $user = $overviewArray['schedules']['TEST USER'];
        $this->assertCount(6, $user);
    }

    public function testGetOverviewViewScheduleKeys()
    {
        $sd = new setupData();
        $sd->generateDays();

        $overviewGet = new OverviewGet();
        $overviewArray = $overviewGet->getOverviewView();

        $scheduleItem = $overviewArray['schedules']['TEST USER']['schedule'][$sd->getDay1()];
        $this->assertArrayHasKey("type",$scheduleItem);
        $this->assertArrayHasKey("date",$scheduleItem);
        $this->assertArrayHasKey("approved",$scheduleItem);
        $this->assertArrayHasKey("id",$scheduleItem);
        $this->assertCount(4, $scheduleItem);
    }


}
 
<?php
/**
 * Created by IntelliJ IDEA.
 * User: matgus
 * Date: 2014-11-09
 * Time: 14:06
 */

require_once '../vendor/autoload.php';
require_once '../classes/autoloader.php';
require_once '../settings.inc';
require_once 'src/setupData.php';

class ScheduleSetTest extends PHPUnit_Framework_TestCase
{

    protected static $dbh;
    protected static $username;
    protected static $sd;

    const SINGLEDATE = "2014-01-01";
    const SINGLEDATE_INVALID = "2014-15-01";
    const DATEFROM = "2014-01-31";
    const DATETO = "2014-02-09";
    const EVENTTYPE_VACATION = "vacation";
    const EVENTTYPE_PARENTAL = "parental";
    const EVENTTYPE_NONE = "none";

    public function setUp()
    {
        self::$dbh = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        self::$sd = new setupData();
        self::$sd->insertScheduleToDb(self::$dbh);
        $sql = "DELETE FROM events WHERE user=" . self::$username;
        mysqli_query(self::$dbh, $sql);
        self::$username = microtime();
        //session_start();
        $_SESSION['user']=self::$username;
        $_SESSION['manager']=0;

        unset($_REQUEST);
    }

    public static function tearDownAfterClass()
    {
        $sql="DELETE FROM events WHERE user=".self::$username;
        mysqli_query(self::$dbh,$sql);
        mysqli_close(self::$dbh);
    }


    public function testSetUserScheduleOneDayInsert()
    {
        self::$username = microtime();
        $_SESSION['user']= self::$username;

        $_REQUEST["date"] = self::SINGLEDATE;
        $_REQUEST["state"] = self::EVENTTYPE_VACATION;


        $scheduleSet = new ScheduleSet();
        $scheduleSet->setUserSchedule(self::$username);


        $sql = "SELECT * FROM events WHERE user='" . self::$username . "'";
        $result = mysqli_query(self::$dbh, $sql);
        $allRows = mysqli_fetch_all($result,MYSQLI_ASSOC);

        $this->assertCount(1, $allRows);
        $aDay = $allRows[0];

        $this->assertEquals(self::EVENTTYPE_VACATION, $aDay['type']);
        $this->assertEquals(self::SINGLEDATE, $aDay['eventDate']);
    }

    public function testSetUserScheduleOneDayUpdate()
    {
        self::$username = microtime();
        $_SESSION['user']= self::$username;

        $_REQUEST["date"] = self::SINGLEDATE;
        $_REQUEST["state"] = self::EVENTTYPE_VACATION;

        $scheduleSet = new ScheduleSet();
        $scheduleSet->setUserSchedule(self::$username);

        $_REQUEST["state"] = self::EVENTTYPE_PARENTAL;
        $scheduleSet->setUserSchedule(self::$username);

        $sql = "SELECT * FROM events WHERE user='" . self::$username . "'";
        $result = mysqli_query(self::$dbh, $sql);
        $allRows = mysqli_fetch_all($result,MYSQLI_ASSOC);

        $this->assertCount(1, $allRows);
        $aDay = $allRows[0];

        $this->assertEquals(self::EVENTTYPE_PARENTAL, $aDay['type']);
        $this->assertEquals(self::SINGLEDATE, $aDay['eventDate']);
    }

    public function testSetUserScheduleOneDayDelete()
    {
        self::$username = microtime();
        $_SESSION['user']= self::$username;

        $_REQUEST["date"] = self::SINGLEDATE;
        $_REQUEST["state"] = self::EVENTTYPE_VACATION;

        $scheduleSet = new ScheduleSet();
        $scheduleSet->setUserSchedule(self::$username);

        $_REQUEST["state"] = self::EVENTTYPE_NONE;
        $scheduleSet->setUserSchedule(self::$username);

        $sql = "SELECT * FROM events WHERE user='" . self::$username . "'";
        $result = mysqli_query(self::$dbh, $sql);
        $allRows = mysqli_fetch_all($result,MYSQLI_ASSOC);

        $this->assertCount(0, $allRows);

    }

    public function testSetUserScheduleDateRange()
    {
        self::$username = microtime();
        $_SESSION['user']= self::$username;

        $_REQUEST["state"]= self::EVENTTYPE_VACATION;
        $_REQUEST["from"]= self::DATEFROM;
        $_REQUEST["to"]= self::DATETO;

        $scheduleSet = new ScheduleSet();
        $scheduleSet->setUserSchedule(self::$username);
        $sql = "SELECT * FROM events WHERE user='".self::$username."'";
        $result = mysqli_query(self::$dbh,$sql);
        $allRows = mysqli_fetch_all($result,MYSQLI_ASSOC);


        $this->assertCount(10, $allRows);
        $aDay = $allRows[0];
        $this->assertEquals(self::EVENTTYPE_VACATION,$aDay['type']);
        $this->assertEquals(self::DATEFROM,$aDay['eventDate']);
    }

    public function testSetUserScheduleNoState()
    {
        self::$username = microtime();
        $_SESSION['user']= self::$username;

        $_REQUEST["from"]= self::DATEFROM;
        $_REQUEST["to"]= self::DATETO;

        $scheduleSet = new ScheduleSet();
        $result = $scheduleSet->setUserSchedule(self::$username);

        $this->assertEquals(TEST_EVENT_PARAM_STATE_NOT_SET,$result);

        $sql = "SELECT * FROM events WHERE user='".self::$username."'";
        $result = mysqli_query(self::$dbh,$sql);
        $allRows = mysqli_fetch_all($result,MYSQLI_ASSOC);


        $this->assertCount(0, $allRows);
    }

    public function testSetUserScheduleNoDate()
    {
        self::$username = microtime();
        $_SESSION['user']= self::$username;

        $_REQUEST["state"]= self::EVENTTYPE_VACATION;

        $scheduleSet = new ScheduleSet();
        $result = $scheduleSet->setUserSchedule(self::$username);

        $this->assertEquals(TEST_EVENT_PARAM_DATE_NOT_SET,$result);

        $sql = "SELECT * FROM events WHERE user='".self::$username."'";
        $result = mysqli_query(self::$dbh,$sql);
        $allRows = mysqli_fetch_all($result,MYSQLI_ASSOC);


        $this->assertCount(0, $allRows);
    }

    public function testSetUserScheduleOneDayInsertStateNone()
    {
        self::$username = microtime();
        $_SESSION['user']= self::$username;

        $_REQUEST["date"] = self::SINGLEDATE;
        $_REQUEST["state"] = self::EVENTTYPE_NONE;

        $scheduleSet = new ScheduleSet();
        $scheduleSet->setUserSchedule(self::$username);

        $sql = "SELECT * FROM events WHERE user='" . self::$username . "'";
        $result = mysqli_query(self::$dbh, $sql);
        $allRows = mysqli_fetch_all($result,MYSQLI_ASSOC);

        $this->assertCount(0, $allRows);

    }

    public function testSetUserScheduleOneDayInsertInvalidDate()
    {
        self::$username = microtime();
        $_SESSION['user']= self::$username;

        $_REQUEST["date"] = self::SINGLEDATE_INVALID;
        $_REQUEST["state"] = self::EVENTTYPE_VACATION;

        $scheduleSet = new ScheduleSet();
        $scheduleSet->setUserSchedule(self::$username);

        $sql = "SELECT * FROM events WHERE user='" . self::$username . "'";
        $result = mysqli_query(self::$dbh, $sql);
        $allRows = mysqli_fetch_all($result,MYSQLI_ASSOC);

        $this->assertCount(0, $allRows);

    }

}
 
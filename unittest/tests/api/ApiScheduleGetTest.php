<?php

/**
 * Created by IntelliJ IDEA.
 * User: matgus
 * Date: 2014-11-09
 * Time: 20:04
 */
require_once '../vendor/autoload.php';
require_once '../settings.inc';
require_once BASEPATH.'/classes/Logging.php';
require_once 'src/setupData.php';
require_once 'src/Http_Executor.php';


class ApiScheduleGet extends PHPUnit_Framework_TestCase
{
    protected static $dbh;
    protected static $username;
    protected static $response;
    protected static $logger;


    public static function setUpBeforeClass()
    {
        $l = new Logging();
        self::$logger = $l->getLoggerTest();
        self::$dbh = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        $sd = new setupData();
        self::$username = $sd->insertScheduleToDb(self::$dbh);
        self::$response = Http_Executor::http_get(WWWLOCATION . '/api/schedule/get/index.php?user=' . self::$username);
    }

    public static function tearDownAfterClass()
    {
        $sql = "DELETE FROM events WHERE user=" . self::$username;
        mysqli_query(self::$dbh, $sql);
        mysqli_close(self::$dbh);
    }


    public function testApiScheduleGetIsJson()
    {

        $scheduleArray = json_decode(self::$response, true);

        $this->assertJson(self::$response);
    }

    public function testApiScheduleGetHasCorrectNumberOfRecords()
    {

        $scheduleArray = json_decode(self::$response, true);
        //self::$logger->addDebug(self::$response,array(__CLASS__,__FUNCTION__,__FILE__,__LINE__));
        $this->assertCount(3, $scheduleArray);
    }


    public function testApiScheduleGetHasCorrectNumberKeysInRecord()
    {
        $scheduleArray = json_decode(self::$response, true);
        foreach ($scheduleArray as $aSchedule) {
            $this->assertCount(8, $aSchedule);
        }
    }

    public function testApiScheduleGetHasCorrectKeysInRecord()
    {
        $scheduleArray = json_decode(self::$response, true);
        foreach ($scheduleArray as $aSchedule) {
            $this->assertArrayHasKey('allDay', $aSchedule);
            $this->assertArrayHasKey('title', $aSchedule);
            $this->assertArrayHasKey('id', $aSchedule);
            $this->assertArrayHasKey('user', $aSchedule);
            $this->assertArrayHasKey('manager', $aSchedule);
            $this->assertArrayHasKey('start', $aSchedule);
            $this->assertArrayHasKey('end', $aSchedule);
            $this->assertArrayHasKey('backgroundColor', $aSchedule);
            $this->assertArrayHasKey('approvalStatus', $aSchedule);
        }
    }

}
 
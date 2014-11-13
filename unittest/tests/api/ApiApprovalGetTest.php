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


class ApiApprovalGetTest extends PHPUnit_Framework_TestCase
{
    protected static $dbh;
    protected static $username;
    protected static $response;
    protected static $testData;
    protected static $logger;
    protected static $manager;


    public static function setUpBeforeClass()
    {
        self::$dbh = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        self::$testData = new setupData();
        self::$username = self::$testData->insertScheduleToDb(self::$dbh);
        self::$manager = self::$testData->getManager();
        self::$response = Http_Executor::http_get(WWWLOCATION . '/api/approval/get/index.php?user=' . self::$username.'&manager='.self::$manager);
    }

    public static function tearDownAfterClass()
    {
        $sql = "DELETE FROM events WHERE user=" . self::$username;
        mysqli_query(self::$dbh, $sql);
        mysqli_close(self::$dbh);
    }


    public function testApiApprovalGetIsJson()
    {

        $scheduleArray = json_decode(self::$response, true);
        $this->assertJson(self::$response);
    }

    public function testApiApprovalGetHasCorrectNumberOfRecords()
    {

        $scheduleArray = json_decode(self::$response, true);
        $this->assertCount(3, $scheduleArray);
    }


    public function testApiApprovalGetHasCorrectNumberKeysInRecord()
    {
        $scheduleArray = json_decode(self::$response, true);
        foreach ($scheduleArray as $aSchedule) {
            $this->assertCount(9, $aSchedule);
        }
    }

    public function testApiApprovalGetHasCorrectKeysInRecord()
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
 
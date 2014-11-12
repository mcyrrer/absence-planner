<?php
require_once '../settings.inc';
require_once BASEPATH . '/vendor/autoload.php';
require_once BASEPATH . '../classes/ApprovalGet.php';
require_once BASEPATH . '/unittest/src/setupData.php';

/**
 * Created by IntelliJ IDEA.
 * User: matgus
 * Date: 2014-11-11
 * Time: 22:10
 */
class ApprovalGetTest extends PHPUnit_Framework_TestCase
{
    protected static $dbh;
    protected static $username;
    protected static $testData;

    public static function setUpBeforeClass()
    {
        self::$dbh = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        self::$testData = new setupData();

        self::$username = self::$testData->insertScheduleToDb(self::$dbh);
    }

    public static function tearDownAfterClass()
    {
        $sql = "DELETE FROM events WHERE user=" . self::$username;
        mysqli_query(self::$dbh, $sql);
        mysqli_close(self::$dbh);
    }


    public function testGetApprovalToDoForSingleUser()
    {
        $ag = new ApprovalGet();
        $result=$ag->getApprovalToDoForSingleUser(self::$username,self::$testData->getManager());
        foreach($result as $aRow)
        {
            $this->assertEquals(self::$username,$aRow['user']);
            $this->assertEquals(self::$testData->getManager(),$aRow['manager']);
        }
        $this->assertCount(3,$result);

    }

    public function testGetApprovalToDoUserList()
    {
        $this->assertFalse(false);
    }
}
 
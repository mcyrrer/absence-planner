<?php
/**
 * Created by IntelliJ IDEA.
 * User: matgus
 * Date: 2014-11-09
 * Time: 19:23
 */
require_once '../vendor/autoload.php';
require_once '../settings.inc';
require_once '../classes/DbHelper.php';

class DbHelperTest extends PHPUnit_Framework_TestCase {
    public function testConnectToMainDb()
    {
        $dbm = new DbHelper();
        $con = $dbm->connectToMainDb();
        $this->assertTrue(mysqli_ping($con));
    }

    public function testEscape()
    {
        $dbm = new DbHelper();
        $con = $dbm->connectToMainDb();
        $stringToBeEscaped = 'TestString'.PHP_EOL.'TestString';
        $escapedString = $dbm->escape($con,$stringToBeEscaped);

//        $this->assertEquals('TestString\r\nTestString',$escapedString);
        $this->assertEquals('TestString\nTestString',$escapedString);
    }

}
 
<?php
/**
 * Created by IntelliJ IDEA.
 * User: matgus
 * Date: 2014-11-09
 * Time: 19:41
 */
require_once '../vendor/autoload.php';
require_once '../settings.inc';
require_once '../classes/DateHelper.php';

class DateHelperTest extends PHPUnit_Framework_TestCase {

    public function testIsWeekendTrue()
    {
        $this->assertTrue(DateHelper::isWeekend("2014-11-09"));
    }

    public function testIsWeekendFalse()
    {
        $this->assertFalse(DateHelper::isWeekend("2014-11-10"));
    }
}
 
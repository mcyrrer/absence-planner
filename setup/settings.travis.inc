<?php
session_start();

define('DB_HOST', '127.0.0.1');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'vacation');


define('LOGFILE', $_ENV["TRAVIS_BUILD_DIR"].'\application.log');
define('LOGFILEUNITTEST', $_ENV["TRAVIS_BUILD_DIR"].'\unittest\unittest.log');
define('BASEPATH', $_ENV["TRAVIS_BUILD_DIR"]);

define('WWWLOCATION', 'http://127.0.0.1/vacation');

define('UNIT_TEST_SERVER', true);

if(isset($_SERVER['AUTHENTICATE_SAMACCOUNTNAME']))
{
    $user = $_SERVER['AUTHENTICATE_SAMACCOUNTNAME'];
}
else
{
    $user = 'testuser';
}

const TEST_EVENT_PARAM_STATE_NOT_SET = 1;
const TEST_EVENT_PARAM_DATE_NOT_SET = 2;
const TEST_EVENT_PARAM_DATE_INVALID = 3;
const TEST_INVALID_SQL = 4;

<?php
//session_name('VACATIONSESSIONID');
//session_start();

define('DB_HOST', '127.0.0.1');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'vacation');


//define('LOGFILE', $_ENV["TRAVIS_BUILD_DIR"].'\application.log');
define('LOGFILE', '/home/travis/build/mcyrrer/absence-planner/application.log');
define('LOGFILEUNITTEST', '/home/travis/build/mcyrrer/absence-planner/unittest/unittest.log');
define('BASEPATH', '/home/travis/build/mcyrrer/absence-planner');

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

/*SQL debug, 1=on, 0=off*/
const SQL_PROFILING = 1;

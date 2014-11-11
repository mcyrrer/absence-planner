<?php
require_once BASEPATH . '/vendor/autoload.php';


/**
 * Class to help out with common mysql tasks
 */
class Logging
{
    private $logger;

    function __construct()
    {
    }

    /**
     * Creates a Monolog logger
     * @return \Monolog\Logger
     */
    public function getLogger()
    {
        $log = new Monolog\Logger('name');
        $log->pushHandler(new Monolog\Handler\StreamHandler(LOGFILE, Monolog\Logger::DEBUG));
        return $log;
    }

    /**
     * Creates a Monolog logger
     * @return \Monolog\Logger
     */
    // @codeCoverageIgnoreStart
    public function getLoggerTest()
    {
        $log = new Monolog\Logger('name');
        $log->pushHandler(new Monolog\Handler\StreamHandler(LOGFILEUNITTEST, Monolog\Logger::DEBUG));
        return $log;
    }
// @codeCoverageIgnoreEnd

}

?>
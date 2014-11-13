<?php
require_once BASEPATH . '/vendor/autoload.php';
require_once BASEPATH . '/classes/DbHelper.php';
require_once BASEPATH . '/classes/Logging.php';


class UserSession
{
    static private $logger;

    function __construct()
    {
        self::$logger = (new Logging())->getLogger();
        $this->userSessionStart();
    }

    private function userSessionStart()
    {
        session_start();

        if (isset($_REQUEST['logout'])) {
            if (isset($_SESSION['user'])) {
                echo "You have been logout<br>";
                self::$logger->addInfo($_SESSION['user'] . ' logged out');
                session_destroy();
                session_start();
            }
        }

        if (!array_key_exists('user', $_SESSION)) {
            self::$logger->addDebug('User already have an active session');
            $_SESSION['user'] ="abc";
        } elseif (isset($_SERVER['AUTHENTICATE_SAMACCOUNTNAME'])) {
            $_SESSION['user'] = $_SERVER['AUTHENTICATE_SAMACCOUNTNAME'];
            self::$logger->addDebug($_SESSION['user'] . ' logged in via SAMACCOUNTNAME', array(__FILE__, __LINE__));
            $this->checkIfManagerAndSetupSession($_SESSION['user']);
        } else {
            self::$logger->addDebug('User '.$_SESSION['user'].' already have an active session');
        }
    }

    private function checkIfManagerAndSetupSession($user)
    {
        $dbM = new DbHelper();
        $con = $dbM->connectToMainDb();

        $sql = "SELECT * FROM mangers WHERE manager_user_id='" . $user . "'";
        $result = mysqli_query($con, $sql);
        if (!$result) {
            if (mysqli_num_rows($result) == 1) {
                $managerInfo = mysqli_fetch_assoc($result);
                $_SESSION['manager'] = true;
                $_SESSION['managerlevel'] = $managerInfo['accesslevel'];
            }
        } else {
            self::$logger->addDebug($user . ' is not a manager', array(__FILE__, __LINE__));

        }
    }


}

?>
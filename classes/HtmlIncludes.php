<?php

class HtmlIncludes
{

    function __construct()
    {
    }

// @codeCoverageIgnoreStart
    public static function header()
    {
        echo '
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="js/fullcalendar-2.1.1/fullcalendar.css"/>
    <link rel="stylesheet" href="vacation.css"/>
    <link rel="stylesheet" href="js/jquery-ui-1.11.2/jquery-ui.min.css"/>
    <script src="js/jquery-1.11.1.min.js"></script>
    <script src="js/jquery-ui-1.11.2/jquery-ui.min.js"></script>
    <script src="js/moment.js"></script>
    <script src="js/fullcalendar-2.1.1/fullcalendar.min.js"></script>
    <script src="js/vacation.js"></script>

    <link href="js/Fixed-Header-Table/demo/css/960.css" rel="stylesheet" media="screen" />
    <link href="js/Fixed-Header-Table/css/defaultTheme.css" rel="stylesheet" media="screen" />
    <link href="js/Fixed-Header-Table/demo/css/myTheme.css" rel="stylesheet" media="screen" />
    <script src="js/Fixed-Header-Table/jquery.fixedheadertable.js"></script>

    <meta charset="UTF-8">
    <title>Vacation sheet</title>
</head>

<body>
<div class="header"><img src="pictures/logo.png"> Vacation sheet (not Google drive....!!!!)</div>
<div class="menu">[<a href="setup/testusers.php">SETUSER</a>]['.$_SESSION['user'].' - '.$_SESSION['manager'].'] <a href="batch.php">Batch add</a> | <a href="index.php">Single day add</a>  | <a href="overview.php">Overview</a> | <a href="approval.php">Approval manager</a></div>
    ';
    }


    public static function footer()
    {
        echo '
    </body>

</html>
';
    }
    // @codeCoverageIgnoreEnd

}

?>
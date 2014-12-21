<?php
require 'vendor/autoload.php';
require 'classes/autoloader.php';
require 'settings.inc';

$us = new UserSession();
$log = new Monolog\Logger('name');
$log->pushHandler(new Monolog\Handler\StreamHandler('app.log', Monolog\Logger::WARNING));


HtmlIncludes::header();
?>
    <script>
        $(function () {
            $(document).tooltip();
        });
        $(document).ready(function () {
            getApprovalToDo();
        });
    </script>
    <div class="container_12">
        <div class="grid_11">
            <h1>Approval manager</h1>
        </div>
        <div class="box">

            <div class="center"><img src="pictures/loading.gif"></div>
        </div>
    </div>




<?php
HtmlIncludes::footer();
?>
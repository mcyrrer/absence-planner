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


    <div class="box">


    </div>


<?php
HtmlIncludes::footer();
?>
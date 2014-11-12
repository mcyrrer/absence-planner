<?php
require 'vendor/autoload.php';
require 'classes/HtmlIncludes.php';

$log = new Monolog\Logger('name');
$log->pushHandler(new Monolog\Handler\StreamHandler('app.log', Monolog\Logger::WARNING));


HtmlIncludes::header();
?>



<div class="box">


</div>


<?php
HtmlIncludes::footer();
?>
<?php
require 'vendor/autoload.php';
require 'classes/HtmlIncludes.php';

$log = new Monolog\Logger('name');
$log->pushHandler(new Monolog\Handler\StreamHandler('app.log', Monolog\Logger::WARNING));


HtmlIncludes::header();
?>



<div class="box">
    <h2>Day by day entry</h2>

    <div class="explainBoxes">
        <span class="info vacation">Vacation</span>
        <span class="info course">Course</span>
        <span class="info parental">Parental leave</span>
        <span class="info none">Will work</span>
        <span class="info">Click on a date several times to change absence reason</span>
    </div>


    <div id='calendar'></div>
</div>


<?php
HtmlIncludes::footer();
?>
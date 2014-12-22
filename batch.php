<?php
require 'vendor/autoload.php';
require 'classes/autoloader.php';
require 'settings.inc';
new UserSession();

$log = new Monolog\Logger('name');
$log->pushHandler(new Monolog\Handler\StreamHandler('app.log', Monolog\Logger::WARNING));


HtmlIncludes::header();
?>


    <div class="actionBox">
        <div class="wizard">
            <form id="batchForm">


            <p>I want to have a few days of due to</p>
            <select id="state" name="state">
                <option value="vacation">Vacation</option>
                <option value="course">Course</option>
                <option value="parental">Parental/ leave</option>
                <option value="none">Clear dates</option>
            </select></p>
            <p>

            </p>

            <p>
                <label for="from">from</label><input type="text" id="from" name="from" size="10"><label for="to">to</label>                <input type="text" id="to" name="to" size="10">


            </p>
            <p class="left">
                Every:<br>
                <input type="checkbox" name="Monday" value="1" checked>Monday
                <input type="checkbox" name="Tuesday" value="1" checked>Tuesday
                <input type="checkbox" name="Wednesday" value="1" checked>Wednesday
                <input type="checkbox" name="Thursday" value="1" checked>Thursday
                <input type="checkbox" name="Friday" value="1" checked>Friday
                <input type="checkbox" name="Saturday" value="1" checked>Saturday
                <input type="checkbox" name="Sunday" value="1" checked>Sunday
            </p>
            </form>

            <p>
                <button id="addToCalendar">Add to calendar</button>
            <div id="loadingBatchCalendar"></div>
            </p>
        </div>
    </div>


<?php
HtmlIncludes::footer();
?>
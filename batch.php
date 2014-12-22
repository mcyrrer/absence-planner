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
            <p>I want to have a few days of due to</p>
            <select id="type">
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

            <p>
            </p>

            <p>
            </p>

            <p class="left">
                Every:<br>
                <input type="checkbox" id="Monday" checked>Monday
                <input type="checkbox" id="Tuesday" checked>Tuesday
                <input type="checkbox" id="Wednesday" checked>Wednesday
                <input type="checkbox" id="Thursday" checked>Thursday
                <input type="checkbox" id="Friday" checked>Friday
                <input type="checkbox" id="Saturday" checked>Saturday
                <input type="checkbox" id="Sunday" checked>Sunday
            </p>

            <p>
                <button id="addToCalendar">Add to calendar</button>
            <div id="loadingBatchCalendar"></div>
            </p>
        </div>
    </div>


<?php
HtmlIncludes::footer();
?>
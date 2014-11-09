<?php
require 'vendor/autoload.php';
require 'classes/HtmlIncludes.php';

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
                <option value="none">Clean dates</option>
            </select></p>
            <p>
                <label for="from">from</label>
            </p>
            <p>
                <input type="text" id="from" name="from" size="10" >
            </p>

            <p>
                <label for="to">to</label>
            </p>
            <p>
                <input type="text" id="to" name="to" size="10">
            </p>
            <p>
                <button id="addToCalendar">Add to calendar</button>
            </p>
        </div>
    </div>


<?php
HtmlIncludes::footer();
?>
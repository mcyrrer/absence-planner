<?php
require 'vendor/autoload.php';
require 'classes/autoloader.php';
require 'settings.inc';
new UserSession();

$log = new Monolog\Logger('name');
$log->pushHandler(new Monolog\Handler\StreamHandler('app.log', Monolog\Logger::WARNING));


HtmlIncludes::header();
?>

    <script>
        $(document).ready(function () {

            $.ajax({
                url: "api/overview/get/index.php?json",
                cache: false
            })
                .done(function (data) {
                    var tableHtml = generateOverviewTable(data);

                    $("#tblr").html(tableHtml);
                    $('#myTable01').fixedHeaderTable({ footer: true,
                        cloneHeadToFoot: true,
                        altClass: 'odd',
                        autoShow: true,
                        fixedColumns: 3
                    });
                    $('#myTable01').fixedHeaderTable('show');

                });
        });

        function generateOverviewTable(scheduleJson) {
            var html = "";
            var structure = $.parseJSON(scheduleJson);
            var dates = structure['dates'];
            var schedules = structure['schedules'];
            html += '<table class="fancyTable" id="myTable01">';
            html += '<thead>';
            html += '<tr>';
            html += '<th class="thItem">AName</th>';
            html += '<th class="thItem">Team</th>';
            html += '<th class="thItem">Manager</th>';
            $.each(dates, function (index, value) {
                html += '<th>' + value + '</th>';
            });
            html += '</thead>';
            html += '<tbody>';
            $.each(schedules, function (key, value) {

                html += '<tr>';
                html += '<td class="thItem">' + value['fullname'] + '</td>';
                html += '<td class="thItem">' + value['team'] + '</td>';
                html += '<td class="thItem">' + value['manager'] + '</td>';
                $.each(dates, function (index, day) {
                    var schedule = value['schedule'];
                    var d = schedule.hasOwnProperty(day);
                    if (schedule.hasOwnProperty(day)) {
                        var scheduleDay = value['schedule'][day];
                        var type = scheduleDay['type'];
                        var approved = scheduleDay['approved'];
                        if (approved == 1)
                            var status = "OK";
                        else
                            var status = "";
                        var id = scheduleDay['id'];
                        html += '<td class="' + type + '">' + status + '</td>';
                    }
                    else {
                        html += '<td></td>';
                    }
                });

                html += '</tr>';
            });

            html += '</tbody>';
            html += '</table>';
            return html;
        }


    </script>

    <div class="box">

        <div class="container_12">
            <div class="grid_11">
                <h1 id="test">CLICK ME</h1>
            </div>
            <div id="tblr" class="grid_11 height800">
            </div>
            <div class="clear"></div>
        </div>


    </div>


<?php
HtmlIncludes::footer();
?>
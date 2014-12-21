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
                    $('#myTable01').fixedHeaderTable({
                        footer: true,
                        cloneHeadToFoot: true,
                        altClass: 'odd',
                        autoShow: true,
                        fixedColumns: 3
                    });
                    $('#myTable01').fixedHeaderTable('show');

                });
        });

        function generateOverviewTable(scheduleJson) {
            var firstDate = "";
            var lastDate = ""
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
            firstDate = dates[0].replace(" ", "-").replace(" ", "-");
            lastDate = dates[dates.length - 1].replace(" ", "-").replace(" ", "-");
            var jsCodePrev = 'onClick="getPrevPage(\'' + firstDate + '\')";';
            var jsCodeNext = 'onClick="getNextPage(\'' + lastDate + '\')";';

            $('#prev').html("<span class='link' " + jsCodePrev + "><img src='pictures/14-32.png'></span>");
            $('#next').html("<span class='link' " + jsCodeNext + "><img src='pictures/12-32.png'></span>");
            $.each(dates, function (index, value) {
                html += '<th>' + value + '</th>';
            });
            html += '</thead>';
            html += '<tbody>';

            $.each(schedules, function (key, value) {

                html += '<tr>';
                html += '<td class="thItem">' + value['fullname'] + '</td>';
                var jsCodeTeam = 'onClick="getTeamOverview(\'' + value['team'] + '\')";';
                html += '<td class="thItem"><span class="link" ' + jsCodeTeam + '>' + value['team'] + '</span></td>';
                var jsCodeManager = 'onClick="getManagerOverview(\'' + value['manager'] + '\')";';
                html += '<td class="thItem"><span class="link" ' + jsCodeManager + '>' + value['manager'] + '</span></td>';

                $.each(dates, function (index, day) {
                    var schedule = value['schedule'];
                    var d = schedule.hasOwnProperty(day);
                    if (schedule.hasOwnProperty(day)) {
                        var scheduleDay = value['schedule'][day];
                        var type = scheduleDay['type'];
                        var approved = scheduleDay['approved'];
                        if (approved == 1)
                            var status = "OK";
                        else if (approved == -1)
                            var status = "NOK";
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

        function getPrevPage(from) {
            $("#tblr").html('<div class="center"><img src="pictures/loading.gif"></div>');

            $.ajax({
                url: "api/overview/get/index.php?json",
                data: {
                    fromend: from
                },
                cache: false
            })
                .done(function (data) {
                    var tableHtml = generateOverviewTable(data);

                    $("#tblr").html(tableHtml);
                    $('#myTable01').fixedHeaderTable({
                        footer: true,
                        cloneHeadToFoot: true,
                        altClass: 'odd',
                        autoShow: true,
                        fixedColumns: 3
                    });
                    $('#myTable01').fixedHeaderTable('show');

                });
        }

        function getNextPage(from) {
            $("#tblr").html('<div class="center"><img src="pictures/loading.gif"></div>');

            $.ajax({
                url: "api/overview/get/index.php?json",
                data: {
                    from: from
                },
                cache: false
            })
                .done(function (data) {
                    var tableHtml = generateOverviewTable(data);

                    $("#tblr").html(tableHtml);
                    $('#myTable01').fixedHeaderTable({
                        footer: true,
                        cloneHeadToFoot: true,
                        altClass: 'odd',
                        autoShow: true,
                        fixedColumns: 3
                    });
                    $('#myTable01').fixedHeaderTable('show');

                });
        }

        function getTeamOverview(team) {
            $("#tblr").html('<div class="center"><img src="pictures/loading.gif"></div>');

            $.ajax({
                url: "api/overview/get/index.php?json",
                data: {
                    type: "TEAM",
                    typedata: team
                },
                cache: false
            })
                .done(function (data) {
                    var tableHtml = generateOverviewTable(data);

                    $("#tblr").html(tableHtml);
                    $('#myTable01').fixedHeaderTable({
                        footer: true,
                        cloneHeadToFoot: true,
                        altClass: 'odd',
                        autoShow: true,
                        fixedColumns: 3
                    });
                    $('#myTable01').fixedHeaderTable('show');

                });
        }
        function getManagerOverview(manager) {
            $("#tblr").html('<div class="center"><img src="pictures/loading.gif"></div>');

            $.ajax({
                url: "api/overview/get/index.php?json",
                data: {
                    type: "MANAGER",
                    typedata: manager
                },
                cache: false
            })
                .done(function (data) {
                    $("#tblr").html('');

                });
        }


    </script>

    <div class="box">

        <div class="container_12">
            <div class="grid_11">
                <h1>Overview</h1>
                <span><span id="prev"></span><span id="next"></span></span>
            </div>
            <div id="tblr" class="grid_11 height800">
                <div class="center"><img src="pictures/loading.gif"></div>
            </div>
            <div class="clear"></div>
        </div>


    </div>


<?php
HtmlIncludes::footer();
?>
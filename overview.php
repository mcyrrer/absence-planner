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
        var offset = 0;
        var typeOrRequest = "ALL";
        var typeData = "";

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
                    hideLoader();

                });
        });

        function generateOverviewTable(scheduleJson) {
            var offsetNext = offset + 1;
            var offsetPrev = offset - 1;
            var offsetCurrent = offset;

            var firstDate = "";
            var lastDate = ""
            var html = "";
            var structure = $.parseJSON(scheduleJson);
            var dates = structure['dates'];
            var schedules = structure['schedules'];
            html += '<table class="fancyTable" id="myTable01">';
            html += '<thead>';
            html += '<tr>';
            html += '<th class="thItem">Name</th>';
            html += '<th class="thItem">Team</th>';
            html += '<th class="thItem">Manager</th>';
            firstDate = dates[0].replace(" ", "-").replace(" ", "-");
            lastDate = dates[dates.length - 1].replace(" ", "-").replace(" ", "-");
            var jsCodePrev = 'onClick="getPrevPage(\'' + firstDate + '\', ' + offsetCurrent + ')";';
            var jsCodeNext = 'onClick="getNextPage(\'' + lastDate + '\', ' + offsetCurrent + ')";';

            $('#prev').html("<span class='link' " + jsCodePrev + "><img src='pictures/14-32.png'></span>");
            $('#next').html("<span class='link' " + jsCodeNext + "><img src='pictures/12-32.png'></span>");

            $('#nextGroup').html("");
            $('#prevGroup').html("");
            var jsCodePrevGroup = 'onClick="getPrevPageGroup(\'' + firstDate + '\', ' + offsetPrev + ')";';
            var jsCodeNextGroup = 'onClick="getNextPageGroup(\'' + firstDate + '\', ' + offsetNext + ')";';
            if (Object.keys(schedules).length > 0) {
                $('#nextGroup').html("<span class='link' " + jsCodeNextGroup + "><img src='pictures/20-32.png'></span>");
            }
            else
            {
                $('#nextGroup').html("<span class='link' " + jsCodeNextGroup + "><img src='pictures/transparent-32.png'></span>");

            }
            if (parseInt(offsetCurrent) > 0) {
                $('#prevGroup').html("<span class='link' " + jsCodePrevGroup + "><img src='pictures/9-32.png'></span>");
            }


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
                var jsCodeManager = 'onClick="getManagerOverview(\'' + value['managerusername'] + '\')";';
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

        function getPrevPageGroup(from, lOffset) {

            offset = lOffset;

            getNextPage(from, offset);
        }

        function getNextPageGroup(from, lOffset) {
            offset = offset + 1;
            getNextPage(from, offset);
        }

        function getPrevPage(from, offset) {

            showLoader();
//            $("#tblr").html('<div class="center"><img src="pictures/loading.gif"></div>');

            $.ajax({
                url: "api/overview/get/index.php?json",
                data: {
                    fromend: from,
                    offset: offset,
                    type: typeOrRequest,
                    typedata: typeData
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
                    hideLoader();

                });
        }

        function getNextPage(from, offset) {
            showLoader();
//            $("#tblr").html('<div class="center"><img src="pictures/loading.gif"></div>');

            $.ajax({
                url: "api/overview/get/index.php?json",
                data: {
                    from: from,
                    offset: offset,
                    type: typeOrRequest,
                    typedata: typeData
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
                    hideLoader();

                });
        }

        function showLoader()
        {
            $("#loader").show();
            $("#loader").html('<div class="center"><img class="imagecenter" src="pictures/loader_large.gif"></div>');
        }

        function hideLoader()
        {
            $("#loader").hide();

        }

        function getTeamOverview(team, offset) {

            typeOrRequest= "TEAM";
            typeData=team;
            showLoader();

//            $("#tblr").html('<div class="center"><img src="pictures/loading.gif"></div>');

            $.ajax({
                url: "api/overview/get/index.php?json",
                data: {
                    type: typeOrRequest,
                    typedata: team,
                    offset: offset
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
                    hideLoader();

                });
        }

        function getManagerOverview(manager, offset) {
            typeOrRequest= "MANAGER";
            typeData = manager;
            showLoader();

//            $("#tblr").html('<div class="center"><img src="pictures/loading.gif"></div>');

            $.ajax({
                url: "api/overview/get/index.php?json",
                data: {
                    type: typeOrRequest,
                    typedata: manager,
                    offset: offset
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
                    hideLoader();

                });
        }


    </script>

    <div class="box">
                <span><span id="prev"></span><span id="next"></span><span id="nextGroup"></span><span
                        id="prevGroup"></span></span>
        <div class="container_12">
            <div class="grid_11">

            </div>
            <div id="tblr" class="grid_11 height800">

            </div>
            <div class="clear"></div>
        </div>


    </div>
<div id="loader"><div class="center"><img class="imagecenter" src="pictures/loader_large.gif"></div></div>


<?php
HtmlIncludes::footer();
?>
$(document).ready(function () {

    datePickerInit();

    $('#calendar').fullCalendar({
        weekends: false,
        weekNumbers: true,
        handleWindowResize: true,
        aspectRatio: 4,
        allDayDefault: true,
        events: 'api/schedule/get/index.php',
        dayClick: function (date, jsEvent, view) {
            eventClick(date, $(this));
        },
        eventClick: function (calEvent, jsEvent, view) {
            var date = calEvent.start;
            eventClick(date, $(this));
        }
    });

    $('#addToCalendar').click(function () {
        var formData = $("#batchForm").serializeArray();
        var from = $('#from').val();
        var to = $('#to').val();
        if (!from.trim() || !to.trim()) {
            alert('From date and to date is mandatory.');
            return;
        }

        //var state = $('#type').val();
        $("#loadingBatchCalendar").html('<div class="center"><img src="pictures/loader_small.gif"></div>');

        $.ajax({
            type: "POST",
            url: 'api/schedule/set/index.php',
            data: formData,
            statusCode: {
                500: function () {
                    alert("Could not save data");
                }
            }
        })
            .done(function (html) {
                $("#loadingBatchCalendar").html('');

            });
    });

    $('#test').click(function () {
        $.ajax({
            url: "api/overview/get/index.php",
            cache: false
        })
            .done(function (html) {
                $("#tblr").html(html);
                $('#myTable01').fixedHeaderTable({
                    footer: true,
                    cloneHeadToFoot: true,
                    altClass: 'odd',
                    autoShow: false,
                    fixedColumns: 3
                });
            });
    });


});

function getApprovalToDo() {
    $.ajax({
        type: "GET",
        url: 'api/approval/get/index.php',
        statusCode: {
            500: function () {
                alert("Could not save data");
            }
        },
        success: function (data) {
            var returnedData = JSON.parse(data);
            $('.box').html("");
            $.each(returnedData, function (i, eventObjects) {

                $('.box').append(eventObjects[0]['fullname'] + "[" + eventObjects[0]['user'] + "] <span class='link' onclick='javascript: approvalSetAll(\"" + eventObjects[0]['user'] + "\", 1);'>[Approve all]</span><span class='link' onclick='approvalSetAll(\"" + eventObjects[0]['user'] + "\", -1);'>[Deny all]</span><br>");

                $.each(eventObjects, function (i, aEventObject) {
                    $('.box').append("[<span class='link'> Approve</span>] [<span class='link'>Deny</span>] " + aEventObject["date"] + " " + aEventObject["title"] + " " + aEventObject["approvalStatus"] + "<br>");
                });
                $('.box').append('<br>');

            });
        }
    });
}

function approvalSetAll(user, state) {
    $(event.target).html('<img src="pictures/loader_small.gif">');
    $.ajax({
        type: "POST",
        url: "api/approval/set/index.php",
        data: {
            state: state,
            user: user
        },
        cache: false
    })
        .done(function (html) {
            getApprovalToDo();
        });
}

function eventClick(date, obj) {



    var dateBgColour= 'rgba(0, 0, 0, 0)';
    var switchOnChildBg = false;

    $('#calendar').fullCalendar('clientEvents', function (event) {
        if (event.start.format() === date.format()) {
            //console.log(event.id + " has a child");
            dateBgColour = event.backgroundColor;
            switchOnChildBg =  true;
        }
    });

    $('#calendar').fullCalendar('removeEvents', function (event) {
        if (event.start.format() === date.format()) {
           // console.log(event.id + " deleted");
            return true;
        }
    });

    var vacation = 'red';
    var vacation_rgb = 'rgb(255, 0, 0)';
    var course = 'blue';
    var course_rgb = 'rgb(0, 0, 255)';
    var parental = 'orange';
    var parental_rgb = 'rgb(255, 165, 0)';
    var none = 'white';
    var none_rgb = 'rgba(0, 0, 0, 0)';
    var none_rgb_transparent = 'transparent';
    var none_rgb_white = 'rgb(255, 255, 255)';
    var none_rgb_current_date = 'rgb(252, 248, 227)';
    var state = "none";
    if (switchOnChildBg == true)
    {
        var rgb =dateBgColour;
    }
    else
    {
        var rgb = obj.css('background-color');

    }

    switch (rgb) {
        case none_rgb:
        case none_rgb_white:
        case none_rgb_current_date:
        case none_rgb_transparent:
            var bgcolor = vacation;
            var state = "vacation";
            break;
        case vacation:
        case vacation_rgb:
            var bgcolor = course;
            var state = "course";
            break;
        case course:
        case course_rgb:
            var bgcolor = parental;
            var state = "parental";
            break;
        case parental:
        case parental_rgb:
            var bgcolor = none;
            var state = "none";
            break;
    }
    //  obj.css('background-color', bgcolor);
    var id = Math.random().toString();
    if (state.indexOf("none") == -1) {
        var event = {
            id: id,
            title: state,
            start: date,
            end: date,
            allDay: true,
            editable: false,
            backgroundColor: bgcolor
        };
        $('#calendar').fullCalendar('renderEvent', event, true) // Add Event to fullCalendar
    }
    $.ajax({
        type: "POST",
        url: 'api/schedule/set/index.php',
        data: {
            date: date.format(),
            state: state
        },
        statusCode: {
            500: function () {
                alert("Could not save data for date "+ date.format());
            }
        }
    });
}


function datePickerInit() {
    $("#from").datepicker({
//        defaultDate: "+1w",
        changeMonth: true,
        numberOfMonths: 1,
        showWeek: true,
        firstDay: 1,
        dateFormat: "yy-mm-dd",
        onClose: function (selectedDate) {
            $("#to").datepicker("option", "minDate", selectedDate);
        }
    });
    $("#to").datepicker({
//        defaultDate: "+1w",
        changeMonth: true,
        numberOfMonths: 1,
        showWeek: true,
        firstDay: 1,
        dateFormat: "yy-mm-dd",
        onClose: function (selectedDate) {
            $("#from").datepicker("option", "maxDate", selectedDate);
        }
    });
}
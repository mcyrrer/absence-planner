$(document).ready(function () {

    datePickerInit();

    $('#calendar').fullCalendar({
        weekends: false,
        weekNumbers: true,
        handleWindowResize: true,
        aspectRatio: 4,
        events: 'api/schedule/get/index.php',
        dayClick: function (date, jsEvent, view) {
            eventClick(date,$(this));
        },
        eventClick: function(calEvent, jsEvent, view) {
            var date = calEvent.start;
            eventClick(date,$(this));
        }
    })

    $('#addToCalendar').click(function() {
        var from = $('#from').val();
        var to = $('#to').val();
        var state = $('#type').val();
        $.ajax({
            type: "POST",
            url: 'api/schedule/set/index.php',
            data: {
                from: from,
                to: to,
                state: state
            },
            statusCode: {
                500: function () {
                    alert("Could not save data");
                }
            }
        });
    });

});

function eventClick(date,obj)
{
    var vacation = 'red';
    var vacation_rgb = 'rgb(255, 0, 0)';
    var course = 'blue';
    var course_rgb = 'rgb(0, 0, 255)';
    var parental = 'orange';
    var parental_rgb = 'rgb(255, 165, 0)';
    var none = 'white';
    var none_rgb = 'rgba(0, 0, 0, 0)';
    var none_rgb_white = 'rgb(255, 255, 255)';
    var none_rgb_current_date = 'rgb(252, 248, 227)';
    var rgb = obj.css('background-color');
    var state = "none";
    switch (rgb) {
        case none_rgb:
        case none_rgb_white:
        case none_rgb_current_date:
            obj.css('background-color', vacation);
            var state = "vacation";
            break;
        case vacation_rgb:
            obj.css('background-color', course);
            var state = "course";
            break;
        case course_rgb:
            obj.css('background-color', parental);
            var state = "parental";
            break;
        case parental_rgb:
            obj.css('background-color', none);
            var state = "none";
            break;
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
                alert("Could not save data");
            }
        }
    });
}


function datePickerInit()
{
    $( "#from" ).datepicker({
//        defaultDate: "+1w",
        changeMonth: true,
        numberOfMonths: 1,
        showWeek: true,
        firstDay: 1,
        dateFormat: "yy-mm-dd",
        onClose: function( selectedDate ) {
            $( "#to" ).datepicker( "option", "minDate", selectedDate );
        }
    });
    $( "#to" ).datepicker({
//        defaultDate: "+1w",
        changeMonth: true,
        numberOfMonths: 1,
        showWeek: true,
        firstDay: 1,
        dateFormat: "yy-mm-dd",
        onClose: function( selectedDate ) {
            $( "#from" ).datepicker( "option", "maxDate", selectedDate );
        }
    });
}
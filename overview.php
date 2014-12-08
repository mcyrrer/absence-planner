<?php
require 'vendor/autoload.php';
require 'classes/HtmlIncludes.php';

$log = new Monolog\Logger('name');
$log->pushHandler(new Monolog\Handler\StreamHandler('app.log', Monolog\Logger::WARNING));


HtmlIncludes::header();
?>

<script>
    $(document).ready(function () {

        $.ajax({
            url: "api/overview/get/index.php",
            cache: false
        })
            .done(function (html) {
                $("#tblr").html(html);
                $('#myTable01').fixedHeaderTable({ footer: true,
                    cloneHeadToFoot: true,
                    altClass: 'odd',
                    autoShow: true,
                    fixedColumns: 3
                });
                $('#myTable01').fixedHeaderTable('show', 1000);

            });
    });


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
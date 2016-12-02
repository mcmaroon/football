<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require __DIR__ . '/../vendor/autoload.php';

use Football\Football;

if (isset($_SESSION["points"])) {
    unset($_SESSION["points"]);
}
$football = new Football();

if (file_exists('../football.log')) {
    unlink('../football.log');
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">        
        <title>Football</title>
        <link type="text/css" rel="stylesheet" href="../Resources/public/css/football.css" media="all" />
        <style>
            body {
                font-family: monospace;
            }
            pre {
                overflow: scroll;
            }
            pre .alert {
                margin: 4px 0;
                padding: 4px;
            }
            pre .alert-notice {
                background-color: greenyellow;
            }
            pre .alert-warning{
                background-color: darkorange;
            }
            .football .point {
                background: url("bg.png") transparent no-repeat;
            }
            .football .point:hover {
                background: url("bg-start.png") transparent no-repeat;
            }
            .football .point[data-x="<?php print $football->getBoardCenter()->x ?>"][data-y="<?php print $football->getBoardCenter()->y ?>"] {
                background: url("bg-start.png") transparent no-repeat;
            }           
            .football .position:not(.debug) {
                color: transparent;
            }
            .football .bit:not(.debug) {
                display: none;
            }
        </style>        
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
        <script src="../Resources/public/js/football.js"></script>
    </head>
    <body>

        <h1><?php echo 'Game center point: x:' . $football->getBoardCenter()->x . ' y:' . $football->getBoardCenter()->y; ?></h1>

        <p id="toggle-debug" style="cursor: pointer;">show/hide debug</p>

        <div style="padding: 12px;">
            <div style="float: left; width: 50%;">
                <div id="football">
                    <?php print require_once('./template.php') ?>
                </div>
            </div>
            <div style="float: left; width: 50%;">
                <pre></pre>
            </div>
        </div>



    </body>

    <script>
        var APP = APP || {};
        var MID = 0;

        if (typeof APP.football === 'function') {
            new APP.football('<?php print $football->getWidth() ?>', '<?php print $football->getHeight() ?>', {
                url: 'ajax.php'
            });

            APP.football.prototype.message = function (message, type) {
                MID++;
                var type = typeof type === 'string' ? type : 'notice';
                $('pre').prepend('<div class="alert alert-' + type + '">' + MID + ' - ' + message + '</div>');
            };

            $('body').delegate('#toggle-debug', 'click', function (e) {
                $('.position').toggleClass('debug');
                $('.bit').toggleClass('debug');
            });
        }

    </script>

</html>
<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <?php include("modules/header.php");?>
        <script src="<?php echo $baseUrl; ?>/static/js/view_blocked.js"></script>
    </head>
    <body>
        <div class="container-fluid">
            <div class="text-center">
                <h1>
                    <span class="glyphicon glyphicon-ban-circle" aria-hidden="true"></span>
                </h1>
            </div>
            <div class="panel panel-default">
                <div class="panel-body text-center">
                    <h1>You are blocked for the next <span id="countDownLabel"><?php if(isset($blockedTime)) echo $blockedTime;?></span> seconds.</h1>
                </div>
            </div>
        </div>
    </body>
</html>

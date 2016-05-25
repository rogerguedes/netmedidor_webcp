<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <?php include("modules/header.php");?>
    </head>
    <body>
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-6 col-md-offset-3 well">
                    <h2 class="text-center"><a href="<?php echo $appFullPath;?>">Home</a></h2>
                    <h3 class="capitalize-fl"><?php echo $glossary['MSGS']['ERROR_404']; ?></h3>
                </div>
            </div>
        </div>
    </body>
</html>

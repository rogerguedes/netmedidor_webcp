<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <?php include("modules/header.php");?>
        <script src="<?php echo $baseUrl; ?>/static/js/clockUpdater.js"></script>
        <script src="<?php echo $baseUrl; ?>/static/js/syncnodes.js"></script>
    </head>
    <body>
        <?php include("modules/menu.php");?>
        <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        </div>
        <div class="container-fluid">
            <div class="row status-bar">
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="panel panel-default">
                        <div class="panel-heading clearfix text-center">
                            <span class="panel-title capitalize"><?php echo $glossary["APP"]["SYNCNODES"]; ?></span>
                            <button id="addModelBtn" type="button" class="btn btn-success pull-right capitalize" title="<?php echo $glossary["GENERAL"]["ADD"]; ?>" ><span class="glyphicon glyphicon-plus" aria-hidden="true"></span></button>
                        </div>
                        <div class="panel-body">
                            <div id="syncNodesDiv" class="table-responsive">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div id="syncNodePane" class="panel panel-default">
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>

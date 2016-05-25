<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <?php include("modules/header.php");?>
    </head>
    <body>
        <?php include("modules/menu.php");?>
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-6 col-md-offset-3">
                    <div class="text-center">
                        <h1><?php echo $appName; ?></h1>
                        <h2 class="hidden-small capitalize"><?php echo $glossary['MSGS']['WELCOME']; ?>, <?php echo $glossary['MSGS']['SIGNIN']; ?></h2>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 col-md-offset-3 mar-bot-1">
                    <div class="text-center">
                        <?php foreach($glossaryList as $lang):?>
                            <a title="<?php echo $lang['name'];?>" href="<?php echo $appFullPath;?>app/index/<?php echo $lang['iso_name'];?>"><img class="img-thumbnail" alt="<?php echo $lang['iso_name'];?>" src="<?php echo $baseUrl;?>static/imgs/flags_iso/48/<?php echo $lang['flag'];?>"/></a>
                        <?php endforeach;?>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="form-box signin-card well">
                    <form action="<?php echo $appFullPath;?>app/login/<?php echo $glossary['META']['ISONAME']; ?>" method="post" accept-charset="utf-8" role="form">
                        <div class="form-group">
                            <input class="form-control input-lg" id="inputEmail" placeholder="<?php echo $glossary['GENERAL']['EMAIL']; ?>" name="email" type="email">
                        </div>
                        <div class="form-group">
                            <input class="form-control input-lg" id="inputEmail" placeholder="<?php echo $glossary['GENERAL']['PASSWORD']; ?>" name="password" type="password">
                        </div>
                        <?php if(isset($errors)):?>
                            <div class="alert alert-danger" role="alert">
                                <ul>
                                    <?php foreach($errors as $err):?>
                                        <li><?php echo $err;?></li>
                                    <?php endforeach;?>
                                </ul>
                            </div>
                        <?php endif;?>
                        <button type="submit" class="btn btn-lg btn-primary btn-block capitalize"><?php echo $glossary['GENERAL']['LOGIN']; ?></button>
                    </form>
                </div>
            </div>
        </div>
    </body>
</html>


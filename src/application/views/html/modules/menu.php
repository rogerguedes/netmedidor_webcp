<?php if(isset($sessionUser)):?>
    <nav class="navbar navbar-default" role="navigation">
        <div class="container-fluid">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand navbarLogoAnchor" href="<?php echo $baseUrl;?>"><img src="<?php echo $baseUrl;?>static/imgs/logo.png" /></a>
                <a class="navbar-brand" href="<?php echo $baseUrl;?>">Eletra Energy Solutions</a><!--lang-->
            </div>
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav navbar-right">
                    <li><a class="capitalize" href="<?php echo $appFullPath;?>syncnodes"><?php echo $glossary["APP"]["SYNCNODES"]; ?></a></li>
                    <li class="dropdown">
                        <a class="capitalize" href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo($sessionUser->getName());?> <span class="glyphicon glyphicon-cog" aria-hidden="true"></span><b class="caret"></b></a>
                        <ul class="dropdown-menu">
                            <li><a class="capitalize" href="#"><?php echo $glossary["GENERAL"]["PROFILE"]; ?></a></li>
                            <li class="divider"></li>
                            <li class="dropdown-header capitalize"><?php echo $glossary["GENERAL"]["MODELS"]; ?></li>
                            <li><a class="capitalize" href="<?php echo $appFullPath;?>metermodels"><?php echo $glossary["APP"]["METERS"]; ?></a></li>
                            <li><a class="capitalize" href="<?php echo $appFullPath;?>syncnodemodels"><?php echo $glossary["APP"]["SYNCNODES"]; ?></a></li><!--lang-->
                            <li class="divider"></li>
                            <li><a class="capitalize" href="<?php echo $appFullPath;?>app/logout"><?php echo $glossary["GENERAL"]["LOGOUT"]; ?></a></li>
                        </ul>
                    </li>
                </ul>
            </div><!-- /.navbar-collapse -->
        </div><!-- /.container-fluid -->
    </nav>
<?php endif;?>

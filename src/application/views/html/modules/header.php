<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');?>
<title><?php echo $appName?></title>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="description" content="">
<meta name="author" content="">

<!--[if lt IE 9]><script src="../assets/js/ie8-responsive-file-warning.js"></script><![endif]-->
<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
<!--[if lt IE 9]>
<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
<script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
<![endif]-->

<!-- Bootstrap CSS-->
<link rel="stylesheet" type="text/css" href="<?php echo $baseUrl; ?>/static/css/bootstrap.css">
<!-- Template CSS-->
<link rel="stylesheet" type="text/css" href="<?php echo $baseUrl; ?>/static/css/template.css">
<!-- Bootstrap JavaScript-->
<script src="<?php echo $baseUrl; ?>/static/js/jquery-1.9.1.js"></script>
<script src="<?php echo $baseUrl; ?>/static/js/bootstrap.js"></script>
<!-- System vars to JavaScript -->
<script>
    var backEnd = {"appFullPath": "<?php echo $appFullPath;?>", "serverTime": <?php echo time(); ?>, "glossary": <?php echo json_encode($glossary); ?>}
</script>
<!--Favicons-->
<link rel="shortcut icon" href="<?php echo $baseUrl; ?>/static/imgs/favicon.png" type="image/x-ic">

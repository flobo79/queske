<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?php echo PROJECT_TITLE ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="create a flipbook on the fly">
    <meta name="author" content="Florian Bosselmann">

    <link href="<?php echo BASEURL ?>/app/templates/<?php echo DESIGN ?>/css/app.css" rel="stylesheet">
    <link href="<?php echo BASEURL ?>/app/templates/<?php echo DESIGN ?>/css/bootstrap.css" rel="stylesheet">
    <link href="<?php echo BASEURL ?>/app/templates/<?php echo DESIGN ?>/css/bootstrap-responsive.css" rel="stylesheet">
    <link href="<?php echo BASEURL ?>/app/templates/<?php echo DESIGN ?>/css/font-awesome.min.css" rel="stylesheet">

    <?php foreach($this->css as $file) { ?>
    <link href="<?php echo BASEURL."/".$file ?>" rel="stylesheet">
    <?php } ?>
    
</head>

<body>
    <div class="navbar navbar-inverse navbar-fixed-top">

        <div class="navbar-inner">

            <div class="container">
                <button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="brand" href="#"><?php echo PROJECT_TITLE ?></a>

                <div class="nav-collapse collapse">

                    <ul class="nav">
                        <?php if($this->identity->isLoggedIn()) { ?>
                            <li><a href="<?php echo BASEURL ?>/questionnaires"><?php echo $this->__str('Questionnaires') ?></a></li>
                            <li><a href="<?php echo BASEURL ?>/surveys"><?php echo $this->__str('Surveys') ?></a></li>
                            <!-- li><a href="<?php echo BASEURL ?>/surveys/create"><?php echo $this->__str('Create Survey') ?></a></li -->
                            <?php if($this->identity->getRole() == 'admin') { ?>
                            <li><a href="<?php echo BASEURL ?>/admin">Admin</a></li>
                            <?php } ?>
                    </ul>

                    <ul class="nav pull-right">
                        <li><a href="#"><?php echo $this->identity->getName() ?></a></li>
                        <li class="span1 pull-right"><a href="<?php echo BASEURL ?>/logout">Logout</a></li>
                        <?php } ?>
                    </ul>
                </div><!--/.nav-collapse -->
            </div>
        </div>
    </div>

    <!-- container -->
    <div class="container main">
      
      {content}

    </div> <!-- end container -->


    <script src="<?php echo BASEURL ?>/app/vendor/jquery/jquery/jquery-1.9.1.js"></script>
    <script >
        jQuery.browser={};(function(){jQuery.browser.msie=false;
        jQuery.browser.version=0;if(navigator.userAgent.match(/MSIE ([0-9]+)\./)){
        jQuery.browser.msie=true;jQuery.browser.version=RegExp.$1;}})();
    </script>
    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
    <script src="<?php echo BASEURL ?>/app/library/js/hash.js"></script>
    <script src="<?php echo BASEURL ?>/app/templates/<?php echo DESIGN ?>/js/bootstrap-select.min.js"></script>
    <script src="<?php echo BASEURL ?>/app/templates/<?php echo DESIGN ?>/js/bootstrap-slider.js"></script>
    <script src="<?php echo BASEURL ?>/app/templates/<?php echo DESIGN ?>/js/bootstrap.min.js"></script>
    <script src="<?php echo BASEURL ?>/app/templates/<?php echo DESIGN ?>/js/bootstrap-colorpicker.js"></script>

    <?php foreach($this->js as $file) { ?>
    <script src="<?php echo BASEURL."/".$file ?>"></script>
    <?php } ?>

    <script >
        if(typeof init == 'function') {
            init();
        }
    </script>


</body>
</html>

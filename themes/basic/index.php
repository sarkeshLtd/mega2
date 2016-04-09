<?php use \Mega\cls\browser as browser;?>
<!DOCTYPE html>
<head>
    </#HEADERS#/>
    <title></#PAGE_TITTLE#/></title>
</head>
<body>
<div class="navbar navbar-default">
    <div class="container">
        <a class="navbar-brand" href="/"></#SITE_NAME#/></a>
        <?php browser\page::position('main_menu'); ?>
    </div>
</div>

<div class="container">
    <?php browser\page::position('slide_show'); ?>
    <div class="row">
        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
            <?php browser\page::position('sidebar1'); ?>
        </div>
        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <?php browser\page::position('top_content'); ?>
            </div>
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <?php browser\page::position('content'); ?>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <?php browser\page::position('top_footer1'); ?>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <?php browser\page::position('top_footer2'); ?>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
            <?php browser\page::position('top_footer3'); ?>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
            <?php browser\page::position('top_footer4'); ?>
        </div>
    </div>
    <hr>
    <div class="col-lg-12">
        <?php browser\page::position('footer'); ?>
    </div>


</div> <!-- /container -->
</body>
</html>
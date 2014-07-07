<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7; IE=EmulateIE9">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
if($this->router->fetch_method()=='index')
{
	echo '<meta http-equiv="refresh" content="30">';
	echo '<!--[if IE]><script src="static/js/excanvas.compiled.js"></script><![endif]-->';
	echo '<script type="text/javascript"
  src="static/js/dygraph-combined.js"></script>';

}
?>

<title><?= $title ?></title>
<link href="static/css/bootstrap.min.css" rel="stylesheet" media="screen">
<link href="static/css/layout.css" rel="stylesheet" type="text/css" />
</head>
<body>



<div id="container">
    <div class="navbar navbar-fixed-top">
      <div class="navbar-inner">
        <div class="container"  style="width:98%">
          <button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a href="<?= WEB_ROOT ?>" class="brand"><img src="static/img/logo.png" style="height:20px"></a>

          <div class="nav-collapse collapse">
            <ul class="nav">
              <li class="active"><a href="<?= WEB_ROOT ?>">Home</a></li>
              <li><a href="http://forum.rockminer.com/" target="_blank">Forum</a></li>
              <li><a href="#contact">Contact</a></li>
              

            </ul>
            <span class="WebUI">RockMinerWeb v<?= CURRENT_VERSION?></span>
          </div><!--/.nav-collapse -->
        </div>
      </div>
    </div>


	  

  <div id="mainContent">
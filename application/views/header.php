<!doctype html>
<html>
<head>
<title><?php echo $title ?></title>
<meta charset="utf-8">
<link href='<?php echo URL::site('favicon.ico')?>' rel='shortcut icon' type='image/x-icon'/>
<link rel="stylesheet" type="text/css" media="all" href="<?php echo URL::site('assets/stylesheets/main.css') ?>">
<?php echo $scripts ?>
</head>
<body>
 <div id="main_container">
 <div id="header">
  <h1><?php echo Kohana::$config->load('common.title')?></h1>
 </div>
 <div id="menu">
  <?php echo Request::factory('navigation/actions')->execute() ?>
 </div>
 <div id="column_text">
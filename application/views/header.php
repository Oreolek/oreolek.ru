<!doctype html>
<html>
<head>
<title><?php echo $title ?></title>
<meta charset="utf-8">
<link href='<?php echo URL::site('favicon.ico')?>' rel='shortcut icon' type='image/x-icon'/>
<?php echo $stylesheet ?>
<?php echo $scripts ?>
</head>
<body>
 <div id="main_container">
 <div id="header">
  <h1><?php echo Kohana::$config->load('common.title')?></h1>
 </div>
 <div id="column_text">

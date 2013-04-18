<!doctype html>
<html lang="ru">
<head>
<title><?php echo $title ?></title>
<meta charset="utf-8">
<link href='<?php echo URL::site('favicon.ico')?>' rel='shortcut icon' type='image/x-icon'/>
<?php echo $stylesheet ?>
<?php echo $scripts ?>
</head>
<body>
 <div class = "main_container">
 <div class = "header text_center">
  <h1><?php echo Kohana::$config->load('common.title')?></h1>
 </div>
<div class="content">

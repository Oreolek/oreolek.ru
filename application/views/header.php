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
    <div class = "container">
      <div class = "header text_center">
      <?php
      if ($logged_in)
      {
        echo '<h1>Добро пожаловать, '.Auth::instance()->get_user()->username.'</h1>';
      }
      else
      {
        echo '<h1>'.Kohana::$config->load('common.title').'</h1>';
      }
      ?>
      </div>
      <div class = "navigation">
        <ul>
          <li><a href="<?php echo URL::site('/post/fresh') ?>">Свежие записи дневника</a></li>
          <li><a href="<?php echo URL::site('/post/index') ?>">Содержание дневника</a></li>
          <li><a href="<?php echo URL::site('/page/index') ?>">Список страниц</a></li>
          <li><a href="<?php echo URL::site('/page/view/1') ?>">О сайте</a></li>
          <?php if (!$logged_in) { ?>
            <li><a href="<?php echo URL::site('/user/signin') ?>">Вход</a></li>
          <?php } else { ?>
            <li><a href="<?php echo URL::site('/post/drafts') ?>">Черновики дневника</a></li>
            <li><a href="<?php echo URL::site('/page/drafts') ?>">Черновики страниц</a></li>
          <?php } ?>
        </ul>
      </div>
      <div class = "main_content">
        <h2><?php echo $title ?></h2>

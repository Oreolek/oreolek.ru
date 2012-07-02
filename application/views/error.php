<?php echo Request::factory('header/standard')->post('title',$title)->execute() ?>
  <h1><?php echo $title?></h1>
  <p><?php echo $description?></p>
 <?php echo Request::factory('footer/standard')->execute() ?>

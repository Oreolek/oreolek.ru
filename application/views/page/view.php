<?php echo Request::factory('header/standard')->post('title',$title)->execute() ?>

<h2><?php echo $title ?></h2>
<div class="hyphenate">
  <?php echo $content ?>
</div>

<?php echo Request::factory('footer/standard')->execute() ?>

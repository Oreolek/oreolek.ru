<?php echo Request::factory('header/standard')->post('title',$title)->execute() ?>

<div class="hyphenate">
  <?php echo $content ?>
</div>

<?php echo Request::factory('footer/standard')->execute() ?>

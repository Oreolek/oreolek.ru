<?php echo Request::factory('header/standard')->post('title',$title)->execute() ?>

<div class="hyphenate">
  <?php echo $content ?>
  <div class="comment_section">
    <?php echo Request::factory('comment/view')->post('post_id', $id)->execute() ?>
  </div>
</div>

<?php echo Request::factory('footer/standard')->execute() ?>

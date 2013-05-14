<?php echo $header; ?>
<div class="tag_content"><?php echo $content; ?>&nbsp;</div>
<div class="tag_posts">
  <h3>Заметки с этим тегом</h3>
  <ol>
    <?php
    foreach ($posts as $post)
    {
      echo '<li><a href="'.URL::site('post/view/'.$post->id).'">'.$post->name.'</a></li>';
    }
    ?>
  </ol>
</div>
<?=$footer;?>

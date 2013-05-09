<?php echo $header; ?>

<div class="table_index">
<?php
if ($is_admin)
{
  echo '<a href="'.URL::site('post/create').'">Новый пост</a>';
}
foreach ($posts as $post)
{
  echo '<div class="date">'. $post->posted_at .'</div>';
  echo '<a class = "link_view" href = "'. URL::site('post/view/' . $post->id). '">' . $post->name . '</a>';
  if ($is_admin)
  {
    echo '<a class = "link_edit" href = "'. URL::site('post/edit/' . $post->id). '">Редактировать</a></div>';
    echo '<a class = "link_delete" href = "'. URL::site('post/delete/' . $post->id). '">Удалить</a></div>';
  }
}
?>
</div>

<?php echo $footer; ?>

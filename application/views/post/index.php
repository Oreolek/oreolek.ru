<?php echo Request::factory('header/standard')->post('title',$title)->execute() ?>

<div class="table_index">
<?php
foreach ($posts as $post)
{
  echo '<div class="date">'. $post->posted_at .'</div>';
  echo '<a class = "link_view" href = "'. URL::site('post/view/' . $post->id). '">' . $post->name . '</a>';
  echo '<a class = "link_edit" href = "'. URL::site('post/edit/' . $post->id). '">Редактировать</a></div>';
  echo '<a class = "link_delete" href = "'. URL::site('post/delete/' . $post->id). '">Удалить</a></div>';
}
?>
</div>

<?php echo Request::factory('footer/standard')->execute() ?>

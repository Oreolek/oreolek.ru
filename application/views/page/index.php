<?php echo $header ?>

<div class="table_index">
<?php
foreach ($pages as $page)
{
  echo '<div class="date">'. $page->posted_at .'</div>';
  echo '<a class = "link_view" href = "'. URL::site('page/view/' . $page->id). '">' . $page->name . '</a>';
  echo '<a class = "link_edit" href = "'. URL::site('page/edit/' . $page->id). '">Редактировать</a></div>';
  echo '<a class = "link_delete" href = "'. URL::site('page/delete/' . $page->id). '">Удалить</a></div>';
}
?>
</div>

<?php echo $footer ?>

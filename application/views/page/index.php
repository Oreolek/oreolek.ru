<?php echo $header ?>

<div class="table_index">
<?php
if (Auth::instance()->logged_in('admin'))
{
  echo '<a href="'.URL::site('page/create').'">Новая страница</a>';
}
foreach ($pages as $page)
{
  echo '<div class="date">'. $page->posted_at .'</div>';
  echo '<a class = "link_view" href = "'. URL::site('page/view/' . $page->id). '">' . $page->name . '</a>';
  if (Auth::instance()->logged_in('admin'))
  {
    echo '<a class = "link_edit" href = "'. URL::site('page/edit/' . $page->id). '">Редактировать</a></div>';
    echo '<a class = "link_delete" href = "'. URL::site('page/delete/' . $page->id). '">Удалить</a></div>';
  }
}
?>
</div>

<?php echo $footer ?>

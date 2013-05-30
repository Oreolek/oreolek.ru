<?php echo $header; ?>

<?php
if ($is_admin)
{
  echo '<a href="'.Route::url('default', array('controller' => Request::current()->controller(), 'action' => 'create')).'" class="link_new">Добавить</a>'; 
}
?>
<div class="table_index">
<?php
$columns = 3;
if (!isset($show_date) OR $show_date != TRUE)
{
  $columns++;
}
if (!$is_admin)
{
  $columns = $columns + 2;
}

foreach ($items as $item)
{
  echo '<div class="table_row">';
  if (isset($show_date) AND $show_date === TRUE)
  {
    echo '<div class="date">'. $item->creation_date() .'</div>';
  }
  echo '<a class = "link_view column'.$columns.'" href = "'.Route::url('default', array('controller' => Request::current()->controller(), 'action' => 'view','id' => $item->id)).'">' . $item->name . '</a>';
  if ($is_admin)
  {
    echo '<a class = "link_edit" href = "'.Route::url('default', array('controller' => Request::current()->controller(), 'action' => 'edit','id' => $item->id)).'">Редактировать</a>';
    echo '<a class = "link_delete" href = "'.Route::url('default', array('controller' => Request::current()->controller(), 'action' => 'delete','id' => $item->id)).'">Удалить</a>';
  }
  echo '</div>';
}
?>
</div>

<?php echo $footer; ?>

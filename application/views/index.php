<?php echo $header; ?>

<?php
if ($is_admin)
{
  echo '<a href="'.Route::url('default', array('controller' => Request::current()->controller(), 'action' => 'create')).'" class="link_new">Добавить</a>'; 
}
?>
<div class="table_index">
<?php
foreach ($items as $item)
{
  echo '<div class="table_row">';
  echo '<div class="date">'. $item->posted_at .'</div>';
  echo '<a class = "link_view" href = "'.Route::url('default', array('controller' => Request::current()->controller(), 'action' => 'view','id' => $item->id)).'">' . $item->name . '</a>';
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

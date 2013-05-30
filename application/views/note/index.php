<?php echo $header; ?>

<div class="table_index">
<?php
echo '<a href="'.URL::site('note/create').'">Новая заметка</a>';
foreach ($notes as $note)
{
  echo '<div class="date">'. $note->posted_at .'</div>';
  echo '<a class = "link_view" href = "'. URL::site('note/view/' . $note->id). '">' . $note->name . '</a>';
  echo '<a class = "link_edit" href = "'. URL::site('note/edit/' . $note->id). '">Редактировать</a></div>';
  echo '<a class = "link_delete" href = "'. URL::site('note/delete/' . $note->id). '">Удалить</a></div>';
}
?>
</div>

<?php echo $footer; ?>

<?php echo $header;

echo Form::open();
if ($errors)
{
  echo '<p class="message">При проверке формы были найдены ошибки:</p>';
  echo '<ul class="errors">';
  foreach ($errors as $message)
  {
    echo "<li> ".$message." </li>";
  }
  echo '</ul>';
}
echo Form::orm_input($post, 'name');
echo Form::orm_textarea($post, 'content');
echo Form::orm_checkbox($post, 'is_draft');
echo Form::orm_input($post, 'posted_at');
echo '<div class="container">'.Form::label('tags', 'Теги').Form::input('tags',$tags).'</div>';
echo Form::submit('submit','Отправить');
echo Form::close();

echo $footer;

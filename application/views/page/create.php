<?php echo $header;

echo Form::open();
if ($errors)
{
  echo '<p class="message">При проверке формы были найдены ошибки:</p>';
  echo '<ul class="errors">';
  foreach ($errors as $message)
  {
    echo "<li> $message </li>";
  }
  echo '</ul>';
}
echo Form::orm_input($page, 'name');
echo Form::orm_textarea($page, 'content');
echo Form::orm_checkbox($page, 'is_draft');
echo Form::submit('submit','Отправить');
echo Form::close();

echo $footer;

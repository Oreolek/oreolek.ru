<?php 
echo $header;
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
echo Form::orm_input($model, 'hostname');
echo Form::orm_input($model, 'database');
echo Form::orm_input($model, 'username');
echo Form::orm_password($model, 'password');
echo Form::orm_input($model, 'prefix');
echo Form::submit('submit','Отправить');
echo Form::close();
echo $footer;

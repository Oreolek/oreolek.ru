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
echo Form::orm_input($user, 'username');
echo Form::orm_input($user, 'email');
echo Form::orm_input($user, 'password');
echo Form::submit('submit','Отправить');
echo Form::close();
echo $footer;

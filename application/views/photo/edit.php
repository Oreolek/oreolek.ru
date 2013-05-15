<?php echo $header;

echo Form::open( null, array( 'enctype' => 'multipart/form-data' ) );
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
$image_path = $photo->get_image_path();
if ($image_path) echo HTML::image($image_path);
echo Form::orm_input($photo, 'name');
echo Form::file('file');
echo Form::submit('submit','Отправить');
echo Form::close();

echo $footer;

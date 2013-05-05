<?php echo $header;

echo Form::open();
echo Form::orm_input($page, 'name');
echo Form::orm_textarea($page, 'content');
echo Form::submit('submit','Отправить');
echo Form::close();

echo $footer;

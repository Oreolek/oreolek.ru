<?php echo $header;

echo Form::open();
echo Form::orm_input($post, 'name');
echo Form::orm_textarea($post, 'content');
echo Form::submit('submit','Отправить');
echo Form::close();

echo $footer;

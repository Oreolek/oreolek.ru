<?php echo Request::factory('header/standard')->post('title',$title)->execute();

echo Form::open();
echo Form::orm_input($post, 'name');
echo Form::orm_textarea($post, 'content');
echo Form::submit('submit','Отправить');
echo Form::close();

echo Request::factory('footer/standard')->execute();

<?php echo Request::factory('header/standard')->post('title',$title)->execute();

echo Form::open(); ?>
<?php echo Form::orm_input($page, 'name');  ?>
<?php echo Form::orm_textarea($page, 'content');  ?>
<?php echo Form::submit('submit','Отправить') ?>
<?php echo Form::close();

echo Request::factory('footer/standard')->execute() ?>

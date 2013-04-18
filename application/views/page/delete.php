<?php echo Request::factory('header/standard')->post('title',$title)->execute();

echo Form::open('page/delete') ?>
<p><?php echo Form::label('confirmation','Действительно удалить страницу?'); echo Form::checkbox('confirmation', 'confirmation', false) ?></p>

<h3><?php echo $page_title; ?></h3>
<?php echo $page_content; ?>

<p><?php echo Form::submit('submit','Отправить') ?></p>
<?php echo Form::close();

echo Request::factory('footer/standard')->execute() ?>
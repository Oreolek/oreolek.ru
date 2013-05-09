<?php echo $header;

echo Form::open() ?>
<p><?php echo Form::label('confirmation','Действительно удалить запись?');
echo Form::checkbox('confirmation', 'yes', false) ?></p>

<h3><?php echo $title; ?></h3>
<?php echo $content; ?>

<p><?php echo Form::submit('submit','Отправить') ?></p>
<?php echo Form::close();

echo $footer; ?>

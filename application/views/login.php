<?php echo Request::factory('header/standard')->post('title',"Вход в систему")->execute() ?>

<div id="error"><?php if(!empty($error)) echo $error;?></div>
<div id="message"><?php if(!empty($message)) echo $message;?></div>
<p>Введите логин и пароль для получения доступа к разделу.</p>
<?php echo Form::open('login') ?>
  <p><?php echo Form::label('login','Логин: '); echo form::input('login','') ?></p>
  <p><?php echo Form::label('password','Пароль: '); echo form::password('password','') ?></p>
  <p><?php echo Form::submit('submit','Отправить') ?></p>
<?php echo Form::close() ?>
<?php echo Request::factory('footer/standard')->execute() ?>

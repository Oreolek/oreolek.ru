<?php echo Request::factory('header/standard')->post('title',"Вход в систему")->execute() ?>

<div id="error"><?php if(!empty($error)) echo $error;?></div>
<div id="message"><?php if(!empty($message)) echo $message;?></div>
<p>Введите логин и пароль для получения доступа к разделу.</p>
<?php echo form::open('login') ?>
  <p><?php echo form::label('login','Логин: '); echo form::input('login','') ?></p>
  <p><?php echo form::label('password','Пароль: '); echo form::password('password','') ?></p>
  <p><?php echo form::submit('submit','Отправить') ?>
</p>
<?php echo form::close() ?>
<?php echo Request::factory('footer/standard')->execute() ?>
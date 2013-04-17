<?php
echo Request::factory('header/standard')
  ->post('title',$title)
  ->execute();
?>
<script>
jQuery(document).ready(function() {
});
</script>

<div class="content">
<?php echo Form::open('page/create') ?>
<p><?php echo Form::label('title','Заголовок: '); echo Form::input('title','') ?></p>
<p><?php echo Form::label('content','Текст статьи: '); echo Form::textarea('content','',array('id' => 'content')) ?></p>
<p><?php echo Form::label('is_markdown','Разметка Markdown'); echo Form::checkbox('is_markdown', 'is_markdown', true) ?></p>
<p><?php echo Form::submit('submit','Отправить') ?></p>
<?php echo Form::close() ?>
</div>

<?php echo Request::factory('footer/standard')->execute() ?>

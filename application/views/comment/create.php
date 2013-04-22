<?php
echo Form::open('comment/create');
echo Form::orm_input($comment, 'author_name');
echo Form::hidden('email', '');
echo Form::hidden('post_id', $comment->post_id);
echo Form::orm_input($comment, 'author_email');
echo Form::orm_textarea($comment, 'content');
echo Form::submit('submit','Отправить');
echo Form::close();

<?php
foreach ($comments as $comment)
{
  echo '<div class="comment">';
  echo '<a class="author" href="mailto:'.$comment->author_email.'">'. $comment->author_name .'</a>';
  echo '<div class="content">'. $comment->content .'</div>';
  echo '</div>';
}


<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Comment view controller.
 * Displays all comments for a specific post.
 **/
class View_Comment_View extends View_Layout {
  public $comments;
  public function get_comments()
  {
    $result = array();
    foreach ($this->comments as $comment)
    {
      $comment_out = array(
        'content' => Markdown::instance()->transform($comment->content),
        'author_email' => $comment->author_email,
        'author_name' => $comment->author_name,
        'id' => $comment->id
      );
      if (empty($comment->author_name))
      {
        $comment_out['author_name'] = Kohana::$config->load('common')->get('anonymous_name');
      }
      array_push($result, $comment_out);
    }
    return $result;
  }
}

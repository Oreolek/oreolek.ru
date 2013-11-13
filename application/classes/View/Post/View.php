<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Post view controller.
 **/
class View_Post_View extends View_Layout {
  /**
   * Post tag models.
   **/
  public $tags;
  /**
   * Post ID
   **/
  public $id;
  /**
   * Post comments
   **/
  public $comments;
  public $date;
  public $is_admin = FALSE;

  public $scripts = array(
    'jquery',
    'jquery.autosize-min.js',
    'lightbox-2.6.min.js',
    'load_comment_form.js'
  );

  public function get_tags()
  {
    $output = 'Теги: ';
    if (count($this->tags) > 0)
    {
      $i = 0;
      foreach ($this->tags as $tag)
      {
        if ($i > 0) $output .= ', ';
        $output .= '<a href="'.URL::site('tag/view/'.$tag->id).'">'.$tag->name.'</a>';
        $i++;
      }
    }
    else 
    {
      return '';
    }
    return $output;
  }

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

  public function link_edit()
  {
    return HTML::anchor(Route::url('default',array('controller' => 'Post', 'action' => 'edit', 'id' => $this->id)), 'Редактировать');
  }

  public function load_comment_form_action() {
    return Route::url('default', array('controller' => 'Comment', 'action' => 'form'));
  }
}

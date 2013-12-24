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
  public $date;
  public $is_admin = FALSE;

  public $scripts = array(
    'jquery',
    'jquery.autosize-min.js',
    'lightbox-2.6.min.js',
    'load_comment_form.js',
    'load_comments.js',
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

  public function link_edit()
  {
    return HTML::anchor(Route::url('default',array('controller' => 'Post', 'action' => 'edit', 'id' => $this->id)), 'Редактировать');
  }

  public function load_comment_form_action()
  {
    return Route::url('default', array('controller' => 'Comment', 'action' => 'form'));
  }

  public function load_comments_action()
  {
    return Route::url('default', array('controller' => 'Comment', 'action' => 'view', 'id' => $this->id));
  }
}

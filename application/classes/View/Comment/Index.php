<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Comment panel view controller.
 **/
class View_Comment_Index extends View_Index {
  public $scripts = array(
    'https://yandex.st/jquery/2.0.3/jquery.min.js',
    'comment_buttons.js'
  );

  /**
   * An internal function to prepare item data.
   **/
  protected function show_item($item)
  {
    die;
    $output = array(
        'date' => $item->posted_at,
        'author_email' => $item->author_email,
        'content' => Markdown::instance()->transform($item->content),
        'author_name' => '',
        'is_approved' => '',
        'comment_id' => $item->id,
        'post_link' => Route::url('default', array('controller' => 'Post', 'action' => 'view', 'id' => $item->post)),
        'edit_link' => Route::url('default', array('controller' => 'Comment', 'action' => 'edit', 'id' => $item->id)),
        'delete_link' => Route::url('default', array('controller' => 'Comment', 'action' => 'delete','id' => $item->id)),
    );
    if (empty($item->author_name))
    {
      $output['author_name'] = Kohana::$config->load('common')->get('anonymous_name');
    }
    else
    {
      $output['author_name'] = $item->author_name;
    }
    $output['is_approved'] = Form::checkbox(
      'is_approved',
      $item->id,
      (boolean) $item->is_approved,
      array(
        'data-edit-url' => Route::url('default', array('controller' => 'Comment','action' => 'edit','id' => $item->id)),
      )
    );
    return $output;
  }


}

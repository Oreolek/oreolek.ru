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
    $output = array(
        'date' => '',
        'author_email' => '',
        'author_name' => '',
        'content' => '',
        'is_approved' => '',
        'edit_link' => '',
        'delete_link' => '',
    );
    $output['date'] = $item->posted_at;
    $output['author_email'] = $item->author_email;
    $output['author_name'] = $item->author_name;
    $output['content'] = Markdown::instance()->transform($item->content);
    $output['post_link'] = Route::url('default', array('controller' => 'Post', 'action' => 'view', 'id' => $item->post));
    $output['comment_id'] = $item->id;
    $output['is_approved'] = Form::checkbox(
      'is_approved',
      $item->id,
      (boolean) $item->is_approved,
      array(
        'data-edit-url' => Route::url('default', array('controller' => 'Comment','action' => 'edit','id' => $item->id)),
      )
    );
    $output['edit_link'] = Route::url('default', array('controller' => 'Comment', 'action' => 'edit', 'id' => $item->id));
    $output['delete_link'] = Route::url('default', array('controller' => 'Comment', 'action' => 'delete', 'id' => $item->id));
    return $output;
  }


}

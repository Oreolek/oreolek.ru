<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Reading view controller.
 * Reading view prints out the content of items, not just the headings.
 * Reading view is suitable only for post items, but it can be used for tag reading.
 **/
class View_Read extends View_Index {
  public $show_date = TRUE;
  public $show_create = FALSE;

  /**
   * An internal function to prepare item data.
   * btw, it can be redefined.
   **/
  protected function show_item($item)
  {
    if (is_null($this->is_admin))
    {
      $this->is_admin = Auth::instance()->logged_in('admin');
    }
    $output = array(
        'date' => '',
        'heading' => '',
        'content' => '',
        'comment_count' => '',
        'edit_link' => '',
        'view_link' => '',
        'comments_link' => ''
    );
    if ($this->show_date)
    {
      $output['date'] = $item->creation_date();
    }
    if ($this->is_admin)
    {
      $output['edit_link'] = Route::url('default', array('controller' => Request::current()->controller(), 'action' => 'edit','id' => $item->id));
    }
    $output['view_link'] = Route::url('default', array('controller' => Request::current()->controller(), 'action' => 'view','id' => $item->id));
    $output['heading'] = $item->name;
    $output['comment_count'] = $item->comment_count;
    $output['comments_link'] = Route::url('default', array('controller' => Request::current()->controller(), 'action' => 'view','id' => $item->id)).'#comments';
    $output['content'] = Text::limit_words(Markdown::instance()->transform($item->content), Kohana::$config->load('common.brief_limit'));
    return $output;
  }
}

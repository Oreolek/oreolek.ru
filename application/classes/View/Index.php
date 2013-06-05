<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Контроллер вида оглавлений
 **/
class View_Index {
  public $show_date = TRUE;
  public $items = NULL;
 
  public function get_items()
  {
    $result = array();
    $colwidth = $this->view_link_colwidth();
    $is_admin = Auth::instance()->logged_in('admin');
    if (is_null($this->items))
    {
      return NULL;
    };
    foreach ($this->items as $item)
    {
      $output = array(
        'date' => '',
        'edit_link' => '',
        'view_link' => '',
        'delete_link' => '',
      );
      if ($this->show_date)
      {
        $output['date'] = $item->creation_date();
      }
      $output['view_link'] = '<a class = "link_view column'.$colwidth.'" href = "'.Route::url('default', array('controller' => Request::current()->controller(), 'action' => 'view','id' => $item->id)).'">'.$item->name.'</a>';
      if ($is_admin)
      {
        $output['edit_link'] = '<a class = "link_edit" href = "'.Route::url('default', array('controller' => Request::current()->controller(), 'action' => 'edit','id' => $item->id)).'">Редактировать</a>';
        $output['delete_link'] = '<a class = "link_delete" href = "'.Route::url('default', array('controller' => Request::current()->controller(), 'action' => 'delete','id' => $item->id)).'">Удалить</a>';
      }
      array_push($result, $output);
    }
    return $result;
  }
  public function header()
  {
    return Request::factory('header/standard')->post('title','Содержание')->execute();
  }

  public function footer()
  {
    return Request::factory('footer/standard')->execute();
  }
  
  protected function view_link_colwidth()
  {
    $columns = 3;
    if (!$this->show_date)
    {
      $columns++;
    }
    if (!Auth::instance()->logged_in('admin'))
    {
      $columns = $columns + 2;
    }
    return $columns;
  }

  public function link_new()
  {
    if (Auth::instance()->logged_in('admin'))
    {
      return '<a href="'.Route::url('default', array('controller' => Request::current()->controller(), 'action' => 'create')).'" class="link_new">Добавить</a>';
    }
    return '';
  }
}

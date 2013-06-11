<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Index view controller.
 **/
class View_Index extends View_Layout {
  public $show_date = TRUE;
  /**
   * Show a link to add new entry
   **/
  public $show_create = TRUE;
  /**
   * Items to show
   **/
  public $items = NULL;
  /**
   * Index description
   **/
  public $content = '';

  /**
   * Pagination controls
   **/
  public function get_paging()
  {
    $current_page = $this->get_current_page();
    $item_count = count($this->items);
    $page_size = Kohana::$config->load('common.page_size');
    $page_count = floor($item_count / $page_size);
    $i = 1;
    $output = '';
    while ($i <= $page_count)
    {
      $output .= '<a href="'.Route::url('pagination', array('controller' => Request::current()->controller(), 'action' => 'index', 'page' => $i)).'"';
      if ($i == $current_page)
      {
        $output .= ' class="active"';
      }
      $output .= '>'.$i.'</a>';
      $i++;
    }
    return $output;
  }

  public function get_items()
  {
    $current_page = $this->get_current_page();
    $page_size = Kohana::$config->load('common.page_size');
    $result = array();
    $colwidth = $this->view_link_colwidth();
    $is_admin = Auth::instance()->logged_in('admin');
    if (is_null($this->items))
    {
      return NULL;
    };
    $item_count = count($this->items);
    if ($item_count > $page_size)
    {
      $page_count = ceil($item_count / $page_size);
      $items = array_slice($this->items->as_array(), $current_page * $page_size, $page_size);
    }
    else
    {
      $items = $this->items;
    }
    foreach ($items as $item)
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
  
  /**
   * Pagination: calculate current page
   **/
  protected function get_current_page()
  {
    $current_page = Request::current()->param('page');
    if (!$current_page)
      return 1;
    return $current_page;
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

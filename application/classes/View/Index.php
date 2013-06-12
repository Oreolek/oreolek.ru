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

  protected $is_admin;

  /**
   * Pagination controls
   **/
  public function get_paging()
  {
    $current_page = $this->get_current_page();
    $item_count = count($this->items);
    $page_size = Kohana::$config->load('common.page_size');
    $page_count = ceil($item_count / $page_size);
    if ($page_count === 1.0)
      return '';
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
    $result = array();
    if (is_null($this->items))
    {
      return NULL;
    };
    $items = $this->filter_items();
    foreach ($items as $item)
    {
      array_push($result, $this->show_item($item));
    }
    return $result;
  }

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
        'edit_link' => '',
        'view_link' => '',
        'delete_link' => '',
    );
    if ($this->show_date)
    {
      $output['date'] = $item->creation_date();
    }
    $output['view_link'] = '<a class = "link_view" href = "'.Route::url('default', array('controller' => Request::current()->controller(), 'action' => 'view','id' => $item->id)).'">'.$item->name.'</a>';
    if ($this->is_admin)
    {
      $output['edit_link'] = '<a class = "link_edit" href = "'.Route::url('default', array('controller' => Request::current()->controller(), 'action' => 'edit','id' => $item->id)).'">Редактировать</a>';
      $output['delete_link'] = '<a class = "link_delete" href = "'.Route::url('default', array('controller' => Request::current()->controller(), 'action' => 'delete','id' => $item->id)).'">Удалить</a>';
    }
    return $output;
  }

  /**
   * Filters $this->items to only current page.
   **/
  protected function filter_items()
  {
    $current_page = $this->get_current_page();
    $page_size = Kohana::$config->load('common.page_size');
    $item_count = count($this->items);
    if ($item_count > $page_size)
    {
      $page_count = ceil($item_count / $page_size);
      return array_slice($this->items->as_array(), ($current_page - 1) * $page_size, $page_size);
    }
    else
    {
      return $this->items;
    }
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

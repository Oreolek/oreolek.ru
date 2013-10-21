<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Tag view controller (index posts with this tag).
 **/
class View_Tag_View extends View_Index {
  public $_view = 'index';
  public $feed_link;
  public $tag_name;
  /**
   * Generate a link to view item by its ID
   * @param integer ID
   **/
  protected function link_view($id)
  {
    return Route::url('default', array('controller' => 'Post', 'action' => 'view','id' => $id));
  }
  
  /**
   * Generate a link to edit item by its ID
   * @param integer ID
   **/
  protected function link_edit($id)
  {
    return Route::url('default', array('controller' => 'Post', 'action' => 'edit','id' => $id));
  }
  
  /**
   * Generate a link to delete item by its ID
   * @param integer ID
   **/
  protected function link_delete($id)
  {
    return Route::url('default', array('controller' => 'Post', 'action' => 'delete','id' => $id));
  }

  /**
   * RSS feed
   **/
  public function feeds()
  {
    return array(
      array(
        'title' => 'Свежие записи с меткой «'.$this->tag_name.'»',
        'url' => $this->feed_link,
      ),
    );
  }

}

<?php defined('SYSPATH') or die('No direct script access.');

class View_Tag_Read extends View_Read {
  public $feed_link;
  public $tag_name;
  
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

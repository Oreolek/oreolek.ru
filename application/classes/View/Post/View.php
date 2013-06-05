<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Контроллер вида оглавлений
 **/
class View_Post_View extends View_Layout {
  public $tags;
  public $id;

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
    return $output;
  }

  public function create_comment()
  {
    return Request::factory('comment/create/' . $this->id)->execute();
  }

  public function comments()
  {
    return Request::factory('comment/view/' . $this->id)->execute();
  }
}

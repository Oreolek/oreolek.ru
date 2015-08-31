<?php defined('SYSPATH') OR die('No direct script access.');

class Feed extends Kohana_Feed {
  /**
    * @argument ORM[] $posts posts to prepare
    * @see Controller/Post::action_feed()
   **/
  public static function prepare_posts($posts, $use_description = TRUE)
  {
    $items = array();
    foreach ($posts as $post)
    {
      $newitem = array(
        'title' => $post->name,
        'description' => '',
        'author' => Kohana::$config->load('common.author_email').' ('.Kohana::$config->load('common.author').')',
        'link' => Route::url('default', array('controller' => 'Post', 'action' => 'view', 'id' => $post->id)),
        'guid' => Route::url('default', array('controller' => 'Post', 'action' => 'view', 'id' => $post->id)),
        'pubDate' => strtotime($post->posted_at),
      );
      if ($use_description){
        $description = '';
        if (!empty($post->password))
        {
          $description = '<p>'.__('Closed post. Access protected by password.').'</p>';
        }
        else
        {
          $description = HTML::tidy(Markdown::instance()->transform($post->content));
        }
        $newitem['description'] = $description;
      }
      array_push($items,$newitem);
    }
    return $items;
  }
}

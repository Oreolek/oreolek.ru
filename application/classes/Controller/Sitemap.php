<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Sitemap extends Controller {
  public function action_index() {
    $sitemap = new Sitemap();
    if ($this->request->param('gzip'))
    {
      $sitemap->gzip = TRUE;
    }
    $posts = Model_Post::get_dates();
    foreach ($posts as $id => $posted_at)
    {
      $url = new Sitemap_URL;
      $url->set_loc(Route::url('default', array('controller' => 'Post', 'action' => 'view', 'id' => $id), TRUE));
      $url->set_last_mod(strtotime($posted_at));
      $url->set_change_frequency('weekly'); // new comments
      $sitemap->add($url);
    }
    $pages = Model_Page::get_ids();
    foreach ($pages as $id)
    {
      $url = new Sitemap_URL;
      $url->set_loc(Route::url('default', array('controller' => 'Page', 'action' => 'view', 'id' => $id), TRUE));
      $url->set_change_frequency('never');
      $sitemap->add($url);
    }
    $this->response->body($sitemap->render());
  }
}

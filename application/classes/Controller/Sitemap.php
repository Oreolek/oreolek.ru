<?php defined('SYSPATH') or die('No direct script access.');
/*
 *  Personal site oreolek.ru source code
 *  Copyright (C) 2014 Alexander Yakovlev
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Affero General Public License as
 *  published by the Free Software Foundation, either version 3 of the
 *  License, or (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Affero General Public License for more details.
 *
 *  You should have received a copy of the GNU Affero General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>. 
 */
/**
 * Sitemap controller. It exists purely for the purpose of building the sitemap.
 **/
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

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
 * Main parent controller. Every controller inherits it.
 **/
class Controller_Layout extends Controller {
  protected $secure_actions = FALSE;
  protected $is_private = FALSE;
  public $auto_render = TRUE;
  public $template = '';

  public function before()
  {
    parent::before();
    $action_name = $this->request->action();
    // clear cache in dev mode
    if (Kohana::$environment == Kohana::DEVELOPMENT)
    {
      Cache::instance('apcu')->delete_all();
    }
    if (
      is_array($this->secure_actions) &&
      array_key_exists($action_name, $this->secure_actions) &&
      Kohana::$environment != Kohana::DEVELOPMENT
    )
    {
      if ( Auth::instance()->logged_in($this->secure_actions[$action_name]) === FALSE)
      {
        if (Auth::instance()->logged_in())
        {
          $this->redirect('error/403');
        }
        else
        {
          $this->redirect('user/signin');
        }
      }
      else
      {
        //user is clear to go but his pages are cache-sensitive
        $this->is_private = TRUE;
      }
    }
  }
  public function after()
  {
    if ($this->auto_render)
    {
      $renderer = Kostache_Layout::factory('layout');
      $this->response->body($renderer->render($this->template, $this->template->_view));
    }
    if ($this->is_private)
    {
      $this->response->headers( 'cache-control', 'private' );
      $this->check_cache();
    }
  }
}

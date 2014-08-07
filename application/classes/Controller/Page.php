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
 * Static page controller.
 **/
class Controller_Page extends Controller_Layout {
  protected $secure_actions = array(
    'drafts' => array('login','admin'),
    'create' => array('login','admin'),
		'edit' => array('login','admin'),
	  'delete' => array('login','admin')
  );
  /**
   * View a page.
   **/
  public function action_view()
  {
    $this->auto_render = FALSE;
    $id = $this->request->param('id');
    $page = ORM::factory('Page', $id);
    if (!$page->loaded())
    {
      $this->redirect('error/404');
    }
    $is_admin = Auth::instance()->logged_in('admin');
    if ($page->is_draft AND !$is_admin)
    {
      $this->redirect('error/403');
    }
    $cache = Cache::instance('apcu');
    $latest_change = $page->posted_at;
    if (!$is_admin)
    {
      $body = $cache->get('page_'.$id);
      if (!empty($body))
      {
        if ($cache->get('page_'.$id.'_changed') === $latest_change)
        {
          $this->response->body($body);
          return;
        }
        else
        {
          $cache->delete('page_'.$id);
        }
      }
    }
    $this->template = new View_Page_View;
    $this->template->title = $page->name;
    $this->template->content = Markdown::instance()->transform($page->content);
    if ($page->is_draft)
    {
      $this->template->title .= ' (черновик)';
    }
    $renderer = Kostache_Layout::factory('layout');
    $body = $renderer->render($this->template, $this->template->_view);
    if (!$is_admin)
    {
      $cache->set('page_'.$id, $body, 60*60*24); //cache page for 1 day
    }
    $cache->set('page_'.$id.'_changed', $latest_change);
    $this->response->body($body);
  }
  /**
   * Page index
   **/
  public function action_index()
  {
    $this->template = new View_Index;
    $this->template->title = 'Содержание';
    $this->template->show_date = FALSE;
    $page_size = Kohana::$config->load('common.page_size');
    $current_page = (int) $this->request->param('page') - 1;
    if ($current_page < 0)
    {
      $current_page = 0;
    }
    $first_item = $page_size * $current_page;
    $this->template->items = ORM::factory('Page')
      ->where('is_draft', '=', '0')
      ->order_by('name', 'ASC')
      ->offset($first_item)
      ->limit($page_size)
      ->find_all(); 
    $this->template->item_count = ORM::factory('Page')
      ->where('is_draft', '=', '0')
      ->count_all();

  }
  public function action_delete()
  {
    $this->template = new View_Delete;
    $id = $this->request->param('id');
    $page = ORM::factory('Page', $id);
    if (!$page->loaded())
    {
      $this->redirect('error/404');
    }
    $this->template->title = 'Удаление страницы';
    $this->template->content_title = $page->name;
    $this->template->content = Markdown::instance()->transform($page->content);

    $confirmation = $this->request->post('confirmation');
    if ($confirmation === 'yes') {
      $page->delete();
      $this->redirect('page/index');
    }
  }
  /**
   * Create a page (for admin)
   **/
  public function action_create()
  {
    $this->template = new View_Page_Edit;
    $this->template->title = 'Новая страница';
    $this->template->errors = array();
    $page = ORM::factory('Page');
    $this->edit_page($page); 
  }
  /**
   * Edit a page (for admin)
   **/
  public function action_edit()
  {
    $this->template = new View_Page_Edit;
    $this->template->title = 'Редактирование страницы';
    $id = $this->request->param('id');
    $page = ORM::factory('Page', $id);
    if (!$page->loaded())
    {
      $this->redirect('error/404');
    }
    $this->edit_page($page);
  }

  /**
   * Edit or create page.
   * Page model should be initialized with empty page (create) or existing one (update).
   **/
  protected function edit_page($page)
  {
    $this->template->errors = array();
    if ($this->request->method() === HTTP_Request::POST) {
      $page->content = $this->request->post('content');
      $page->name = $this->request->post('name');
      $page->is_draft = $this->request->post('is_draft');
      try {
        if ($page->check())
        {
          $page->save();
        }
      }
      catch (ORM_Validation_Exception $e)
      {
        $this->template->errors = $e->errors('');
      }
      if (empty($this->template->errors))
      {
        $this->redirect('page/view/' . $page->id);
      }
    }
    $this->template->model = $page;
  }

  /**
   * Draft index
   **/
  public function action_drafts()
  {
    $this->template = new View_Index;
    $this->template->title = 'Содержание (черновики)';
    $this->template->show_date = FALSE;
    $this->template->items = ORM::factory('Page')
      ->where('is_draft', '=', '1')
      ->order_by('name', 'DESC')
      ->find_all(); 
  }
}

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
 * Tag controller.
 * Tags are case-sensitive and can be applied to blog posts.
 **/
class Controller_Tag extends Controller_Layout {
  public $template = 'tag/view';
  protected $secure_actions = array(
		'edit' => array('login', 'admin'),
		'create' => array('login', 'admin'),
    'delete' => array('login', 'admin')
  );

  /**
   * Index all posts with this tag.
   **/
  public function action_view()
  {
    $id = $this->request->param('id');
    $tag = ORM::factory('Tag',$id);
    if (!$tag->loaded())
    {
      $this->redirect('error/404');
    }
    $this->template = new View_Tag_View;
    $this->template->title = 'Тег: '.$tag->name;
    $this->template->show_date = TRUE;
    $this->template->show_create = FALSE;
    $this->template->items = $tag->posts->where('is_draft', '=', '0')->find_all();
    $this->template->content = Markdown::instance()->transform($tag->description);
    $this->template->feed_link = Route::url('default', array('controller' => 'Tag', 'action' => 'feed', 'id' => $id));
    $this->template->tag_name = $tag->name;
  }

  /**
   * Read all posts in tag.
   **/
  public function action_read()
  {
    $id = $this->request->param('id');
    $tag = ORM::factory('Tag',$id);
    if (!$tag->loaded())
    {
      $this->redirect('error/404');
    }
    $this->template = new View_Read;
    $this->template->title = 'Записи по тегу: '.$tag->name;
    $this->template->items = $tag->posts->where('is_draft', '=', '0')->find_all();
    $this->template->content = Markdown::instance()->transform($tag->description);
    $this->template->feed_link = Route::url('default', array('controller' => 'Tag', 'action' => 'feed', 'id' => $id));
    $this->template->tag_name = $tag->name;
  }

  /**
   * Index all tags.
   **/
  public function action_index()
  {
    $this->template = new View_Index;
    $this->template->title = 'Список меток';
    $this->template->show_date = FALSE;
    $this->template->items = ORM::factory('Tag')->order_by('name', 'ASC')->find_all();
  }

  public function action_edit()
  {
    $id = $this->request->param('id');
    $tag = ORM::factory('Tag',$id);
    if (!$tag->loaded())
    {
      $this->redirect('error/404');
    }
    $this->template = new View_Edit;
    $this->template->title = 'Редактирование тега: '.$tag->name;
    $this->template->errors = array();
    $this->template->controls = array(
      'name' => 'input',
      'description' => 'text',
    );
    if (HTTP_Request::POST == $this->request->method()) {
      $tag->description = $this->request->post('description');
      $tag->name = $this->request->post('name');
      try {
        if ($tag->check())
        {
          $tag->update();
        }
      }
      catch (ORM_Validation_Exception $e)
      {
        $this->template->errors = $e->errors();
      }
      if (empty($this->template->errors))
      {
        $this->redirect('tag/view/' . $tag->id);
      }
    }
    $this->template->model = $tag;
  }

  public function action_create()
  {
    $this->template = new View_Edit;
    $tag = ORM::factory('Tag');
    $this->template->title = 'Создание тега';
    $this->template->errors = array();
    $this->template->controls = array(
      'name' => 'input',
      'description' => 'text',
    );
    if ($this->request->method() === HTTP_Request::POST) {
      $tag->description = $this->request->post('description');
      $tag->name = $this->request->post('name');
      try {
        if ($tag->check())
        {
          $tag->create();
        }
      }
      catch (ORM_Validation_Exception $e)
      {
        $this->template->errors = $e->errors();
      }
      if (empty($this->template->errors))
      {
        $this->redirect('tag/view/' . $tag->id);
      }
    }
    $this->template->model = $tag;
  }

  public function action_delete()
  {
    $this->template = new View_Delete;
    $id = $this->request->param('id');
    $tag = ORM::factory('Tag', $id);
    if (!$tag->loaded())
    {
      $this->redirect('error/404');
    }
    $this->template->title = 'Удаление метки дневника';
    $this->template->content_title = $tag->name.' (записей: '.$tag->posts->count_all().')';
    $this->template->content = Markdown::instance()->transform($tag->description);

    $confirmation = $this->request->post('confirmation');
    if ($confirmation === 'yes') {
      $tag->delete();
      $this->redirect('tag/index');
    }
  }

  /**
   * Atom feed for fresh posts in tag
   **/
  public function action_feed()
  {
    $this->auto_render = false;
    $id = $this->request->param('id');
    $tag = ORM::factory('Tag',$id);
    if (!$tag->loaded())
    {
      $this->redirect('error/404');
    }
    $posts = $tag->posts
      ->where('is_draft', '=', '0')
      ->order_by('posted_at', 'DESC')
      ->limit(10)
      ->find_all(); 
    $info = array(
        'title' => Kohana::$config->load('common.title'),
        'author' => Kohana::$config->load('common.author'),
        'pubDate' => $posts[0]->posted_at,
        );
    $items = array();
    foreach ($posts as $post)
    {
      array_push($items, array(
            'title' => $post->name,
            'description' => Markdown::instance()->transform($post->content),
            'link' => 'post/view/' . $post->id,
            ));
    }
    $this->response->body( Feed::create($info, $items) );
  }

}

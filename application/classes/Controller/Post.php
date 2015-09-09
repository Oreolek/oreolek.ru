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
 * Controller of blog posts.
 **/

class Controller_Post extends Controller_Layout {
  protected $secure_actions = array(
    'drafts' => array('login','admin'),
    'create' => array('login','admin'),
    'edit' => array('login','admin'),
    'delete' => array('login','admin')
  );
  /**
   * View a post.
   **/
  public function action_view()
  {
    $this->auto_render = FALSE;
    $id = $this->request->param('id');
    $post = ORM::factory('Post', $id);
    if (!$post->loaded())
    {
      $this->redirect('error/404');
    }
    $is_admin = Auth::instance()->logged_in('admin');
    if (($post->is_draft == TRUE || strtotime($post->posted_at) > time()) AND !$is_admin)
    {
      $this->redirect('error/403');
    }
    if (!empty($post->password) && !Auth::instance()->logged_in('admin'))
    {
      $password_post = $this->request->post('password');
      if ($password_post)
      {
        Cookie::set('password', $password_post);
      }
      $password = Cookie::get('password', $password_post);
      if ($password != $post->password)
      {
        $this->auto_render = TRUE;
        $this->template = new View_Post_Password;
        $this->template->model = ORM::factory('Post');
        $this->template->controls = array(
          'password' => 'password',
        );
        $this->template->message = __('This post is closed. Enter password to read text and comments.');
        if (!empty($password))
        {
          $this->template->message = __('Saved password does not match. Try again.');
        }
        return;
      }
    }
    $cache = Cache::instance('apcu');
    $latest_change = $post->posted_at;
    $this->template = new View_Post_View;
    $this->template->is_admin = $is_admin;
    $this->template->id = $id;
    $post_cached = array();
    if (!$is_admin)
    {
      $post_cached = $cache->get('post_'.$id);
      if (!empty($post))
      {
        if ($cache->get('post_'.$id.'_changed') !== $latest_change)
        {
          $cache->delete('post_'.$id);
          $post_cached['content'] = Markdown::instance()->transform($post->content);
          $post_cached['tags'] = $post->tags->find_all();
          $post_cached['name'] = $post->name;
          $post_cached['date'] = date('c', strtotime($post->creation_date()));
        }
      }
    }
    $this->template->content = $post_cached['content'];
    $this->template->tags = $post_cached['tags'];
    $this->template->title = $post_cached['name'];
    $this->template->date = $post_cached['date'];
    if ($post->is_draft) $this->template->title .= ' '.__('(draft)');
    $renderer = Kostache_Layout::factory('layout');
    $body = $renderer->render($this->template, $this->template->_view);
    if (!$is_admin)
    {
      $cache->set('post_'.$id, $post_cached, 60*60*24); //cache page for 1 day
      $cache->set('post_'.$id.'_changed', $latest_change);
    }
    $this->response->body($body);
  }

  public function action_edit()
  {
    $this->template = new View_Post_Edit;
    $this->template->title = __('Edit post');
    $id = $this->request->param('id');
    $post = ORM::factory('Post', $id);
    if (!$post->loaded())
    {
      $this->redirect('error/404');
    }
    $this->edit_post($post);
  }

  /**
   * Short index with only post headings
   **/
  public function action_index()
  {
    $this->template = new View_Index;
    $page_size = Kohana::$config->load('common.page_size');
    $current_page = (int) $this->request->param('page') - 1;
    if ($current_page < 0)
    {
      $current_page = 0;
    }
    $first_item = $page_size * $current_page;
    $this->template->items = ORM::factory('Post')
      ->where('is_draft', '=', '0')
      ->and_where(DB::expr('DATEDIFF(SYSDATE(), `post`.`posted_at`)'), '>=', '0')
      ->order_by('posted_at', 'DESC')
      ->offset($first_item)
      ->limit($page_size)
      ->find_all();
    $this->template->item_count = ORM::factory('Post')
      ->where('is_draft', '=', '0')
      ->count_all();
  }
  
  /**
   * Actually read all posts on one page
   **/
  public function action_read()
  {
    $this->auto_render = FALSE;
    $cache = Cache::instance('apcu');
    $logged_in = Auth::instance()->logged_in();
    $current_page = (int) $this->request->param('page') - 1;
    if ($current_page < 0)
    {
      $current_page = 0;
    }
    if ($logged_in === FALSE)
    {
      $body = $cache->get('read_posts_'.$current_page);
      if (!empty($body))
      {
        $latest_change = Model_Post::get_latest_date();
        if ($cache->get('latest_post') === $latest_change)
        {
          $this->response->body($body);
          return;
        }
        else
        {
          $cache->set('latest_post', $latest_change);
          $cache->delete('read_posts_'.$current_page);
        }
      }
    }
    $this->template = new View_Post_Read;
    $this->template->title = 'Дневник';
    $page_size = Kohana::$config->load('common.page_size');
    $first_item = $page_size * $current_page;
    $this->template->items = ORM::factory('Post')
      ->with_count('comments', 'comment_count')
      ->where('is_draft', '=', '0')
      ->and_where(DB::expr('DATEDIFF(SYSDATE(), `post`.`posted_at`)'), '>=', '0')
      ->order_by('posted_at', 'DESC')
      ->offset($first_item)
      ->limit($page_size)
      ->find_all();
    $this->template->item_count = ORM::factory('Post')
      ->where('is_draft', '=', '0')
      ->count_all();
    $renderer = Kostache_Layout::factory('layout');
    $body = $renderer->render($this->template, $this->template->_view);
    if ($logged_in === FALSE)
    {
      $cache->set('read_posts_'.$current_page, $body, 60*60*24); //cache page for 1 day 
    }
    $this->response->body($body);
  }

  /**
   * 10 fresh posts
   **/
  public function action_fresh()
  {
    $this->template = new View_Index;
    $this->template->title = __('Fresh posts');
    $this->template->item_count = 10;
    $this->template->need_paging = FALSE;
    $this->template->items = ORM::factory('Post')
      ->where('is_draft', '=', '0')
      ->and_where(DB::expr('DATEDIFF(SYSDATE(), `post`.`posted_at`)'), '>=', '0')
      ->order_by('posted_at', 'DESC')
      ->offset(0)
      ->limit(10)
      ->find_all(); 
  }

  /**
   * RSS feed for fresh posts
   **/
  public function action_feed()
  {
    $this->auto_render = false;
    $posts = ORM::factory('Post')
      ->where('is_draft', '=', '0')
      ->and_where(DB::expr('DATEDIFF(SYSDATE(), `post`.`posted_at`)'), '>=', '0')
      ->order_by('posted_at', 'DESC')
      ->limit(10)
      ->find_all(); 
    $info = array(
        'title' => Kohana::$config->load('common.title'),
        'pubDate' => strtotime($posts[0]->posted_at),
        'description' => ''
    );
    $items = Feed::prepare_posts($posts);
    
    $this->response->headers('Content-type', 'application/rss+xml');
    $this->response->body( Feed::create($info, $items) );
  }

  public function action_delete()
  {
    $this->template = new View_Delete;
    $id = $this->request->param('id');
    $post = ORM::factory('Post', $id);
    if (!$post->loaded())
    {
      $this->redirect('error/404');
    }
    $this->template->title = __('Post deletion');
    $this->template->content_title = $post->name;
    $this->template->content = Markdown::instance()->transform($post->content);

    $confirmation = $this->request->post('confirmation');
    if ($confirmation === 'yes') {
      $post->delete();
      $this->redirect('post/index');
    }
  }

  /**
   * Create a post (for admin)
   **/
  public function action_create()
  {
    $this->template = new View_Post_Edit;
    $this->template->title = __('New post');
    $this->template->errors = array();
    $post = ORM::factory('Post');
    $this->edit_post($post);
  }

  /**
   * Draft index
   **/
  public function action_drafts()
  {
    $this->template = new View_Index;
    $this->template->title = 'Содержание дневника (черновики)';
    $this->template->items = ORM::factory('Post')
      ->where('is_draft', '=', '1')
      ->order_by('posted_at', 'DESC')
      ->limit(10)
      ->find_all(); 
  }

  /**
   * Edit or create post.
   * Post model should be initialized with empty post (create) or existing one (update).
   **/
  protected function edit_post($post)
  {
    $this->template->errors = array();
    $this->template->tags = $post->tags->find_all();
    
    if ($this->request->method() === HTTP_Request::POST) {
      $post->content = $this->request->post('content');
      $post->name = $this->request->post('name');
      $post->password = $this->request->post('password');
      $posted_at = strtotime($this->request->post('posted_at'));
      if ($posted_at > 0)
      {
        $post->posted_at = date('c', $posted_at);
      }
      else
      {
        $post->posted_at = NULL;
      }
      if (empty($post->posted_at))
      {
        $post->posted_at = date('c');
      }
      if ($this->request->is_ajax())
      {
        $this->auto_render = FALSE;
        if ($this->request->post('mode') === 'save')
        {
          $post->save();
        }
        $retval = array(
          'preview' => Markdown::instance()->transform($post->content),
        );
        $this->response->body(json_encode($retval));
        return;
      }
      $post->is_draft = $this->request->post('is_draft');
      $tags = $this->request->post('tags');
      $validation = $post->validate_create($this->request->post());
      $mode = 'edit';
      if ($this->request->post('preview') != '')
      {
        $mode = 'view';
      }
      try
      {
        if ($validation->check())
        {
          if ($mode === 'edit')
          {
            $post->save();
          }
        }
        else
        {
          $this->template->errors = $validation->errors('default');
        }
        if ($mode === 'view' and !empty($post->content))
        {
          $this->template->preview = Markdown::instance()->transform($post->content);
        }
      }
      catch (ORM_Validation_Exception $e)
      {
        $this->template->errors = $e->errors('default');
      }
      if (empty($this->template->errors) && $mode === 'edit')
      {
        if (!empty($tags))
        {
          $tags = explode(',', $tags);
          $tags = array_map('trim', $tags);
          //adding new tags
          foreach ($tags as $tag)
          {
            $model = ORM::factory('Tag')->where('name', '=', $tag)->find();
            if (!$model->loaded())
            {
              $model = ORM::factory('Tag');
              $model->name = $tag;
              $model->create();
            }
            if (!$post->has('tags', $model->id))
            {
              $post->add('tags', $model->id);
            }
          }
          
          $tag_models = $post->tags->find_all();
          //deleting unused tags
          foreach ($tag_models as $tag)
          {
            if (array_search($tag->name, $tags) === FALSE)
            {
              $post->remove('tags', $tag->id);
            }
          }
        }
        $this->redirect('post/view/' . $post->id);
      }
    }
    $this->template->model = $post;
  }

  public function action_search()
  {
    $this->template = new View_Search;
  }
}

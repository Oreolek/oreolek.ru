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
 * Comment controller. Heavily uses AJAX.
 **/
class Controller_Comment extends Controller_Layout {
  protected $secure_actions = array(
    'index' => array('login','admin'),
    'edit' => array('login','admin'),
    'delete' => array('login','admin'),
  );

  /**
   * AJAX action to create a comment.
   **/
  public function action_create()
  {
    $this->auto_render = FALSE;
    if (HTTP_Request::POST != $this->request->method()) {
      throw new HTTP_Exception_500('Только запросы POST');
    }
    $post_id = $this->request->param('id');
    if (empty($post_id))
    {
      throw new HTTP_Exception_500('Не указан ID записи');
    }
    $comment = ORM::factory('Comment');
    $comment->post_id = $post_id;
    $comment->content = HTML::chars($this->request->post('content'));
    $comment->author_name = $this->request->post('author_name');
    $comment->author_email = $this->request->post('author_email');
    $email = $this->request->post('email');
    $title = $this->request->post('title');
    $name = $this->request->post('name');
    try
    {
      if ($comment->check()) {
        if (Kohana::$config->load('common')->get('comment_approval'))
        {
          if (
            Model_Comment::antispam_check($comment->content) === FALSE OR
            Model_Comment::useragent_check(Request::user_agent('browser')) === FALSE OR
            $email != '' OR
            $title != '' OR
            $name != ''
          )
          {
            Session::instance()->set('flash_error', 'Ваш комментарий принят. К сожалению, но он выглядел подозрительно для технического анализатора, поэтому был помечен как спам. Он не будет отображаться в списке комментариев, пока владелец блога не укажет обратное.', '<br>');
            $comment->is_approved = Model_Comment::STATUS_SPAM;
          }
          else
          {
            $comment->is_approved = Model_Comment::STATUS_APPROVED;
          }
        }
        else
        {
          $comment->is_approved = Model_Comment::STATUS_PENDING;
        }
        $comment->create();
      }
    }
    catch (ORM_Validation_Exception $e)
    {
      Session::instance()->set('flash_error', implode($e->errors(''), '<br>'));
      Session::instance()->set('comment_body', $comment->content);
      Session::instance()->set('comment_author', $comment->author_name);
      Session::instance()->set('comment_email', $comment->author_email);
    }
    $this->redirect('post/view/' . $post_id);
    unset($email);
  }

  /**
   * AJAX action to get a form for a new comment.
   **/
  public function action_form()
  {
    $this->auto_render = FALSE;
    if ( ! Fragment::load('comment_form', Date::DAY * 7))
    {
      $model = ORM::factory('Comment');
      $model->content = Session::instance()->get_once('comment_body');
      $model->author_name = Session::instance()->get_once('comment_author');
      $model->author_email = Session::instance()->get_once('comment_email');
      $inputs = array();
      $inputs['author_email'] = Form::orm_textinput($model, 'author_email');
      $inputs['author_name'] = Form::orm_textinput($model, 'author_name');
      $inputs['content'] = Form::orm_textarea($model, 'content');
      $this->template = new View_Comment_Form;
      $this->template->url = URL::site('comment/create/-ID-');
      $this->template->inputs = $inputs;
      $renderer = Kostache::factory();
      $this->response->body($renderer->render($this->template, $this->template->_view));

      Fragment::save();
    }
  }

  public function action_index()
  {
    $this->template = new View_Comment_Index;
    $this->template->title = 'Комментарии дневника';
    $this->template->items = ORM::factory('Comment')->order_by('posted_at', 'DESC')->find_all();
  }

  public function action_edit()
  {
    $id = $this->request->param('id');
    $model = ORM::factory('Comment', $id);
    if (!$model->loaded())
    {
      $this->redirect('error/404');
    }
    $this->template = new View_Edit;
    $this->template->errors = array();
    $this->template->title = 'Редактирование комментария';
    $this->template->controls = array(
      'author_name' => 'input',
      'author_email' => 'input',
      'content' => 'text',
      'is_approved' => 'checkbox'
    );
    if ($this->request->method() === HTTP_Request::POST) {
      $model->values($this->request->post());
      // AJAX JSON checks
      $is_approved = $this->request->post('is_approved');
      if ($is_approved === 'true')
      {
        $model->is_approved = TRUE;
      }
      if ($is_approved === 'false')
      {
        $model->is_approved = FALSE;
      }
      $validation = $model->validate_create($this->request->post());
      try
      {
        if ($model->check())
        {
          $model->update();
        }
        else
        {
          $this->template->errors = $validation->errors('comment');
        }
      }
      catch (ORM_Validation_Exception $e)
      {
        $this->template->errors = $e->errors('comment');
      }
      if (empty($this->template->errors) AND !$this->request->is_ajax())
      {
        $this->redirect('post/view/' . $model->post);
      }
    }
    $this->template->model = $model;
  }

  public function action_delete()
  {
    $id = $this->request->param('id');
    $model = ORM::factory('Comment', $id);
    if (!$model->loaded())
    {
      $this->redirect('error/404');
    }
    $this->template = new View_Delete;
    $this->template->title = 'Удаление комментария';
    $this->template->content_title = 'Комментарий от '.$model->author_name;
    $this->template->content = Markdown::instance()->transform($model->content);

    $confirmation = $this->request->post('confirmation');
    if ($confirmation === 'yes') {
      $model->delete();
      $this->redirect('comment/index');
    }

  }

  /**
   * RSS feed for fresh comments
   **/
  public function action_feed()
  {
    $this->auto_render = false;
    $comments = ORM::factory('Comment')
      ->where('is_approved', '=', '1')
      ->order_by('posted_at', 'DESC')
      ->limit(10)
      ->find_all(); 
    $info = array(
        'title' => Kohana::$config->load('common.title').' (комментарии)',
        'pubDate' => strtotime($comments[0]->posted_at),
        'description' => ''
    );
    $items = array();
    foreach ($comments as $comment)
    {
      array_push($items, array(
            'title' => $comment->author_name,
            'description' => Markdown::instance()->transform($comment->content),
            'author' => $comment->author_email,
            'link' => Route::url('default', array('controller' => 'Post', 'action' => 'view', 'id' => $comment->post_id)).'#comment_'.$comment->id,
            'guid' => Route::url('default', array('controller' => 'Post', 'action' => 'view', 'id' => $comment->post_id)).'#comment_'.$comment->id,
            'pubDate' => strtotime($comment->posted_at),
      ));
    }
    $this->response->headers('Content-type', 'application/rss+xml');
    $this->response->body( Feed::create($info, $items) );

  }

  /**
   * View comments by post ID
   * AJAX action to load comments
   **/
  public function action_view()
  {
    $this->auto_render = FALSE;
    $cache = Cache::instance('apcu');
    $id = $this->request->param('id');
    $body = $cache->get('comments_'.$id);
    if (!empty($body))
    {
      $latest_change = Model_Comment::get_latest_date($id);
      if ($cache->get('comments_'.$id.'_changed') === $latest_change)
      {
        $this->response->body($body);
        return;
      }
      else
      {
        $cache->set('comments_'.$id.'_changed', $latest_change);
        $cache->delete('comments_'.$id);
      }
    }
    $this->template = new View_Comment_View;
    $this->template->comments = ORM::factory('Comment')
      ->where('post_id', '=', $id)
      ->where('is_approved', '=', Model_Comment::STATUS_APPROVED)
      ->order_by('posted_at', 'ASC')
      ->find_all();
    $renderer = Kostache::factory();
    $body = $renderer->render($this->template, $this->template->_view);
    $cache->set('comments_'.$id, $body, 60*60*24); 
    $this->response->body($body);
  }

}

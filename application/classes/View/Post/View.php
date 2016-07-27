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
 * Post view controller.
 **/
class View_Post_View extends View_Layout {
  /**
   * Post tag models.
   **/
  public $tags;
  /**
   * Post ID
   **/
  public $id;
  public $date;
  public $is_admin = FALSE;

  public $scripts = array(
    'autosize',
    'load_comment_form.js',
    'load_comments.js',
    'https://yandex.st/share/share.js'
  );

  public function get_tags()
  {
    $output = 'Теги: ';
    if (count($this->tags) > 0)
    {
      $i = 0;
      foreach ($this->tags as $tag)
      {
        if ($i > 0) $output .= ', ';
        $output .= '<a href="'.URL::site('tag/view/'.$tag->id).'"><span property="keywords">'.$tag->name.'</span></a>';
        $i++;
      }
    }
    else 
    {
      return '';
    }
    return $output;
  }

  public function link_edit()
  {
    return HTML::anchor(Route::url('default',array('controller' => 'Post', 'action' => 'edit', 'id' => $this->id)), 'Редактировать');
  }

  public function load_comment_form_action()
  {
    return Route::url('default', array('controller' => 'Comment', 'action' => 'form'));
  }

  public function load_comments_action()
  {
    return Route::url('default', array('controller' => 'Comment', 'action' => 'view', 'id' => $this->id));
  }

  public function get_breadcrumbs() {
    return '<ol class="breadcrumb"><li><a href="/">Дневник</a></li><li class="active">'.$this->title.'</li></ol>';
  }

  public function donate()
  {
    return array(
      'heading' => 'Понравилось? Поддержите, чтобы таких статей было больше!',
      'code' => '
        <form method="post" target="_blank" action="https://money.yandex.ru/quickpay/confirm.xml">
          <input name="receiver" value="410011560528046" type="hidden">
          <input name="label" value="" type="hidden">
          <input name="quickpay-form" value="small" type="hidden">
          <input name="is-inner-form" value="true" type="hidden">
          <input name="targets" value="oreolek.ru на новые статьи" type="hidden">
          <input name="sum" value="100" maxlength="8" type="hidden">
          <input name="successURL" value="" type="hidden">
          <input name="paymentType" value="AC" type="hidden">
          <button type="submit" class="btn btn-general">Яндекс</button>
        </form>
        <form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_blank">
          <input type="hidden" name="cmd" value="_s-xclick">
          <input type="hidden" name="hosted_button_id" value="M5RYZF8BK7R8W">
          <button type="submit" class="btn btn-general">Paypal</button>
        </form>',
    );
  }
}

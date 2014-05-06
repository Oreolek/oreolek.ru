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
 * Layout view controller. It is the parent view of them all (except for AJAX ones).
 **/
class View_Layout {
  public $_view = NULL;
  public $title = '';
  public $scripts = array();
  public $base_scripts = array(
    'hyphenator.min.js',
    'webfont.js',
    'jquery',
    'bootstrap'
  );
 
  /**
   * Inherited paging function
   **/
  public function get_paging() {}
  
  public function site_title()
  {
    if (Auth::instance()->logged_in())
    {
      return 'Добро пожаловать, '.Auth::instance()->get_user()->username;
    }
    else
    {
      return Kohana::$config->load('common.title');
    }
  }
  public function stylesheet()
  {
    return HTML::style('https://netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.min.css')."\n".Less::compile(APPPATH.'assets/stylesheets/main', 'all');
  }

  public function get_content()
  {
    return Kostache::factory()->render($this->content);
  }

  public function scripts()
  {
    $scripts = array_merge ($this->base_scripts, $this->scripts);
    $temp = "";
    foreach($scripts as $script):
      if (strstr($script, '://') === FALSE) //no protocol given, script is local
      {
        if ($script === 'jquery') // CDN shortcut
        {
          $temp .= HTML::script('https://yandex.st/jquery/2.1.0/jquery.min.js')."\n";
        }
        elseif ($script === 'bootstrap')
        {
          $temp .= HTML::script('https://yandex.st/bootstrap/3.1.1/js/bootstrap.min.js')."\n";
        }
        else
        {
          $temp .= HTML::script('application/assets/javascript/'.$script)."\n";
        }
      }
      else
      {
        $temp .= HTML::script($script)."\n";
      }
    endforeach;
    $temp .= $this->stat_scripts();
    return $temp;
  }

  /**
   * Old IE compatibility patch JS
   **/
  public function ie_scripts()
  {
    $scripts = array('html5shiv.js','css3-mediaqueries.js');
    $retval = '';
    foreach ($scripts as $script)
    {
      $retval .= HTML::script('application/assets/javascript/'.$script)."\n";
    }
    return $retval;
  }

  public function favicon()
  {
    return URL::site('favicon.ico');
  }

  public function navigation()
  {
    $result = array();
    $drop_links = array(
      'Свежие записи' => 'post/fresh',
      'Все записи' => 'post/index',
      'Метки' => 'tag/index'
    );
    $navigation = array(
      'Страницы' => 'page/index',
      'О блоге' => 'page/view/1',
    );
    if (!Auth::instance()->logged_in())
    {
      $navigation['Вход'] = 'user/signin';
    }
    else
    {
      $navigation = array_merge($navigation, array(
        'Комментарии' => 'comment/index',
        'Черновики страниц' => 'page/drafts',
        'Заметки' => 'note/index',
      ));
      $drop_links['Черновики дневника'] = 'post/drafts';
    }

    foreach ($navigation as $key => $value)
    {
      $result[] = HTML::anchor($value, $key);
    }

    $dropdown = array(
      'name' => 'Дневник',
      'links' => array()
    );

    foreach ($drop_links as $key => $value)
    {
      $dropdown['links'][] = HTML::anchor($value, $key);
    }
    return array(
      'links' => $result,
      'dropdown' => $dropdown,
      'search_url' => Route::url('default', array('controller' => 'Post', 'action' => 'search'))
    );
  }

  /**
   * Inline statistic scripts
   **/
  protected function stat_scripts()
  {
    $output = '';
    $yandex_metrika_id = Kohana::$config->load('stats.yandex_metrika_id');
    if (!empty($yandex_metrika_id))
    {
      $yandex_metrika = '<!-- Yandex.Metrika counter --><script type="text/javascript">(function (d, w, c) { (w[c] = w[c] || []).push(function() { try { w.yaCounter11115457 = new Ya.Metrika({id:11115457, webvisor:true, clickmap:true, trackLinks:true, accurateTrackBounce:true}); } catch(e) { } }); var n = d.getElementsByTagName("script")[0], s = d.createElement("script"), f = function () { n.parentNode.insertBefore(s, n); }; s.type = "text/javascript"; s.async = true; s.src = (d.location.protocol == "https:" ? "https:" : "http:") + "//mc.yandex.ru/metrika/watch.js"; if (w.opera == "[object Opera]") { d.addEventListener("DOMContentLoaded", f, false); } else { f(); } })(document, window, "yandex_metrika_callbacks");</script><noscript><div><img src="//mc.yandex.ru/watch/11115457" style="position:absolute; left:-9999px;" alt="" /></div></noscript><!-- /Yandex.Metrika counter -->';
      $output .= sprintf($yandex_metrika, $yandex_metrika_id, $yandex_metrika_id, $yandex_metrika_id);
    }
    return $output;
  }

  /**
   * RSS feed array
   **/
  public function feeds()
  {
    $post_feed = Route::url('default', array('controller' => 'Post', 'action' => 'feed'));
    $comment_feed = Route::url('default', array('controller' => 'Comment', 'action' => 'feed'));
    return array(
      array(
        'title' => 'Свежие записи дневника',
        'url' => $post_feed,
      ),
      array(
        'title' => 'Свежие комментарии',
        'url' => $comment_feed
      )
    );
  }

  public function flashes()
  {
    $session = Session::instance();
    return array(
      'info' => $session->get_once('flash_info'),
      'success' => $session->get_once('flash_success'),
      'error' => $session->get_once('flash_error'),
      'warning' => $session->get_once('flash_warning'),
    );
  }

}

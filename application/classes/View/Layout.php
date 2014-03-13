<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Layout view controller
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
  public $errors;
 
  /**
   * Inherited paging function
   **/
  public function get_paging() {}
  
  public function has_errors()
  {
    return !empty($this->errors);
  }

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
      $result[] = HTML::anchor(URL::site('/'.$value), $key);
    }

    $dropdown = array(
      'name' => 'Дневник',
      'links' => array()
    );

    foreach ($drop_links as $key => $value)
    {
      $dropdown['links'][] = HTML::anchor(URL::site('/'.$value), $key);
    }
    return array(
      'links' => $result,
      'dropdown' => $dropdown,
      'search_url' => Route::url('default', array('controller' => 'Post', 'action' => 'search'))
    );
  }

  public function get_errors()
  {
    $result = array();
    foreach ($this->errors as $key => $string)
    {
      array_push($result, $string);
    }
    return $result;
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
}

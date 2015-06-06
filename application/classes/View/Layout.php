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
    'hyphenator',
    'jquery',
    'bootstrap',
    'moment',
    'moment_ru.js',
    'common.js',
    'lightbox',
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
    $scripts = array_unique(array_merge ($this->base_scripts, $this->scripts));
    $temp = "";
    foreach($scripts as $script):
      if (strstr($script, '://') === FALSE) //no protocol given, script is local
      {
        switch ($script) // CDN shortcuts
        {
          case 'jquery':
            $temp .= HTML::script('https://code.jquery.com/jquery-2.1.3.min.js')."\n";
            break;

          case 'bootstrap':
            $temp .= HTML::script('https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js')."\n";
            break;

          case 'hyphenator':
            $temp .= HTML::script('https://cdn.jsdelivr.net/hyphenator/4.3.0/hyphenator.min.js')."\n";
            break;
          
          case 'moment':
            $temp .= HTML::script('https://cdn.jsdelivr.net/momentjs/2.10.2/moment.min.js')."\n";
            break;

          case 'autosize':
            $temp .= HTML::script('https://cdn.jsdelivr.net/jquery.autosize/3.0.3/autosize.min.js')."\n";
            break;

          case 'lightbox':
            $temp .= HTML::script('https://cdn.jsdelivr.net/lightbox2/2.7.1/js/lightbox.min.js')."\n";
            break;

          default:
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
    $scripts = array(
      'https://cdn.jsdelivr.net/html5shiv/3.7.2/html5shiv.min.js',
      'https://cdn.jsdelivr.net/css3-mediaqueries/0.1/css3-mediaqueries.min.js'
    );
    $retval = '';
    foreach ($scripts as $script)
    {
      $retval .= HTML::script($script)."\n";
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
    $config = Kohana::$config->load('common');
    return array(
      'links' => $result,
      'dropdown' => $dropdown,
      'text_navigation' => 'Переключить навигацию',
      'author_img' => $config['author_img'],
      'author_img_alt' => $config['author_img_alt'],
      'author_about' => $config['author_about'],
      'search' => <<<EOL
<div class="ya-site-form ya-site-form_inited_no" onclick="return {'action':'/Post/search','arrow':false,'bg':'transparent','fontsize':12,'fg':'#000000','language':'ru','logo':'rb','publicname':'Поиск по oreolek.ru','suggest':true,'target':'_self','tld':'ru','type':3,'usebigdictionary':true,'searchid':2201150,'webopt':false,'websearch':false,'input_fg':'#000000','input_bg':'#ffffff','input_fontStyle':'normal','input_fontWeight':'normal','input_placeholder':null,'input_placeholderColor':'#000000','input_borderColor':'#7f9db9'}"><form action="http://yandex.ru/sitesearch" method="get" target="_self"><input type="hidden" name="searchid" value="2201150"/><input type="hidden" name="l10n" value="ru"/><input type="hidden" name="reqenc" value="utf-8"/><input type="search" name="text" value=""/><input type="submit" value="Найти"/></form></div><script type="text/javascript">(function(w,d,c){var s=d.createElement('script'),h=d.getElementsByTagName('script')[0],e=d.documentElement;if((' '+e.className+' ').indexOf(' ya-page_js_yes ')===-1){e.className+=' ya-page_js_yes';}s.type='text/javascript';s.async=true;s.charset='utf-8';s.src=(d.location.protocol==='https:'?'https:':'http:')+'//site.yandex.net/v2.0/js/all.js';h.parentNode.insertBefore(s,h);(w[c]||(w[c]=[])).push(function(){Ya.Site.Form.init()})})(window,document,'yandex_site_callbacks');</script>
EOL
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

  public function rss_link()
  {
    return HTML::anchor(Route::url('default', array('controller' => 'Post', 'action' => 'feed')), '<span class="fa fa-rss">&nbsp;</span> RSS дневника');
  }
}

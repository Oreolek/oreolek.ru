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
      'code' => '<iframe frameborder="0" allowtransparency="true" scrolling="no" src="https://money.yandex.ru/embed/small.xml?account=410011560528046&quickpay=small&any-card-payment-type=on&button-text=04&button-size=s&button-color=orange&targets=oreolek.ru+%D0%BD%D0%B0+%D0%BD%D0%BE%D0%B2%D1%8B%D0%B5+%D1%81%D1%82%D0%B0%D1%82%D1%8C%D0%B8+%D0%BF%D1%80%D0%BE...&default-sum=100&successURL=" width="158" height="31"></iframe><form class="alignleft" action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="hosted_button_id" value="M5RYZF8BK7R8W">
<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_LG.gif" border="0" name="submit" alt="PayPal">
</form><script data-gratipay-username="Oreolek" data-gratipay-widget="button" src="https://gttp.co/v1.js"></script><script src="/uploads/2014/09/coin.min.js"></script><script>CoinWidgetCom.go({wallet_address: "1GR5BqTJicETf9H6QnM1VsYwRw2cFcWiCb", currency: "bitcoin", counter: "amount", alignment: "bl", qrcode: false, auto_show: false, lbl_button: "Пожертвовать", lbl_address: "Мой адрес Bitcoin", lbl_count: "пожертвований", lbl_amount: "BTC"});</script>',
    );
  }
}

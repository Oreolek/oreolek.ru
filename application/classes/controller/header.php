<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Header extends Controller_Template {
 public $template = 'header';
 public function action_standard() {
  $this->template->title = $this->request->post('title');
  $stylesheets = array(
    APPPATH.'assets/stylesheets/base',
    APPPATH.'assets/stylesheets/colors',
    APPPATH.'assets/stylesheets/layout',
  );
  $this->template->stylesheet = Less::compile($stylesheets);
  $scripts = $this->request->post('scripts');
  $temp = "";
  if (is_array($scripts)) foreach($scripts as $script):
   $temp .= '<script type="text/javascript" charset="utf-8" src="'.URL::site('assets/javascript/'.$script).'"></script>'."\n";
  endforeach;
  $this->template->scripts = $temp;
 }
 public function action_view(){$this->request->redirect('');}
}

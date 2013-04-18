<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Header extends Controller_Template {
 public $template = 'header';
 public function action_standard() {
  $this->template->title = $this->request->post('title');
  $this->template->stylesheet = Less::compile(APPPATH.'assets/stylesheets/main');
  $scripts = array(
    'hyphenator.min.js'
  );
  $scripts_param = $this->request->post('scripts');
  if (is_array($scripts_param)) $scripts = array_merge ($scripts, $scripts_param);
  unset ($scripts_param);
  $temp = "";
  foreach($scripts as $script):
   $temp .= '<script type="text/javascript" charset="utf-8" src="'.URL::site('application/assets/javascript/'.$script).'"></script>'."\n";
  endforeach;
  $this->template->scripts = $temp;
  unset ($temp);
 }
 public function action_view(){$this->request->redirect('');}
}

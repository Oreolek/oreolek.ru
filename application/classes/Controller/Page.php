<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Page extends Controller_Template
{
  public $template = 'page';
  public function action_view($id)
  {
    $page = new Model_Page($id);
    $this->template->title = $page->id;
    $this->template->content = $page->content;
    if ($page->is_markdown) $this->template->content = Markdown::instance()->transform($this->template->content);
  }
}

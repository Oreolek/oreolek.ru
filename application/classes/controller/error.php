<?php defined('SYSPATH') or die('No direct script access.'); 
 
class Controller_Error extends Controller_Template { 
 public $template = 'error';
 
 /**
  * Pre determine error display logic
  */ 
 public function before() { 
  parent::before(); 
 
  // Sub requests only! 
  if ($this->request->is_initial()) $this->request->action(404);  
  $this->response->status((int) $this->request->action()); 
 } 
 
 /**
  * Serves HTTP 404 error page
  */
//адрес страницы не выводится
 public function action_404() {
  $this->template->title = 'Страница не найдена';
  $this->template->description = 'Запрошенная вами страница не найдена. Скорее всего, это была просто опечатка. Проверьте строку адреса.';
 } 
 
 /**
  * Serves HTTP 500 error page
  */ 
 public function action_500() {  
  $this->template->description = 'Произошла внутренняя ошибка. Не волнуйтесь, её должны скоро исправить.';
  $this->template->title ='Внутренняя ошибка сервера';
 }
} 
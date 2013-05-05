<?php defined('SYSPATH') or die('No direct script access.'); 

class Controller_Error extends Controller_Layout { 
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
  public function action_404() {
    $title = 'Страница не найдена';
    $this->template->header = Request::factory('header/standard')->post('title',$title)->execute();
    $this->template->description = 'Запрошенная вами страница не найдена. Скорее всего, это была просто опечатка. Проверьте строку адреса.';
  }

  /**
   * Serves HTTP 403 Access Denied page
   **/
  public function action_403() 
  {
    $title = 'Доступ запрещён';
    $this->template->header = Request::factory('header/standard')->post('title',$title)->execute();
    $this->template->description = 'Вам запрещён доступ к этому адресу.';
  }

  /**
   * Serves HTTP 500 error page
   */ 
  public function action_500() {  
    $this->template->description = 'Произошла внутренняя ошибка. Не волнуйтесь, её должны скоро исправить.';
    $title ='Внутренняя ошибка сервера';
    $this->template->header = Request::factory('header/standard')->post('title',$title)->execute();
  }
} 

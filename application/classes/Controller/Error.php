<?php defined('SYSPATH') or die('No direct script access.'); 

class Controller_Error extends Controller_Layout { 
  /**
   * Pre determine error display logic
   */ 
  public function before() { 
    parent::before(); 
    $this->template = new View_Message;

    // Sub requests only! 
    if ($this->request->is_initial()) $this->request->action(404);  
    $this->response->status((int) $this->request->action()); 
  } 

  /**
   * Serves HTTP 404 error page
   */
  public function action_404() {
    $this->template->title = 'Страница не найдена';
    $this->template->message = 'Запрошенная вами страница не найдена. Скорее всего, это была просто опечатка. Проверьте строку адреса.';
  }

  /**
   * Serves HTTP 403 Access Denied page
   **/
  public function action_403() 
  {
    $this->template->title = 'Доступ запрещён';
    $this->template->message = 'Вам запрещён доступ к этому адресу.';
  }

  /**
   * Serves HTTP 500 error page
   */ 
  public function action_500() {  
    $this->template->message = 'Произошла внутренняя ошибка. Не волнуйтесь, её должны скоро исправить.';
    $this->template->title ='Внутренняя ошибка сервера';
  }
} 

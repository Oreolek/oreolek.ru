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
 * Error handling controller.
 **/
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

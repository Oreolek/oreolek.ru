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
 * Page creation/editing view controller
 **/
class View_Page_Edit extends View_Edit {
  public function input_name()
  {
    return Form::orm_input($this->model, 'name');
  }

  public function input_is_draft()
  {
    return Form::orm_input($this->model, 'is_draft', 'checkbox');
  }

  public function input_content()
  {
    return Form::orm_input($this->model, 'content', 'textarea');
  }
}

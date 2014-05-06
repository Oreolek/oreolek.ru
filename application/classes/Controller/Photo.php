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
 * Photo management. This controller is an early draft of what should be an upload section.
 * It isn't even turned on yet.
 **/
class Controller_Photo extends Controller_Layout {
  protected $secure_actions = array(
    'edit' => array('login','admin'),
  );

  /**
   * View photo by ID
   **/
  public function action_view()
  {
    $id = $this->request->param('id');
    $photo = ORM::factory('Photo', $id);
    if (!$photo->loaded())
    {
      $this->redirect('error/404');
    }
    $view = new View_Message;
    $this->template->title = $photo->name;
    $this->template->message = HTML::image($photo->get_image_path());
  }

  /**
   * Upload a new photo
   **/
  public function action_edit()
  {
    $this->template = new View_Photo_Edit;
    $id = $this->request->param('id');
    $photo = ORM::factory('Photo', $id);
    $this->template->title = 'Загрузка фотографии';
    $this->template->errors = array();
    $this->template->controls = array(
      'name' => 'input',
    );
    if ($photo->loaded())
    {
      $this->template->image_path = $photo->image_path;
    }
    $this->template->custom_controls = array(
      'file' => array(
        'type' => 'file',
        'label' => 'Файл изображения'
      )
    );
    if (HTTP_Request::POST == $this->request->method()) {
      $validation_post = Validation::factory($this->request->post())
        ->rules('name', array(
          array('not_empty'),
        ));
      $validation_files = Validation::factory( $_FILES )
        ->rule( 'file', array( 'Upload', 'not_empty' ) )
        ->rule( 'file', array( 'Upload', 'valid' ) )
        ->rule( 'file', 'Upload::type', array(':value', array('jpg', 'png', 'gif')));
      $file = Arr::get($_FILES, 'file');
      if ($validation_post->check() AND $validation_files->check() AND isset($file))
      {
        $photo->filename = Arr::get($file, 'name');
        $filename = $photo->file_save($file);
        if ( $filename === false ) {
	      	throw new Exception( 'Unable to save uploaded file.' );
      	}
        $photo->name = $this->request->post('name');
        try {
          if ($photo->check())
          {
            $photo->create();
          }
        }
        catch (ORM_Validation_Exception $e)
        {
          $this->template->errors = $e->errors();
        }
      }
      else
      {
        $this->template->errors = Arr::merge( $validation_post->errors(), $validation_files->errors() );
      }
      if (empty($this->template->errors))
      {
        $this->redirect('photo/view/' . $photo->id);
      }
    }
    $this->template->model = $photo;
  }

  public function action_create()
  {
    $this->redirect('photo/edit');
  }
  
  public function action_upload()
  {
    $this->redirect('photo/edit');
  }

  /**
   * Index all photos in the database.
   **/
  public function action_index()
  {
  }
}

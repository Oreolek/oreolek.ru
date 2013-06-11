<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Photo management
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

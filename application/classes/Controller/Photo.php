<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Photo management
 **/
class Controller_Photo extends Controller_Layout {
  public $template = 'photo/view';
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
    if (!$photo->loaded()) $this->redirect('error/404');
    $title = $photo->name;
    $this->template->header = Request::factory('header/standard')->post('title',$title)->execute();
    $this->template->image_path = $photo->get_image_path();
    $this->template->thumbnail_path = $photo->get_thumbnail_path();
  }

  /**
   * Upload a new photo
   **/
  public function action_edit()
  {
    $this->template = new View('photo/edit');
    $id = $this->request->param('id');
    $photo = ORM::factory('Photo', $id);
    $title = 'Загрузка фотографии';
    $this->template->header = Request::factory('header/standard')->post('title',$title)->execute();
    $this->template->footer = Request::factory('footer/standard')->execute();
    $this->template->errors = array();
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
          if ($photo->check()) $photo->create();
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
      if (empty($this->template->errors)) $this->redirect('photo/view/' . $photo->id);
    }
    $this->template->photo = $photo;
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

<?php defined('SYSPATH') OR die('No direct access allowed.');
class Model_User extends Model_Auth_User {
 protected $_rules = array(
  'username' => array(
   'not_empty' => NULL,
   'min_length' => array(4),
   'max_length' => array(32),
   'regex' => array('/^[-\pL\pN_.]++$/uD')
  ),
  'password' => array(
   'not_empty' => NULL,
   'min_length' => array(5),
   'max_length' => array(42)
  ),
  'email' => array(
   'not_empty' => NULL,
   'min_length' => array(5),
   'max_length' => array(127),
   'validate::email' => NULL
  )
 );

 protected $_callbacks = array(
  'username' => array('username_available'),
  'email' => array('email_available')
 );

 public function validate_create(&$array){
  $array = Validate::factory($array)
   ->filter(TRUE, 'trim')
   ->rules('username', $this->_rules['username'])
   ->rules('password', $this->_rules['password'])
   ->rules('email', $this->_rules['email']);

  foreach ($this->_callbacks as $field => $callbacks){
   foreach ($callbacks as $callback){
    $array->callback($field, array($this, $callback));
   }
  }
  return $array;
 }
}

<?php defined('SYSPATH') OR die('No direct script access.');
return array(
  'content' => array(
    'not_empty' => 'You should write a comment first if you want to click a big comment posting button.',
    'min_length' => 'You should write something more here.',
  ),
  'author_name' => array(
    'not_empty' => 'You did not leave a name. How would you like to be addressed?',
  ),
  'author_email' => array(
    'not_empty' => 'You should leave an email address. Please.',
    'email' => 'This does not look like an email address. Surely a small mistake.',
    'max_length' => 'This does not look like an email address. Surely a small mistake.'
  )
);

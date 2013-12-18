<?php defined('SYSPATH') or die('No direct script access.');
 /**
  * Custom error pages fix
  **/
class HTTP_Exception extends Kohana_HTTP_Exception {
    /**
     * Generate a Response for all Exceptions without a more specific override
     * 
     * The user should see a nice error page, however, if we are in development
     * mode we should show the normal Kohana error page.
     * 
     * @return Response
     */
    public function get_response()
    {
        // Lets log the Exception, Just in case it's important!
        Kohana_Exception::log($this);
 
        if (Kohana::$environment >= Kohana::DEVELOPMENT)
        {
            // Show the normal Kohana error page.
            return parent::get_response();
        }
        else
        {
          // Generate a nicer looking "Oops" page using error sub request 
          $attributes = array( 
            'action' => $this->getCode(),
            'message' => rawurlencode($this->getMessage())
          ); 

          return Request::factory(Route::get('error')->uri($attributes)) 
            ->execute() 
            ->body();
        }
    }
}

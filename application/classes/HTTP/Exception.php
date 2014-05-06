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
 
        if (FALSE && Kohana::$environment >= Kohana::DEVELOPMENT)
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
          
          $body = Request::factory(Route::get('error')->uri($attributes)) 
            ->execute() 
            ->body();

          $response = Response::factory()
                ->status($this->getCode())
                ->body($body);
 
          return $response;
        }
    }
}

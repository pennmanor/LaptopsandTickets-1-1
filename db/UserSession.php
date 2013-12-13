<?php
/*
  Copyright 2013 Penn Manor School District, Andrew Lobos, and Benjamin Thomas

   Licensed under the Apache License, Version 2.0 (the "License");
   you may not use this file except in compliance with the License.
   You may obtain a copy of the License at

       http://www.apache.org/licenses/LICENSE-2.0

   Unless required by applicable law or agreed to in writing, software
   distributed under the License is distributed on an "AS IS" BASIS,
   WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
   See the License for the specific language governing permissions and
   limitations under the License.
*/

/**
 * Class for accessing a user's session data
 * @author Andrew
 */
class UserSession
{
	/**
	 * Create a UserSession object
	 * This must be called in a place where headers may be sent, as it calls session_start()
	 */
	public function __construct()
	{
		session_start();
		
		$this->id = $this->initSessionVariable('USER_ID');
		$this->name = $this->initSessionVariable('USER_NAME');
		$this->authenticated = $this->initSessionVariable('USER_IS_LOGGEDIN');
	}

	/**
	 * Returns $_SESSION[$name] if it exists. If it does not exist, it is initalized to false, then returned
	 * @return $_SESSION[$name]
	 */
	public function initSessionVariable($name)
	{
		if ( array_key_exists($name, $_SESSION) )
			return $_SESSION[$name];
		else
		{
			$_SESSION[$name] = false;
			return false;
		}
	}
	
	/**
	 * Get the student ID of the currently logged in user
	 * @return The user's student ID
	 */
	public function getID()
	{
		return $this->id;
	}
	
	/**
	 * Get the student name of the currently logged in user
	 * @return The user's name
	 */
	public function getName()
	{
		return $this->name;
	}
	
	/**
	 * Check if this session is logged in
	 * @return True if logged in, false otherwise
	 */
	public function isAuthenticated()
	{
		return $this->authenticated;
	}
	
	/**
	 * Check if this session is a helper
	 * @return True if this user is a helper, false otherwise
	 */
	public function isHelper()
	{
		global $helpers;
		foreach ( $helpers as $helper )
		{
			if ( $helper == $this->id )
				return true;
		}
		return false;
	}
	
	/**
	 * Set this session as the provided student ID is logged in
	 * This function DOES NOT check passwords. This should be called AFTER the user is authenticated with their password.
	 * @param $id The ID to login
	 */
	public function login($id)
	{
		$this->id = $id;
		$student = $this->getAsStudent();
		if ( !$student )
			return false;
		$this->name = $student->getName();
		if ( !$this->name )
			return false;
	
		$this->authenticated = true;
		
		$_SESSION['USER_ID'] = $this->id;
		$_SESSION['USER_NAME'] = $this->name;
		$_SESSION['USER_IS_LOGGEDIN'] = $this->authenticated;
		return true;
	}
	
	/**
	 * Destroy this user's session
	 */
	public function logout()
	{
		$this->id = false;
		$this->name = false;
		$this->authenticated = false;
		
		$_SESSION['USER_ID'] = false;
		$_SESSION['USER_NAME'] = false;
		$_SESSION['USER_IS_LOGGEDIN'] = false;
		session_destroy();
	}
	
	/**
	 * Get the Student object for the current user
	 * @return Student object for the current user, false if no user is logged in
	 */
	public function getAsStudent()
	{
		if ( !$this->id )
			return false;
		return new Student($this->id);
	}
}
?>
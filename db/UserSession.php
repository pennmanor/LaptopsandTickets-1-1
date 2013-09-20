<?php
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
	 * @returns $_SESSION[$name]
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
	 * @returns The user's student ID
	 */
	public function getID()
	{
		return $this->id;
	}
	
	/**
	 * Get the student name of the currently logged in user
	 * @returns The user's name
	 */
	public function getName()
	{
		return $this->name;
	}
	
	/**
	 * Check if this session is logged in
	 * @returns True if logged in, false otherwise
	 */
	public function isAuthenticated()
	{
		return $this->authenticated;
	}
	
	/**
	 * Check if this session is a helper
	 * @returns True if this user is a helper, false otherwise
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
	 * @returns Student object for the current user, false if no user is logged in
	 */
	public function getAsStudent()
	{
		if ( !$this->id )
			return false;
		return new Student($this->id);
	}
}
?>
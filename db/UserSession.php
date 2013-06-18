<?php
class UserSession
{
	public function __construct()
	{
		session_start();
		
		$this->id = $_SESSION['USER_ID'];
		$this->name = $_SESSION['USER_NAME'];
		$this->authenticated = $_SESSION['USER_IS_LOGGEDIN'];
	}
	
	public function getID()
	{
		return $this->id;
	}
	
	public function getName()
	{
		return $this->name;
	}
	
	public function isAuthenticated()
	{
		return $this->authenticated;
	}
	
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
	
	public function getAsStudent()
	{
		if ( !$this->id )
			return false;
		return new Student($this->id);
	}
}
?>
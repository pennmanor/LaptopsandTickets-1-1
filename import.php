<?php
// Expected format: id, first, last, grade
// No column headers


if ( array_key_exists("REMOTE_ADDR", $_SERVER) )
	die("This script can not be run from the web.\n");

require_once("config.php");
require_once("db/include.php");

$data = "";

while ( $d = fgets(STDIN) )
	$data .= $d;

// Get user objects
$DBStudents = Student::getAll();

// Copy usernames from file DB
$FileStudents = array();
$lines = explode("\n", $data);

foreach ($lines as $line )
{
	$row = explode(",", trim($line));
	if ( count($row) == 4 )
		$FileStudents[] = $row;
}

function existsInFile($findStudent)
{
	global $FileStudents;
	foreach ($FileStudents as $student)
	{
		if ($findStudent == $student[0])
			return true;
	}
	return false;
}

function existsInDB($findStudent)
{
	global $DBStudents;
	foreach ($DBStudents as $student)
	{
		if ( $findStudent == $student->id )
			return true;
	}
	return false;
}


// Remove users that are in the database, but not in the file.
foreach ($DBStudents as $student)
{
	if ( !existsInFile($student->id) )
	{
		if ( $student->getLaptop() )
		{
			echo $student->id." should be removed, but still has a laptop assigned!\n";
		}
		else
		{
			echo "Removing student ".$student->id."\n";
			if ( !Student::nuke($student->id) )
				echo "Student removal failed!\n";
		}
	}
}

// Add users that are in the file, but not the database
foreach ($FileStudents as $student)
{
	if ( !existsInDB($student[0]) )
	{
		echo "Adding student ".$student[0]." ".$student[1]." ".$student[2]." in grade ".$student[3]."\n";
		if ( !Student::create($student[0], $student[1]." ".$student[2], $student[3]) )
			echo "Student create failed!\n";
	}
}


?>
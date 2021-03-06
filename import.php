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
// Expected format: id, first, last, grade
// No column headers


if ( array_key_exists("REMOTE_ADDR", $_SERVER) )
	die("This script can not be run from the web.\n");

require_once("config.php");
require_once("db/include.php");

function setStudentArrayKeys($a)
{
	$a[PROPERTY_SID] = $a[0];
	$a[PROPERTY_NAME] = $a[1]." ".$a[2];
	$a[PROPERTY_GRADE] = $a[3];
	return $a;
}

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
		$FileStudents[] = setStudentArrayKeys($row);
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
		if ( $findStudent == $student->getID() )
			return true;
	}
	return false;
}


// Remove users that are in the database, but not in the file.
foreach ($DBStudents as $student)
{
	if ( !existsInFile($student->getID()) )
	{
		if ( $student->getLaptop() )
		{
			echo $student->getID()." should be removed, but still has a laptop assigned!\n";
		}
		else
		{
			echo "Removing student ".$student->getID()."\n";
			if ( !Student::nuke($student->getID()) )
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
	else
	{
		$thisStudent = new Student($student[0]);
		foreach ( $student as $key => $fileValue )
		{
			if ( !is_numeric($key) )
			{
				$dbValue = $thisStudent->getProperty($key);
				if ( $fileValue != $dbValue )
				{
					echo "Updating ".$key." on ".$student[0]." to ".$fileValue."\n";
					$thisStudent->setProperty($key, $fileValue);
				}
			}
		}
	}
}


?>

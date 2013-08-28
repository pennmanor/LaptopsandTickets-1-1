# _Laptop Inventory and Ticket System_
_Integrated inventory and ticket system for 1:1 programs_

## Dependencies
* A modern version of PHP
* MySQL
* A web server with PHP configured

## Install

### Introduction
* Copy this directory where you want it located on your site
* Import structure.sql into a blank SQL database

### Configuration
Edit config.php.example. Take note of the following options:

* $buildingList is an array of building ID(key/index) mapped to building name(value)
* $issueTypes is an array of the issue types possible when a tech/helper logs service on a machine
* $helpers is the array of accounts to be treated as admin/"helper"
* $databaseInfo is an array containing the connection info for MySQL
* $itemsPerPage defines how many items per page will be displayed in paginated views
* OpenID options will be covered in the OpenID Configuration section

Remember to save the updated file as **config.php**

### Authentication/OpenID Configuration

* openIDproviderURL is the URL for the OpenID provider. For Google Apps OpenID, use http://google.com/accounts/o8/site-xrds?hd=YOUR.APPSDOMAIN.HERE
* openIDbaseURL is the URL for this application with a trailing slash. This is used to redirect users after authentication.
* openIDlogoutURL is the URL to redirect users to when the logout button is pressed. For Google, use https://www.google.com/accounts/Logout
* If you are using Google apps, you'll need to add a file in your web server's root at your apps domain. Here is a template for this file:

	<?xml version="1.0" encoding="UTF-8"?>
	<xrds:XRDS xmlns:xrds="xri://$xrds" xmlns="xri://$xrd*($v*2.0)">
		<XRD>
			<Service priority="0">
				<Type>http://specs.openid.net/auth/2.0/signon</Type>
				<URI>https://www.google.com/a/YOUR.APPSDOMAIN.HERE/o8/ud?be=o8</URI>
			</Service>
			<Service priority="0">
				<Type>http://specs.openid.net/auth/2.0/server</Type>
				<URI>https://www.google.com/a/YOUR.APPSDOMAIN.HERE/o8/ud?be=o8</URI>
			</Service>
		</XRD>
	</xrds:XRDS>


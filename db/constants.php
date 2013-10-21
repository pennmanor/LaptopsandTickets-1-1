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
define("ACTION_CREATE", 1);
define("ACTION_ASSIGN", 2);
define("ACTION_UNASSIGN", 3);

// Genaric properties
define("PROPERTY_ID", "id");
define("PROPERTY_SID", "sid");
define("PROPERTY_TIMESTAMP", "timestamp");
define("PROPERTY_BUILDING", "building"); // Laptops and Students

// Laptop properties
define("PROPERTY_HOSTNAME", "hostname");
define("PROPERTY_SERIAL", "serial");
define("PROPERTY_ASSETTAG", "assetTag");
define("PROPERTY_WMAC", "wirelessMAC");
define("PROPERTY_EMAC", "ethernetMAC");

define("PROPERTY_NOTES", "notes");

// Student properties
define("PROPERTY_NAME", "name");
define("PROPERTY_GRADE", "grade");
define("PROPERTY_LAPTOP", "laptop");

// Ticket properties
define("PROPERTY_TITLE", "title");
define("PROPERTY_BODY", "body");
define("PROPERTY_STUDENT", "student");
define("PROPERTY_HELPER", "helper");
define("PROPERTY_STATE", "state");

// Feedback properties
define("PROPERTY_LIKE", "like");
define("PROPERTY_DISLIKE", "dislike");

// Error Types
define("RESULT_NONE", 0);
define("RESULT_FAIL", 1);
define("RESULT_SUCCESS", 2);
define("RESULT_DUP", 3);

define("HISTORYEVENT_CREATION", ACTION_CREATE);
define("HISTORYEVENT_ASSIGNMENT", ACTION_ASSIGN);
define("HISTORYEVENT_UNASSIGN", ACTION_UNASSIGN);
define("HISTORYEVENT_SERVICE", 4);

define("HISTORYEVENT_TICKET_INFO", 6);
define("HISTORYEVENT_TICKET_REPLY", 7);
define("HISTORYEVENT_TICKET_STATECHANGE", 9);
define("HISTORYEVENT_SIGNIN", 10);
define("HISTORYEVENT_SIGNOUT", 11);

define("TICKETSTATE_CLOSED", 0);
define("TICKETSTATE_OPEN", 1);

define("API_SUCCESS", "success");
define("API_STATUS", "status");
define("API_INFO", "info");

define("API_DATA_ID", "id");
define("API_DATA_NAME", "name");
?>
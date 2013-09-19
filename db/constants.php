<?php
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
define("API_RESULT", "result");

define("API_DATA_ID", "id");
define("API_DATA_NAME", "name");
define("API_DATA_STUDENT", "student");
define("API_DATA_ACTION", "action");
define("API_DATA_BY", "by");
define("API_DATA_FOR", "for");

define("API_LIMIT", "limit");
define("API_LIMIT_MY", "my");
define("API_LIMIT_OPEN", "open");
define("API_LIMIT_CLOSED", "closed");
define("API_LIMIT_HELPER", "helper");
define("API_LIMIT_ASSIGNED", "assigned");
define("API_LIMIT_UNASSIGNED", "unassigned");

define("API_ACTION_ALL", "all");
define("API_ACTION_SEARCH", "search");
define("API_ACTION_GET", "get");
?>
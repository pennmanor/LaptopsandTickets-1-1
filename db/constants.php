<?php
define("ACTION_CREATE", 1);
define("ACTION_ASSIGN", 2);
define("ACTION_UNASSIGN", 3);

define("PROPERTY_ID", "id");
define("PROPERTY_HOSTNAME", "hostname");
define("PROPERTY_SERIAL", "serial");
define("PROPERTY_ASSETTAG", "assetTag");
define("PROPERTY_WMAC", "wirelessMAC");
define("PROPERTY_EMAC", "ethernetMAC");
define("PROPERTY_BUILDING", "building");
define("PROPERTY_NOTES", "notes");

define("PROPERTY_NAME", "name");

define("RESULT_NONE", 0);
define("RESULT_FAIL", 1);
define("RESULT_SUCCESS", 2);
define("RESULT_DUP", 3);

define("HISTORYEVENT_CREATION", ACTION_CREATE);
define("HISTORYEVENT_ASSIGNMENT", ACTION_ASSIGN);
define("HISTORYEVENT_UNASSIGN", ACTION_UNASSIGN);
define("HISTORYEVENT_SERVICE", 4);
?>
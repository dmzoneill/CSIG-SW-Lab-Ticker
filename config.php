<?php

/*
 * A readme file, README.html, is provided alongside this config file which explains 
 * in full the list of options available for each of these configuration controllers
 */

error_reporting( E_ALL & ~E_STRICT );

require_once( "ky/kyIncludes.php" );
include_once( "view.class.php" );
include_once( "ticker.class.php" );

$template = isset( $_GET['template'] ) ? $_GET['template']  : "basic"; //false return is the default display format

define( "__API_URL" , "https://snnlab.ir.intel.com/api/index.php?" );
define( "__API_REST_KEY" , "1863491e-1afd-bfa4-d98a-8fe5d60307e6" );
define( "__API_SECRET_KEY" , "MWM3N2Y4YzUtNWQwOS04ZmQ0LTUxZmYtNDI2OGZmZTJiZjUxM2QxNjQ5Y2ItMTZmNi1kZjY0LTA1YWUtZTU4MmVkNGZmYWQz" );
define( "__SITE_ROOT" , dirname(__FILE__) );
define( "__VIEWFOLDER" , __SITE_ROOT . "/views" );
define( "__VIEW" , __VIEWFOLDER . "/" . $template );
define( "__TICKET_TYPE" , "Issue" ); 				//Issue|Task|Bug|Lead|Feedback
define( "__TICKET_STATUS" , "In Progress|Open" );	//Open|In Progress|On Hold|Closed
define( "__REFRESH_TIME" , 15);						//time in seconds to poll for updates
define( "__MAX_TICKETS" , 40);						//number of tickets to display on screen. If -1, all tickets will be shown.
define( "__SORT_METHOD" , "CREATION_DATE"); 		//"CREATION_DATE" or "ID"
define( "__SORT_DIRECTION" , "ASC"); 				//"ASC" or "DESC"
define( "__DEPARTMENT_EXCLUSIONS", "WGC|Hardware [WGC]|Software [WGC]|Networking [WGC]|Shipping [WGC]|Silicon / Processors [WGC]");
//see README.html for full list of departments

kyConfig::set( new kyConfig( __API_URL , __API_REST_KEY , __API_SECRET_KEY ) );




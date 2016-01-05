<?php
/*
****************************************************************
#mode.php File
#Version 1.4
****************************************************************
#Copy Righted 2006-2007(http://nevuxbulletin.com)
#Created By NevuxAB Developement Team
****************************************************************
*/


define("NABPROOF",true);


/*====================================================
@Include all main functions to run NAB
=====================================================*/
	include("./config.php");
	include("./functions/globals.php");
	
/*
||Show HEADER||
*/
	include_once("./sources/header.php");
	include("./navigation.php");
	include("./modes.php");
	moderator();
	include("footer.php");

?>
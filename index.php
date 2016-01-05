<?php
/*================================================================
||****************************************************************||
||#index.php File                                                                                                     ||
||#Version 1.3.1.2										   ||
||****************************************************************||
||#Copy Righted 2006-2007(http://nevuxbulletin.com)||
||#Created By NevuxBB Developement Team||
||****************************************************************||
=================================================================*/

define("NABPROOF",true);

	if(file_exists("./installer.php"))
	{
		echo "Your forum is insecure, please remove installation files.";
		exit(0);
	}
/*====================================================
@Include all main functions to run NAB
=====================================================*/
	include("./config.php");
	include("./functions/globals.php");
/*
||Show HEADER||
*/
	include_once("./sources/header.php");
/*
||RUN OFFLINE/BANNED CHECKS||
*/
	if(check_if_banned_ip() === true || check_if_banned_user() === true)
	{
		banned();
	}
	offline();
/*
||RUN INDEX||
*/
	include("./navigation.php");
	update_users();
	require("classes/run_nab.php");
/*
||RUN Footers & Board Wrappers||
*/

include("./footer.php");
?>
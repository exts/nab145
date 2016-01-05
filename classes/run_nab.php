<?php
/*
****************************************************************
#classes/run_nab.php File
#Version 1.4
****************************************************************
#Copy Righted 2006-2007(http://nevuxbulletin.com) [nevuxab.info]
#Created By NevuxAB Developement Team
****************************************************************
*/
	if( !defined("NABPROOF") )
	{
		die("Hacking attempt.");
	}
	
//include board functions

	include("./sources/boardindex.php");
	include("./sources/forums.php");
	include("./sources/topics.php");
	include("./sources/post.php");
	include("./sources/logreg.php");
	include("./sources/cpanel.php");
	include("./sources/profile.php");
	include("./sources/search.php");
	
	$pxt = "act";//page index.php?loc=main or just index.php
	switch($_GET[$pxt])
	{
		case "find":
			research();
		break;
		case "profile":
			profile();
			break;
		case "members":
			pageerror("Coming soon..","Feature coming soon..","This feature will be added in the next update.");
			break;
		case "viewforum":
			RUN_Forums();
			break;
		case "topicshow":
			RUN_Topic();
			break;
		case "newtopic":
			RUN_Newtopic();
			break;
		case "newreply":
			RUN_Newreply();
			break;
		case "login":
			login();
			break;
		case "logout":
			do_logout();
			break;
		case "register":
			register();
			break;
		case "lostpass":
			lostpass();
			break;
		case "passrecovery":
			lostpass2();
			break;
		case "Cpanel":
			cpanel();
			break;
		default:
			RUN_Index();
			RUN_Wrappers();
			break;
	}
?>	
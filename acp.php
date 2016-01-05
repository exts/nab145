<?php
/*
****************************************************************
#acp.php File
#Version 1.4
****************************************************************
#Copy Righted 2006-2007(http://nevuxbulletin.com) [nevuxab.info]
#Created By NevuxAB Developement Team
****************************************************************
*/
	include("./config.php");
	include("sources/admin/login.php");
	include("sources/admin/temp.php");
	include("sources/admin/ACP.php");
	theader();//show the header
	if( is_admin() )
	{
		left_nav();
		$content = acp_run();
		right_body($content,"");
	}
	else
	{
		login();
	}
	footer();//show the footer
	
?>
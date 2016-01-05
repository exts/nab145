<?php
/*
****************************************************************
#sources/header.php File
#Version 1.4
****************************************************************
#Copy Righted 2006-2007(http://nevuxbulletin.com) [nevuxab.info]
#Created By NevuxAB Developement Team
****************************************************************
*/
		if(!defined("NABPROOF"))
		{
			die("Hacking attempt");
		}
			$bname = mysql_query("SELECT `boardname` FROM `boardstatus` WHERE `id` = '1' ") or die(mysql_error());
			$boardname = mysql_fetch_array($bname);
			$Temp = new Template;
			$Temp->dir = $logged['dskin'];
			$Temp->file = "header.tpl";
			$Temp->tp(__LINE__,__FILE__);
			$Temp->tr(array(
			'TITLE' => '<title>'.$boardname['boardname'].' - (Powered By Nevux Ability Boards)</title> ',
			'SKIN' => $logged['dskin'],
			'PERMISSIONS' => $NAV
			));
			echo $Temp->html;
?>


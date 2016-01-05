<?php
/*
****************************************************************
#install_temp.php File
#Version 1.4
****************************************************************
#Copy Righted 2006-2007(http://nevuxbulletin.com) [nevuxab.info]
#Created By NevuxAB Developement Team
****************************************************************
*/
	function theader()
	{
		$Temp = new Template;
		$Temp->dir = 'pro';
		$Temp->file = "header.tpl";
		$Temp->tp(__LINE__,__FILE__);
		$Temp->tr(array(
		'TITLE' => '<title>Installation Template - Nevux Ability Boards</title>',
		'SKIN' => 'pro',
		'PERMISSIONS' => ''
		));
		echo $Temp->html;
	}
	function tfooter()
	{
		$Temp = new Template;
		$Temp->dir = 'pro';
		$Temp->file = "footer.tpl";
		$Temp->tp();
		echo $Temp->html;
	}
?>
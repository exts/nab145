<?php
/*
****************************************************************
#classes/profile.php File
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

function profile()
{	
	global $logged;
	$id = intval( htmlspecialchars($_GET['profile;id']) );
	
	$sql = mysql_query("SELECT * FROM `users` WHERE `id` = '" . $id . "' LIMIT 1;");
	if( mysql_num_rows($sql) <= 0)
	{
		pageerror("Profile Error","Doesn't exists.","Sorry, but it looks like this user wasn't created or was deleted.");
	}
	$information = mysql_fetch_array($sql);
	$Temp = new Template;
	$Temp->dir = $logged['dskin'];
	$Temp->file = "profile.tpl";
	$Temp->tp();
	$Temp->tr(array(
		'USERNAME' => $information['username'],
		'POSTS' => $information['post'],
		'GROUP' => getgroupname($information['level']),
		'JOINED' => (empty($information['joined'])) ? 'unknown' : date("m-d-y",$information['joined']),
		'AVY' => '',
		'MSN' => (empty($information['msn'])) ? 'unknown' : $information['msn'],
		'GTALK' => 'unknown',
		'EMAIL' => 'unknown'
	));
	echo $Temp->html;
}
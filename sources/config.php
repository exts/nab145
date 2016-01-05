<?php
/*
****************************************************************
#config.php
#Version 1.4
****************************************************************
#Copy Righted 2006-2007(http://nevuxbulletin.com) [nevuxab.info]
#Created By NevuxAB Developement Team
****************************************************************
*/
ob_start();
session_start();

$_CONFIG = array();

$_CONFIG['HOST'] = "localhost";

$_CONFIG['SQL_USER'] = "nab_nab";

$_CONFIG['SQL_PASS'] = "dang3r84";

$_CONFIG['SQL_DB']	= "nab_nab";

$_CONFIG['VERSION'] = "1.4.3";

$_CONFIG['STAGE'] = 2;

if ( !@mysql_connect($_CONFIG['HOST'],$_CONFIG['SQL_USER'],$_CONFIG['SQL_PASS']) )
{
		die("Sorry, we couldn't connect to the database, please check your configuration. [0]");
}

if( !@mysql_select_db($_CONFIG['SQL_DB']) )
{
		die("Sorry, we couldn't connect to the database, please check your configuration. [1]");
}

if( isset( $_SESSION['uid'] ) )
{
		$session_id = htmlspecialchars( $_SESSION['uid'] );
		$session_pass = htmlspecialchars( $_SESSION['upass'] );
		$log = mysql_query("SELECT * FROM `users` WHERE `id` = '".$session_id."' AND `password` = '".$session_pass."' ");
		$logged = mysql_fetch_array($log);
		$permissions_ = mysql_query("SELECT * FROM `groups` WHERE `id` = '".$logged['level']."'");
		$permissions = mysql_fetch_array($permissions_);
}
elseif( !isset( $_SESSION['uid']) )
{
		$permissions = array();
		$logged = array();
		$logged['level'] = 0;
		$logged['dskin'] = "pro";
}

if(!file_exists("./installer.php"))
{
include("./functions/funcs_main.php");
}
include("./classes/template.php");
$NAV = ($logged['username']) ? 'Welcome back <strong>' .$logged['username']. '</strong> (<a href="index.php?act=logout">Logout</a>), <a href="index.php?act=Cpanel">Control Panel</a> | ' . (($permissions['admin'] == 't') ? '<a href="acp.php">Admin Panel</a>' : '') . "" : "Welcome back Guests, <a href='index.php?act=login'>Login</a> Or <a href='index.php?act=register'>Register</a>";
?>
<?php
/*
****************************************************************
#functions/globals.php File
#Version 1.3
****************************************************************
#Copy Righted 2006-2007(http://nevuxbulletin.com)
#Created By NevuxAB Developement Team
****************************************************************
*/
	if(!defined("NABPROOF"))
	{
		die("Hacking attempt");
	}
	function globals()
	{
		foreach($_GET as $key => $val)
		{
			if(	get_magic_quotes_gpc()	)
			{
				$val = stripslashes( $val );
			}
			else
			{
				$val = addslashes( $val );
			}
			return $_GET[$key] = htmlspecialchars( strip_tags( $val ) );
		}
	
		foreach($_POST as $key => $val)
		{
			if(	get_magic_quotes_gpc()	)
			{
				$val = stripslashes( $val );
			}
			else
			{
				$val = addslashes( $val );
			}
			return $_POST[$key] = htmlspecialchars( strip_tags( $val ) );
		}
	}
	globals();

?>
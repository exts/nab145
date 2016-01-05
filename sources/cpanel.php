<?php
/*
****************************************************************
#sources/cpanel.php File
#Version 1.4.3
****************************************************************
#Copy Righted 2006-2007(http://nevuxbulletin.com)
#Created By NevuxAB Developement Team
****************************************************************
*/


if(!defined("NABPROOF"))
{
	die("Hacking attempt");
}

//Cpanel main function to run each individual page
function cpanel()
{
	global $logged;
	
	if( !$logged['username'] )
	{
		die( no_permission() );
	}
	else
	{
		$Temp = new Template;
		$Temp->dir = $logged['dskin'];
		$Temp->file = "cpanel_body.tpl";
		$Temp->tp();
		$Temp->tr(array(
			'BODY' => CPanelPages($_GET['option'])
		));
		echo $Temp->html;
	}
}

//Switches between pages
function CPanelPages($page)
{
	//clean page
	$page = htmlspecialchars($page);
	//do different things on different pages
	switch($page)
	{
		case "email": //updates Email
			return panel_email();
			break;
		case "profile": //Updates Profile
			return panel_profile();
			break;
		case "password": //Changes Password
			return change_password();
			break;
		default:
			return cpanel_home();
			break;
	}
}

//changes password
function change_password()
{
	global $logged;
	if( !$_POST['changepass'] )
	{
		$Temp = new Template;
		$Temp->dir = $logged['dskin'];
		$Temp->file = "change_pass.tpl";
		$Temp->tp();
		return $Temp->html;
	}
	else
	{
		$pass = htmlspecialchars($_POST['pname']);
		$pass2 = htmlspecialchars($_POST['password']);
		$pass3 = htmlspecialchars($_POST['password2']);
		$current1 = mysql_query("SELECT `password` , `salt` FROM `users` WHERE `username` = '".$logged[username]."' ") or die(pageerror("Change Password Error","There was an error in changing password!","Error getting information from the database!"));
		$current = mysql_fetch_array($current1);
		$salt = substr(md5(uniqid(rand(), true)), 0, 5);
		$new_password = md5($salt.$pass2);
		if( ( md5( $current['salt'] . $pass ) ) != $current['password'] )
		{
			pageerror("Change Password Error","There was an error in changing password!","Your Current Password was incorrect!");
		}
		elseif( $pass2 != $pass3 )
		{
			pageerror("Change Password Error","There was an error in changing password!","Passwords didn't match!");
		}
		else
		{
			//update password
			$new_pass = mysql_query("UPDATE `users` SET `password` = '".$new_password."', salt = '".$salt."' WHERE `username` = '".$logged['username']."' ") or die(pageerror("Change Password Error","There was an error in changing password!","Something went wrong in the database please contact administrator!"));
			unset($_SESSION['uid']);
			unset($_SESSION['upass']);
			unset($_SESSION['nbb_apanel']);
			session_destroy();
			return imessage("Password Changed!","Password Changed Sucess!","Thank you,now your password is changed!","index.php",true);
		}
	}
}

//panel_home function
function cpanel_home()
{
	global $logged;
	$userNotes = mysql_query("SELECT `notepad` FROM `users` WHERE `username` = '".$logged['username']."' ") or die("ERROR");
	$userNotes2 = mysql_fetch_array($userNotes);
	if( !isset($_POST['updatenotes']) )
	{
		$Temp = new Template;
		$Temp->dir = $logged['dskin'];
		$Temp->file = "notepad.tpl";
		$Temp->tp();
		$Temp->tr(array(
			'BODY' => $userNotes2['notepad']
		));
		return $Temp->html;
	}
	else
	{
		$notesCP = htmlspecialchars($_POST['notes']);
		$notepadUD = mysql_query("UPDATE `users` SET `notepad` = '".$notesCP."' WHERE `username` = '".$logged['username']."' ") or die("Couldn't update notes!");
		return imessage("Notepad Updated Sucessfully!","".$logged['username'].", your notepad was updated successfully.","Thank you now your notepad is updated!","index.php?act=Cpanel",true);
	}
}

//Edit Profile
function panel_profile()
{
	global $logged;
	if( !isset($_POST['updatepro']) )
	{
		$Timezone = "";
		for($x=-12;$x<13;$x++){
			if($x == $logged['timezone'])
			{
				$Timezone .= "<option selected=\"selected\" value=\"$x\">$x</option>";
			}
			else
			{
				$Timezone .= "<option value=\"$x\">$x</option>";
			}
		}
		$Temp = new Template;
		$Temp->dir = $logged['dskin'];
		$Temp->file = "cpanel_editprofile.tpl";
		$Temp->tp();
		$Temp->tr(array(
			'AVY' => $logged['avatar'],
			'AIM' => $logged['aim'],
			'MSN' => $logged['msn'],
			'ICQ' => $logged['icq'],
			'SIG' => $logged['signature'],
			'TIMEZONE' => $Timezone
		));
		return $Temp->html;
	}
	else
	{
		$aim = htmlspecialchars($_POST['aim']);
		$msn = htmlspecialchars($_POST['msn']);
		$icq = htmlspecialchars($_POST['icq']);
		$avy = $_POST['avy'];
		$avy = str_replace("\"","",$avy);
		$avy = str_replace("'","",$avy);
		$avy = strip_tags(htmlspecialchars($avy));
		$sig = htmlspecialchars($_POST['siggy']);
		$timeZ = htmlspecialchars($_POST['timezone']);
		$profileu = mysql_query("UPDATE `users` SET `avatar` = '".$avy."', `aim` = '".$aim."', `msn` = '".$msn."', `icq` = '".$icq."', `signature` = '".$sig."', `timezone` = '".$timeZ."' WHERE `username` = '".$logged['username']."' ") or die("Couldn't update user contact admin");
		return imessage("Profile Updated Sucessfully!","","Thank you, now your profile is updated!","index.php?act=Cpanel&option=profile",true);
	}
}

//updates users email
function panel_email()
{
	global $logged;
	if( !isset( $_POST['update'] ) )
	{
		$Temp = new Template;
		$Temp->dir = $logged['dskin'];
		$Temp->file = "cpanel_email.tpl";
		$Temp->tp(__LINE__,__FILE__);
		$Temp->tr(array(
			'E' => $logged['email']
		));
		return $Temp->html;
	}
	else
	{
		if( empty( $_POST['password'] ) )
		{
			pageerror("Profile Error","Something went wrong","Looks like you left the password field blank please go back and fix it.");
		}
		if( empty( $_POST['e1'] ) || empty( $_POST['e2'] ) )
		{
			pageerror("Profile Error","Something went wrong","In order to change your email, you must provide both new email fields with a new email to update email.");
		}
		if( md5( $logged['salt'] . $_POST['password'] ) != $logged['password'] )
		{
			pageerror("Profile Error","Something went wrong","Sorry, but your password was incorrect, please fix this error.");
		}
		if( $_POST['e1'] != $_POST['e2'] )
		{
			pageerror("Profile Error","Something went wrong","Email fields didn't match please go back to make them match.");
		}
		$email = htmlspecialchars( $_POST['e1'] );
		$sql = mysql_query("UPDATE `users` SET `email` = '" . $email . "' WHERE `username` = '" . $logged['username'] . "'");
		if( !$sql )
		{
			pageerror("Profile Error","Something went wrong","There was a problem updating sql: " . mysql_error());
		}
		return imessage("Profile Updated Sucessfully!","","Thank you, now your profile is updated!","index.php?act=Cpanel&option=email",true);
	}
}
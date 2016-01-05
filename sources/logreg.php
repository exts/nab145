<?php
/*
****************************************************************
#sources/logreg.php File
#Version 1.4
****************************************************************
#Copy Righted 2006-2007(http://nevuxbulletin.com)
#Created By NevuxAB Developement Team
****************************************************************
*/
//--------------------------------
//lostpass function
//--------------------------------	
	if(!defined("NABPROOF"))
	{
		die("Hacking attempt");
	}
	function lostpass()
	{
		global $logged;
		if( !isset($_POST['changepass']) )
		{
		//show the form
			$Temp = new Template;
			$Temp->dir = $logged['dskin'];
			$Temp->file = 'lostpass.tpl';
			$Temp->tp();
			echo $Temp->html;
		}
		else
		{
			//do some validation
			$email = htmlspecialchars($_POST['lostpass']);
			$email2 = explode("@",$email);
			if(count($email2) != "2")
			{
				//show error
			}
			$bsurl = mysql_query("SELECT `url` FROM `boardstatus` WHERE id='1' ");
			$burl = mysql_fetch_array($bsurl);
			//if validation goes correct update pass and send that email
			$salt = substr(md5(uniqid(rand(), true)), 0, 5);
			$password = substr(md5(uniqid(rand(), true)), 0, 8);
			$hash = md5($password.$salt).md5($salt);
			$hashlink = "index.php?act=passrecovery&id=".$hash;
			$message = "Hello, you have requested a password recovery please follow this link to complete password update: <br /><br />\n\n".$burl[`url`].$hashlink;
			$user = mysql_query("UPDATE `users` SET `passval` = '".$hash."' WHERE `email` = '".$email."' ") or die(pageerror("Password Recovery Error","There was a problem updating pass validation","Something went wrong updating users password validation please contact an administrator or you won't be able to recovery your password!"));
			val_pass($email,$message);
			finished("Email Sent","Validation was sent to email","Please check your email and follow the password validation link to update your password","index.php");
		}
	}
//--------------------------------
//lostpass2 function
//--------------------------------	
	function lostpass2()
	{
		if( isset($_GET['id']) )
		{
			$id = htmlspecialchars($_GET['id']);
			
			$passhash = mysql_query("SELECT `username`,`passval`,`email` FROM `users` WHERE `passval` = '".$id."' ");
			$passhash = mysql_fetch_array($passhash);
			if($id != $passhash['passval'])
			{
				die(pageerror("Password Recovery Error","Invalid ID","You have reached a ID that doesn't exists you are now logged!"));
			}
			$salt = substr(md5(uniqid(rand(), true)), 0, 5);
			$password = substr(md5(uniqid(rand(), true)), 0, 8);
			$password2 = md5($salt.$password);
			$message = ("
				Hello ".$passhash['username'].",
				You have validated your account and your password has been updated.<br /><br />
				Your New password is: <b>".$password."</b><br />
				Please update your password when finished.
			");
			$username = mysql_query("UPDATE `users` SET `password` = '".$password2."',`salt` = '".$salt."', `passval` = '' WHERE `email` = '".$passhash['email']."' ") or die(pageerror("Password Recovery Error","There was a problem updating password","Something went wrong updating users password please contact an administrator or you won't be able to recovery your password!"));
			val_pass($passhash['email'],$message);
			finished("Email Sent","Validation was sent to email","Please check your email and your password has been updated! ","index.php");
		}
		else
		{
			die(pageerror("Password Recovery Error","Invalid ID","You have reached a ID that doesn't exists you are now logged!"));
		}
	}
//--------------------------------
//val_pass function
//--------------------------------
	function val_pass($email,$message)
	{
		$e_mail = explode("@",$email);
		$subject = "Password Recovery";
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		// Additional headers
		$headers .= 'To: '.$e_mail[0].' <'.$email.'>' . "\r\n";
		$headers .= 'From: Password Recovery < nevuxbulletinpasswordrecovery.com>' . "\r\n";
		$headers .= 'Cc: '.$email.'' . "\r\n";
		$headers .= 'Bcc:  '.$email.'' . "\r\n";
		mail($email, $subject, $message, $headers);
	}
//--------------------------------
//login function
//--------------------------------
	function login()
	{
		global $logged;
		if(!$logged['username'])
		{
			$username = htmlspecialchars($_POST['name']);
			$pass = htmlspecialchars($_POST['password']);
			$num1 = rand(1,15);
			$num2 = rand(1,15);
			$total = $_POST['n1']+$_POST['n2'];
			$_SESSION['answer'] = $total;
			$val = $_POST['valkey'];
			if( !isset($_POST['login']) )
			{
				$Temp = new Template;
				$Temp->dir = $logged['dskin'];
				$Temp->file = 'login.tpl';
				$Temp->tp();
				$Temp->tr(array("NUM"=>$num1,"NUM_"=>$num2));
				echo $Temp->html;
			}
			else
			{
				if($val != $total)
				{
					pageerror("Login Error","There was an error loging in.","Verification # was not correct please go back and check again!");
				}
				else
				{
					$user = mysql_query("SELECT * FROM `users` WHERE `username` = '".$username."'");
					if( ($userz = mysql_num_rows($user) ) == 0)
					{
						pageerror("Login Error","There was an error loging in.","User Doesn't exist!");
					}
					else
					{
						$usa = mysql_fetch_array($user);
						if( ( md5($usa['salt'].$pass) ) != ($usa[password]) )
						{
							pageerror("Login Error","There was an error loging in.","Password was incorrect!");
						}
						else
						{
							$_SESSION['uid'] = htmlspecialchars($usa['id']);
							$_SESSION['upass'] = htmlspecialchars($usa['password']);
							$time = time();
							$hash = mysql_query("UPDATE `users` SET `online` = '".$time."' WHERE `username` = '".$username."' ");
							if( !$hash )
							{
								pageerror("User Error","There was an error hashing pass!","There was an error adding hash please contact admin!");
							}
							else
							{
								finished("Logged In Sucessfully!",$username . " now Logged in!","Thank you now you are now logged in!","index.php");
							}
						}
					}
				}
			}
		}
		else
		{
			finished("Login Error","Error logging in.","Sorry, but you are already logged in.","index.php");
		}
	}
//--------------------------------
//do_logout function
//--------------------------------
	function do_logout()
	{
		global $logged;
		$loggedout = mysql_query("UPDATE `users` SET `online` = '0000000000' WHERE username='".$logged[username]."' ") or die(pageerror("User Error","There was an error logging!","There was an error logging out please contact admin!"));
		finished("Logged Out Sucess","You are now logged out","You sucessfully logged out sucessfully, you will now be redirected to index.","index.php");
		unset($_SESSION['uid']);
		unset($_SESSION['upass']);
		unset($_SESSION['nbb_apanel']);
		session_destroy();
	}
//--------------------------------
//register function
//--------------------------------
	function register()
	{
		//check to see if config on
		$TOS_ON = mysql_query("SELECT `coppa` FROM `boardstatus` WHERE `id` = '1' ") or die(pageerror("Database Error","There was an error selecting data!","There was an error selecting data from database, couldn't fetch coppa info, please contact admin!"));
		$TOS_O = mysql_fetch_array($TOS_ON);
		do_register_1();
	}
//--------------------------------
//do_register_1 function
//--------------------------------	
	function do_register_1()
	{
		//show TOS Rules if pressed agreed show register page
		$TOS = mysql_query("SELECT `coppa` FROM `boardrules` WHERE `id` = '1' ") or die(pageerror("Database Error","There was an error selecting data!","There was an error selecting data from database, couldn't fetch TOS Rules info, please contact admin!"));
		$TOS_R = mysql_fetch_array($TOS);
		do_register_2($TOS_R['coppa']);
	}
//--------------------------------
//do_register_2 function
//--------------------------------
	function do_register_2($TOS)
	{
		global $logged;
		if(!$logged['username'])
		{
			if( !isset($_POST['register']) )
			{
				$Temp = new Template;
				$Temp->dir = $logged['dskin'];
				$Temp->file = 'register.tpl';
				$Temp->tp();
				$Temp->tr(array("TOS"=>$TOS));
				echo $Temp->html;
			}
			else
			{
				$captcha_value = $_POST['verify'];
				$captcha = $_SESSION['captchastr'];
				$email = htmlspecialchars($_POST['email']);
				$email2 = htmlspecialchars($_POST['email2']);
				$pass = htmlspecialchars($_POST['password']);
				$pass2 = $_POST['password2'];
				$username = htmlspecialchars($_POST['name']);
				$user = mysql_query("SELECT * FROM `users` WHERE `username` = '".$username."' ");
				$user = mysql_num_rows($user);
				$TOSz = $_POST['TOS'];
				if($user != 0)
				{
					die(pageerror("Registration Error","There was an error in registering!","User name already exists please try a new name!"));
				}
				elseif( $captcha_value != $captcha )
				{
					die(pageerror("Registration Error","There was an error in registering!","Your verification characters were incorrect.  Please go back and try again.  If you can't see the characters, refresh else contact the administrator."));
				}
				elseif( empty($username) || empty($pass) || empty($pass2) || empty($email) || empty($email2) )
				{
					die(pageerror("Registration Error","There was an error in registering!","There was a input left empty please go back and try again!"));
				}
				elseif( !isset($TOSz) )
				{
					die(pageerror("Registration Error","There was an error in registering!","In order to register on this site you must agree to TOS!"));
				}
				
				$ip =$_SERVER["REMOTE_ADDR"];
				$salt = substr(md5(uniqid(rand(), true)), 0, 5);
				$hash = md5($salt.$pass);
				$online = time();
				$timezone = intval(htmlspecialchars($_POST['timezone']));
				$new_user = mysql_query("INSERT INTO `users` (`username`,`password`,`email`,`ip`,`salt`,`online`,`timezone`) VALUES('".$username."','".$hash."','".$email."','".$ip."','".$salt."','".$online."','".$timezone."') ") or die(pageerror("Registration Error","There was an error in registering!","Something went wrong in the database please contact administrator!"));
				$users = mysql_query("SELECT `id` FROM `users` ORDER BY `id` DESC LIMIT 1");
				$users = mysql_fetch_array($users);
				$_SESSION['uid'] = ($users['id']);
				$_SESSION['upass'] = htmlspecialchars($hash);
				finished("Registered Sucessfully!",$username . " now Logged in!","Thank you now you are now logged in and Registered!","index.php");
			}
		}
		else
		{
			finished("Register Error","Error registering in.","Sorry, but you are already logged in.  Please logout before you can reregister.","index.php");
		}
	}
<?php
/*
****************************************************************
#sources/admin/login.php File
#Version 1.4
****************************************************************
#Copy Righted 2006-2007(http://nevuxbulletin.com) [nevuxab.info]
#Created By NevuxAB Developement Team
****************************************************************
*/
	
	function login()
	{	
		global $logged,$permissions;
		if($logged['username'])
		{
			if( !isset($_POST['submit']) )
			{
				echo
				('
					<form method="post" action="">
						<table width="100%" class="forum" cellspacing="1" cellpadding="0">
							<tr>
								<td class="category" colspan="2"><div class="cat_title">Please Login</div></td>
							</tr>
							<tr>
								<td width="100%" class="small_title" colspan="2" align="center"><span>Login in order to be able to manage the admin control panel.</span></td>
							</tr>
							<tr>
								<td width="20%" class="common">Username</td>
								<td width="80%" class="common"><input type="text" name="username" /></td>
							</tr>
							<tr>
								<td width="20%" class="common">Password</td>
								<td width="80%" class="common"><input type="password" name="password" /></td>
							</tr>
							<tr>
								<td colspan="2" align="center" class="common"><input type="submit" name="submit" value="Login" /></td>
							</tr>
						</table>
					</form>
				');
			}	
			else
			{
				if( !empty($_POST['username']) AND !empty($_POST['password']) )
				{
					$username = htmlspecialchars($_POST['username']);
					$password = htmlspecialchars($_POST['password']);
					$info = mysql_query("SELECT `salt`,`password`,`level` FROM `users` WHERE `username` = '".$username."'") or die(pageerror("SQL Error","There was an error selecting information from database.","Sorry there was an error trying to select user hash and user password from the Database."));
					$info_ = mysql_fetch_array($info);
					if( md5($info_['salt'] . $password ) == $info_['password'])
					{
						if($permissions['admin'] == 't')
						{
							$_SESSION['admin'] = true;
							finished("Login Successful","Your now logged in as an administrator","You have successfully logged in as admin","acp.php");
						}
						else
						{
							pageerror("Login Error","Error logging into the admin panel.","Sorry, you are not an administrator!");
						}
					}
					else
					{
						pageerror("Login Error","Error logging into the admin panel.","Sorry, the password was incorrect for this account.");
					}
				}
				else
				{
					pageerror("Login Error","There was an error logging in.","Sorry, but you left a field blank please go back and try again.");
				}
			}
		}
		else
		{
			finished("Login Error","Session not found.","In order to login to the administration you will need to login to the forums first for security reasons.","index.php");
		}
	}
	function is_admin()
	{
		if( !isset($_SESSION['admin']) )
		{
			return false;
		}
		return true;
	}
?>
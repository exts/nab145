<?php
/*
****************************************************************
#sources/admin/ACP.php File
#Version 1.4
****************************************************************
#Copy Righted 2006-2007(http://nevuxbulletin.com) [nevuxab.info]
#Created By NevuxAB Developement Team
****************************************************************
*/
	function acp_run()
	{
		global $logged;
		switch($_GET['action'])
		{
			case "test":
				return ("
					<table width='100%' cellspacing='3' cellpadding='0'>
						<tr>
							<td width='80%'><strong>Category Name</strong></td>
							<td width='20%'><a href='#'>Edit</a> <a href='#'>Delete</a></td></td>
						</tr>
						<tr>
							<td colspan='2'><a href='#'>Forum 1</a>, <a href='#'>Forum 1</a>, <a href='#'>Forum 1</a>, <a href='#'>Forum 1</a>, <a href='#'>Forum 1</a>, <a href='#'>Forum 1</a>, <a href='#'>Forum 1</a>, <a href='#'>Forum 1</a>, <a href='#'>Forum 1</a>, <a href='#'>Forum 1</a>, <a href='#'>Forum 1</a>, <a href='#'>Forum 1</a>, <a href='#'>Forum 1</a>, <a href='#'>Forum 1</a>, <a href='#'>Forum 1</a>, <a href='#'>Forum 1</a>, <a href='#'>Forum 1</a>, <a href='#'>Forum 1</a></td>
						</tr>
						<tr>
							<td width='80%'><strong>Category Name</strong></td>
							<td width='20%'><a href='#'>Edit</a> <a href='#'>Delete</a></td></td>
						</tr>
						<tr>
							<td colspan='2'><a href='#'>Forum 1</a></td>
						</tr>
					</table>
				");
			break;
			case "editusergroup":
				return edit_groups();
			break;
			case "boards":
				return manageboards();
			break;	
			case "edituser":
				return editusers();
			break;
			case "adduser":
				return adduser();
			break;
			case "newcat":
				return addcat();
			break;
			case "newforum":
				return newforum();
			break;
			case "config":
				return settings();
			break;	
			default:
				return acp_home();
			break;
		}
	}
	function acp_home()
	{
		return "Welcome to the Administration panel, here you will be able to manage your forums,users with ease By modifying settings. <br /><br /><br />" . vchecker();
	}	
	//version checker
	function vchecker()
	{
		global $_CONFIG;
		$url = "http://nevuxab.info/versionchecker.php";
		if(function_exists("curl_init"))
		{
			$send = curl_init();
			curl_setopt($send,CURLOPT_POST,1);
			curl_setopt($send,CURLOPT_HEADER,0);
			curl_setopt($send, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($send,CURLOPT_URL,$url);
			curl_setopt($send,CURLOPT_POSTFIELDS,"ver=" . $_CONFIG['VERSION'] . "&stage=" . $_CONFIG['STAGE']);
			$result = curl_exec($send);
			return substr($result,0,strlen($result)-1) . ".";
		}
		return "";
	}
	//configuration manage settings in the acp.
	function settings()
	{
		if( !isset($_POST['submit']) )
		{
			$sql = mysql_query("SELECT * FROM `boardstatus` WHERE `id` = '1'");
			if(mysql_num_rows($sql) > 0)
			{
				extract(($rows = mysql_fetch_array($sql)));
				$banned = parse_banned_ips($banned,"v");
				return 
				("
				<form method='post' action=''>
					<table width='100%' cellspacing='3' cellpadding='0'>
						<tr>
							<td width='50%'><strong>Board Online</strong></td>
							<td width='50%'><input type='checkbox' ". (($status == "online") ? "checked='checked'" : '')."name='online' /></td>
						</tr>
						<tr>
							<td width='50%' valign='top'><strong>Forum offline message</strong></td>
							<td width='50%'><textarea name='reason'>".$reason."</textarea></td>
						</tr>
						<tr>
							<td width='50%'><strong>Registration Online</strong></td>
							<td width='50%'><input type='checkbox' name='register' ". (($registration == "on") ? "checked='checked'" : '')." /></td>
						</tr>
						<tr>
							<td width='50%'><strong>Community Name</strong></td>
							<td width='50%'><input type='text' name='bname' value='".$boardname."'/></td>
						</tr>
						<tr>
							<td width='50%'><strong>Board URL</strong> <em>[http://urlsite.com/]</em></td>
							<td width='50%'><input type='text' name='url' value='".$url."' /></td>
						</tr>
						<tr>
							<td width='50%'><strong>Post Per Topic</strong></td>
							<td width='50%'><input type='text' name='ppt' value='".$postpertopic."' /></td>
						</tr>
						<tr>
							<td width='50%'><strong>Topics Per Forum</strong></td>
							<td width='50%'><input type='text' name='tpf' value='".$topicsperforum."' /></td>
						</tr>
						<tr>
							<td width='50%' valign='top'><strong>Banned IP Addresses</strong></td>
							<td width='50%'><textarea name='banned'>".$banned."</textarea></td>
						</tr>
						<tr>
							<td colspan='2' align='center'><input type='submit' name='submit' value='Update Config' /></td>
						</tr>
					</table>
				</form>
				");
			}
			else
			{
				return "There was an error selecting data from database, this row doesn't exist";
			}
		}
		else
		{
			if( !empty($_POST['url']) AND !empty($_POST['bname']) )
			{
				$boardname = htmlspecialchars($_POST['bname']);
				$register = (isset($_POST['register'])) ? 'on' : 'off';
				$url = htmlspecialchars($_POST['url']);
				$online = (isset($_POST['online'])) ? "online" : "offline";
				$banned = parse_banned_ips($_POST['banned'],"u");
				$ppt = intval( htmlspecialchars( $_POST['ppt'] ) );
				$reason = htmlspecialchars( $_POST['reason'] );
				$tpf = intval( htmlspecialchars( $_POST['tpf'] ) );
				if( mysql_query("UPDATE `boardstatus` SET `topicsperforum` = '".$tpf."', `reason` = '" . $reason. "', `postpertopic` = '" . $ppt . "', `boardname` = '".$boardname."', `url` = '".$url."', `status` = '".$online."', `registration` = '".$register."',`banned` = '".$banned."' WHERE `id` = '1'") )
				{
					return "Configuration was updated successfully.";
				}
				else
				{
					return "Sorry, there was a problem updating configuration, please contact NevuxAB Tech Support.";
				}
			}
			else
			{
				return "Sorry, you left a field blank please go back and try again.";
			}
		}
	}
	//Add New Forum
	function newforum()
	{
		if( !isset($_POST['submit']) )
		{
			return
			("
				<form method='post' action=''>
					<table width='100%' cellspacing='3' cellpadding='0'>
						<tr>
							<td width='30%'>Forum Name</td>
							<td width='70%'><input type='text' name='name' /></td>
						</tr>
						<tr>
							<td width='30%' valign='top'>Forum Description</td>
							<td width='70%'><textarea cols='20' rows='5' name='desc'></textarea></td>
						</tr>
						<tr>
							<td width='30%'>Forum Parent</td>
							<td width='70%'>".parents(-1)."</td>
						</tr>
						<tr>
							<td width='30%'>Forum Locked</td>
							<td width='70%'><input type='checkbox' name='locked' /></td>
						</tr>
						<tr>
							<td colspan='2'>
								".forum_permissions(0,1)."
							</td>
						</tr>
					</table>
				</form>
			");
		}
		else
		{

			if(!empty($_POST['parent']) AND !empty($_POST['name']) )
			{
				$views = array();
				$read = array();
				$reply_p = array();
				$reply_t = array();
				(isset($_POST['gview'])) ? $views[0] = 't' : '';
				(isset($_POST['gread'])) ? $read[0] = 't' : '';
				if(isset($_POST['view']))
				{
					foreach($_POST['view'] as $v)
					{
						$views[$v] = 't';
					}	
				}
				if(isset($_POST['read']))
				{
					foreach($_POST['read'] as $b)
					{
						$read[$b] = 't';
					}
				}
				if(isset($_POST['reply']))
				{
					foreach($_POST['reply'] as $w)
					{
						$reply_p[$w] = 't';
					}
				}
				if(isset($_POST['topic']))
				{
					foreach($_POST['topic'] as $e)
					{
						$reply_t[$e] = 't';
					}
				}
				$permissions = serialize(array('view' => $views,'read' => $read,'reply' => $reply_p,'topic' => $reply_t));
				$parent = explode("|",$_POST['parent']);
				$parent_ = ($parent[0] == 'cat') ? "`cid`" : "`sid`";
				$title = htmlspecialchars($_POST['name']);
				$desc = htmlspecialchars($_POST['desc']);
				$locked = (isset($_POST['locked'])) ? 't':'f';
				if( mysql_query("INSERT INTO `forums`(".$parent_.",`title`,`description`,`locked`,`permissions`) VALUES('".$parent[1]."','".$title."','".$desc."','".$locked."','".$permissions."')") )
				{
					return "Forum was successfully added to database.";
				}
				else
				{
					return "Sorry, there was an sql error trying to insert data into database.";
				}
			}
			else
			{
				return "You either left a field blank, or you need to create a category before adding any forums.";
			}
		}
	}
	function forum_permissions($id,$type,$e=null)
	{
		switch($type)
		{
			case 1:
				$s = mysql_query("SELECT `id`,`name` FROM `groups`");
				while($r = mysql_fetch_array($s))
				{
					$g .= "
							<tr>
								<td width='28%'>".$r['name']."</td>
								<td width='12%'><input type='checkbox' name='view[]' value='".$r['id']."' /></td>
								<td width='12%'><input type='checkbox' name='read[]' value='".$r['id']."' /></td>
								<td width='12%'><input type='checkbox' name='reply[]' value='".$r['id']."' /></td>
								<td width='12%'><input type='checkbox' name='topic[]' value='".$r['id']."' /></td>
							</tr>
					";
				}
				$groups = "
				<div align='center'>
				<fieldset class='fieldset'> 
					<legend>Group Permissions</legend>
					<table width='100%' cellspacing='3' cellpadding='0'>
						<tr>
							<td width='28%'><strong>Group Name</strong></td>
							<td width='12%'><strong>View</strong></td>
							<td width='12%'><strong>Read</strong></td>
							<td width='12%'><strong>Reply(P)</strong></td>
							<td width='12%'><strong>Reply(T)</strong></td>
						</tr>
						<tr>
							<td width='28%'>Guest</td>
							<td width='12%'><input type='checkbox' name='view[]' value='0' /></td>
							<td width='12%'><input type='checkbox' name='read[]' value='0' /></td>
							<td width='12%'>&nbsp;</td>
							<td width='12%'>&nbsp</td>
						<tr>
						<tr><td colspan='5'><strong>Built in/Custom Groups</strong></td></tr>
						".$g."
					</table>
				</fieldset>
				</div>
				<table width='100%'>
					<tr>
						<td align='center'><input type='submit' name='submit' value='Add Forum' /></td>
					</tr>
				</table>
				";
				return $groups;
			break;
			case 2:
				$perms = ($e == "") ? array() : unserialize($e);
				$g = "";
				$s = mysql_query("SELECT `id`,`name` FROM `groups`");
				while($r = mysql_fetch_array($s))
				{
					$g .= "
							<tr>
								<td width='28%'>".$r['name']."</td>
								<td width='12%'><input type='checkbox' name='view[]' value='".$r['id']."' " . (($perms['view'][$r['id']] == 't') ? "checked='checked'" : '') . " /></td>
								<td width='12%'><input type='checkbox' name='read[]' value='".$r['id']."' " . (($perms['read'][$r['id']] == 't') ? "checked='checked'" : '') . " /></td>
								<td width='12%'><input type='checkbox' name='reply[]' value='".$r['id']."' " . (($perms['reply'][$r['id']] == 't') ? "checked='checked'" : '') . " /></td>
								<td width='12%'><input type='checkbox' name='topic[]' value='".$r['id']."' " . (($perms['topic'][$r['id']] == 't') ? "checked='checked'" : '') . " /></td>
							</tr>
					";
				}
				$groups = "
				<div align='center'>
				<fieldset class='fieldset'> 
					<legend>Group Permissions</legend>
					<table width='100%' cellspacing='3' cellpadding='0'>
						<tr>
							<td width='28%'><strong>Group Name</strong></td>
							<td width='12%'><strong>View</strong></td>
							<td width='12%'><strong>Read</strong></td>
							<td width='12%'><strong>Reply(P)</strong></td>
							<td width='12%'><strong>Reply(T)</strong></td>
						</tr>
						<tr>
							<td width='28%'>Guest</td>
							<td width='12%'><input type='checkbox' name='view[]' " . (($perms['view'][0] == 't') ? 'checked=\'checked\'' : '') . " value='0' /></td>
							<td width='12%'><input type='checkbox' name='read[]' " . (($perms['read'][0] == 't') ? 'checked=\'checked\'' : '') . "  value='0' /></td>
							<td width='12%'>&nbsp;</td>
							<td width='12%'>&nbsp</td>
						<tr>
						<tr><td colspan='5'><strong>Built in/Custom Groups</strong></td></tr>
						" . $g . "
					</table>
				</fieldset>
				</div>
				";
				return $groups;
			break;
		}
	}
	function parents()
	{
		$opts = "";
		$sql = mysql_query("SELECT * FROM `categories` ORDER BY `order`");
		if(mysql_num_rows($sql) > 0)
		{
			while( $row = mysql_fetch_array($sql) )
			{
				if(func_get_arg(0) && func_get_arg(0) == $row['id'] && func_get_arg(1) == "c")
				{
					$opts .= "<option selected='selected' value='cat|".$row['id']."'>".$row['title']."</option>\n";
				}
				else
				{
					$opts .= "<option value='cat|".$row['id']."'>".$row['title']."</option>\n";
				}	
				$forums = mysql_query("SELECT * FROM `forums` WHERE `cid` = '".$row['id']."'");
				while( $forum = mysql_fetch_array( $forums ) )
				{
					if(func_get_arg(0) && func_get_arg(0) == $forum['id'] && func_get_arg(1) == "f")
					{
						$opts .= "<option selected='selected' value='forum|".$forum['id']."'>&nbsp;&nbsp;&nbsp;&nbsp;".$forum['title']."</option>\n";
					}
					else
					{
						$opts .= "<option value='forum|".$forum['id']."'>&nbsp;&nbsp;&nbsp;&nbsp;".$forum['title']."</option>\n";
					}
				}
			}
			return "<select name='parent'>" . $opts . "</select>";
		}
		else
		{
			return "Create a Category before you can create a forum.";
		}
	}
//Add a new category
	function addcat()
	{
		if( !isset($_POST['submit']) )
		{
			return
			("
				<form method='post' action=''>
					<table width='100%' cellspacing='3' cellpadding='0'>
						<tr>
							<td width='30%'>Category Name</td>
							<td width='70%'><input type='text' name='name' /></td>
						</tr>
						<tr>
							<td colspan='2' align='center'><input type='submit' name='submit' value='Add Category' /></td>
						</tr>
					</table>
				</form>
			");
		}
		else
		{
			if( !empty($_POST['name']) )
			{
				$cat = htmlspecialchars($_POST['name']);
				if( mysql_query("INSERT INTO `categories` (`title`) VALUES('".$cat."')") )
				{
					return "Category was added successfully to the database.";
				}
				else
				{
					return "There was a sql error trying to insert category into database please contact Nevux Ability Boards Tech Team.";
				}
			}
			else
			{
				return "Sorry, you left a field blank.";
			}
		}
	}
//Add User
	function adduser()
	{
		if( !isset($_POST['submit']) )
		{
			return 
			("
				<form method='post' action=''>
					<table width='100%' cellspacing='3' cellpadding='0'>
						<tr>
							<td width='30%'>Username</td>
							<td width='70%'><input type='text' name='name' /></td>
						</tr>
						<tr>
							<td width='30%'>Password</td>
							<td width='70%'><input type='password' name='pass' /></td>
						</tr>
						<tr>
							<td width='30%'>Email</td>
							<td width='70%'><input type='text' name='email' /></td>
						</tr>
						<tr>
							<td width='30%'>Group</td>
							<td width='70%'><select name='level'>" . ggOptions() . "</select></td>
						</tr>
						<tr>
							<td colspan='2' align='center'><input type='submit' name='submit' value='Add User' /></td>
						</tr>
					</table>
				</form>
			");
		}
		else
		{
			if( !empty($_POST['name']) AND !empty($_POST['pass']) AND !empty($_POST['email']) )
			{
				$username = htmlspecialchars($_POST['name']);
				$salt = substr(md5(rand(0,1999)),0,8);
				$pass = htmlspecialchars(md5($salt.$_POST['pass']));
				$email = htmlspecialchars($_POST['email']);
				$level = intval(htmlspecialchars($_POST['level']));
				if( !user_exists($username) )
				{
					if( mysql_query("INSERT INTO `users` (`username`,`password`,`salt`,`email`,`level`) VALUES('".$username."','".$pass."','".$salt."','".$email."','".$level."')") )
					{
						return "User <b>" . $username . "</b> was created successfully, with the password <b>".$_POST['pass']."</b>";
					}
					else
					{
						return "Sorry, there was an error inserting user into database, contact Nevux Ability Boards Tech Support.";
					}
				}
				else
				{
					return "Sorry, this user account already exists.";
				}
			}
			else
			{
				return "Sorry, you left a field blank please go back and try again.";
			}
		}
	}
//Edit Users
	function editusers()
	{
		global $logged;
		//search for users
		if(!isset($_GET['find']))
		{
			return
			("
				<form method='post' action='acp.php?action=edituser&find='>
					<table width='100%'>
						<tr>
							<td width='100%' align='center'><input type='text' name='username' /></td>
						</tr>	
						<tr>
							<td width='100%'><input type='submit' name='userz' value='Find' /></td>
						</tr>
					</table>
				</form>
			");
		}
		else
		{
			$users = "";
			$search = htmlspecialchars($_POST['username']);
			$sql = mysql_query("SELECT `username` FROM `users` WHERE `username` LIKE '%".$search."%'");
			if(mysql_num_rows($sql) > 0)
			{
				while( $row = mysql_fetch_array($sql) )
				{
					$users .= "<option value='".$row['username']."'>" . $row['username'] . "</option>";
				}
				if( !isset($_GET['edit']) )
				{
					return
					("
						<form method='post' action='acp.php?action=edituser&find=&edit='>
							<table width='100%'>
								<tr>
									<td width='100%' align='center'><select name='users'>" . $users . "</select></td>
								</tr>
								<tr>
									<td width='100%'><input type='submit' name='edit' value='Edit This Account' /></td>
								</tr>
							</table>
						</form>
					");
				}
				else
				{
					$uname = htmlspecialchars($_POST['users']);
					$use = mysql_query("SELECT * FROM `users` WHERE `username` = '".$uname."'");
						if( !isset($_POST['modify']) )
						{
							$info = mysql_fetch_array($use);
							return 
							("
								<form method='post' action=''>
									<table width='100%'>
										<tr>
											<td width='30%'>IP Address</td>
											<td width='70%'><em>".$info['ip']."</em></td>
										</tr>
										<tr>
											<td width='30%'>Username</td>
											<td width='70%'><input type='hidden' name='id' value='".$info['id']."' /><input type='text' name='username' value='".$info['username']."' /></td>
										</tr>
										<tr>
											<td width='30%'>Email</td>
											<td width='70%'><input type='text' name='email' value='".$info['email']."' /></td>
										</tr>
										<tr>
											<td width='30%'>Group</td>
											<td width='70%'><select name='group'>". user_groups($info['level']) . "</select></td>
										</tr>
										<tr>
											<td width='30%'>Banned</td>
											<td width='70%'><input type='checkbox' name='banned' ".(($info['banned'] == 0) ? '' : 'checked="checked"')." /></td>
										</tr>
										<tr>
											<td width='30%' valign='top'>Signature</td>
											<td width='70%'><textarea name='signature' cols='20' rows='5'>".$info['signature']."</textarea></td>
										</tr>
										<tr>
											<td width='30%'>Avatar</td>
											<td width='100%'><input type='text' name='avatar' value='".$info['avatar']."' /></td>
										</tr>
										<tr>
											<td width='30%'>Posts</td>
											<td width='70%'><input type='text' name='post' value='".$info['post']."' /></td>
										</tr>
										<tr>
											<td width='100%' colspan='2' align='center'><input type='submit' name='modify' value='Edit Profile' /></td>
										</tr>
									</table>
								</form>
							");
						}
						else
						{
							if( !empty($_POST['username']) AND !empty($_POST['email']) )
							{
								$username = htmlspecialchars($_POST['username']);
								$id = intval(htmlspecialchars($_POST['id']));
								$email = htmlspecialchars($_POST['email']);
								$post = htmlspecialchars($_POST['post']);
								$signature = htmlspecialchars($_POST['signature']);
								$avy = htmlspecialchars($_POST['avatar']);
								$banned = ($_POST['id'] == 1) ? 0 : ((isset($_POST['banned'])) ? 1 : 0);
								$group = ($_POST['id'] == 1) ? 5 : intval(htmlspecialchars($_POST['group']));
								if($_POST['id'] == 1)
								{
									if($logged['id'] == 1)
									{
										if( mysql_query("UPDATE `users` SET `username` = '".$username."',`email` = '".$email."', `post` = '".$post."',`level` = '".$group."',`signature` = '".$signature."',`avatar` = '".$avy."',`banned` = '".$banned."' WHERE `id` = '".$id."' ") )
										{
											return "This account was updated successfully.";
										}
										else
										{
											return "There was an error inserting data into database, please contact Nevux Ability Boards Support Tech.";
										}
									}
									else
									{
										return "Sorry, you can't modify the root account.";
									}
								}
								else
								{
									if( mysql_query("UPDATE `users` SET `username` = '".$username."',`email` = '".$email."', `post` = '".$post."',`level` = '".$group."',`signature` = '".$signature."',`avatar` = '".$avy."',`banned` = '".$banned."' WHERE `id` = '".$id."' ") )
									{
										return "This account was updated successfully.";
									}
									else
									{
										return "There was an error inserting data into database, please contact Nevux Ability Boards Support Tech.";
									}
								}
							}
							else
							{
								return "You either left the email,username or signature field empty, please go back and make sure those fields where not left blank.";
							}
						}
				}
			}
			else
			{
				return "There are no users.";
			}
		}
	}
	function user_groups($groups)
	{
		return get_user_groups($groups);
	}
	function manageboards()
	{
		if( !isset($_GET['type']) )
		{
			$BODY = "";
			$cats = mysql_query("SELECT * FROM `categories` ORDER BY `order`");
			if(mysql_num_rows($cats) > 0)
			{
				$BODY .= "<table width='100%' cellspacing='3' cellpadding='0'>";
				while( $row = mysql_fetch_array( $cats) )
				{
					$BODY .=
					("
						<tr>
							<td width='80%'><strong>".$row['title']."</strong></td>
							<td width='20%'><a href='acp.php?action=boards&type=cat&id=".$row['id']."&edit'>Edit</a> <a href='acp.php?action=boards&type=cat&id=".$row['id']."&delete'>Delete</a></td></td>
						</tr>
					");
					$forums = mysql_query("SELECT * FROM `forums` WHERE `cid` = '".$row['id']."'");
					$forums_ = "";
					while( $forum = mysql_fetch_array( $forums ) )
					{
						$forums_ .= "<a href='acp.php?action=boards&type=forum&id=".$forum['id']."&edit'>" . $forum['title'] ."</a>, ";
					}
						$BODY .= ("
							<tr>
								<td colspan='2'>" . substr($forums_,0,strlen($forums_)-2) . "</td>
							</tr>
						");
				}
				$BODY .= "</table>";
			}
			else
			{
				return "There aren't any categorys in the database, go create some.";
			}
			return $BODY;
		}
		else
		{
			switch($_GET['type'])
			{
				case "cat":
					if(isset($_GET['edit']) AND !isset($_GET['delete']))
					{
						$cid = intval(htmlspecialchars($_GET['id']));
						$sql = mysql_query("SELECT * FROM `categories` WHERE `id` = '".$cid."'");
						$row = mysql_fetch_array($sql);
						if(!isset($_POST['submit']))
						{
							return ("
								<form method='post' action=''>
									<table width='100%' cellspacing='3' cellpadding='0'>
										<tr>
											<td width='30%'>Category Name</td>
											<td width='70%'><input type='text' name='name' value='".$row['title']."' /></td>
										</tr>
										<tr>
											<td colspan='2' align='center'><input type='submit' name='submit' value='Edit Category' /></td>
										</tr>
									</table>
								</form>
							");
						}
						else
						{
							if(!empty($_POST['name']))
							{
								$category = htmlspecialchars($_POST['name']);
								$id = intval(htmlspecialchars($_GET['id']));
								if( mysql_query("UPDATE `categories` SET `title` = '".$category."' WHERE `id` = '".$id."'") )
								{
									return "Category was updated successfully.";
								}
								else
								{
									return "There was a problem updating category, please contact Nevux Ability Boards Tech Support.";
								}
							}	
							else
							{
								return "You left a field blank please go back and make sure all fields are filled.";
							}
						}
					}
					elseif(isset($_GET['delete']) AND !isset($_GET['edit']) )
					{
						if(!isset($_POST['delete']))
						{
							return ("
								<form method='post' action=''>
									<table width='100%'>
										<tr>
											<td width='50%'>Are you Sure you want to delete this Category?</td><td width='50%'><input type='submit' name='delete' value='Delete' /></td>
										</tr>
									</table>
								</form>
							");
						}
						else
						{
							$id = intval(htmlspecialchars($_GET['id']));
							if( mysql_query("DELETE FROM `categories` WHERE `id` = '".$id."'") )
							{
								return "Category was deleted successfully.";
							}
							else
							{
								return "There was an error deleteing categorys from Database.";
							}
						}
					}
					else
					{
						return "Error action.";
					}
				break;
				case "forum":
					if(isset($_GET['edit']) AND !isset($_GET['delete']))
					{	
						$id = intval(htmlspecialchars($_GET['id']));
						$sql = mysql_query("SELECT * FROM `forums` WHERE `id` = '".$id."'");
						$row = mysql_fetch_array($sql);
						$sub = "";
						$sub_ = mysql_query("SELECT * FROM `forums` WHERE `sid` = '".$row['id']."'");
						if(mysql_num_rows($sub_) > 0)
						{
							while($rows = mysql_fetch_array($sub_))
							{
								$sub .= "<a href='acp.php?action=boards&type=forum&id=".$rows['id']."&edit'>".$rows['title']."</a>, ";
							}		
						}
						if(!isset($_POST['submit']))
						{
							return
							("
								<form method='post' action=''>
									<table width='100%' cellspacing='3' cellpadding='0'>
										<tr>
											<td width='30%'>Forum Name</td>
											<td width='70%'><input type='text' name='name' value='".$row['title']."' /></td>
										</tr>
										<tr>
											<td width='30%' valign='top'>Forum Description</td>
											<td width='70%'><textarea cols='20' rows='5' name='desc'>".$row['description']."</textarea></td>
										</tr>
										<tr>
											<td width='30%'>Forum Parent</td>
											<td width='70%'>".parents((($row['cid'] != 0) ? $row['cid'] : $row['sid']),(($row['cid'] != 0) ? "c" : "f"))."</td>
										</tr>
										<tr>
											<td width='30%'>Forum Locked</td>
											<td width='70%'><input type='checkbox' " . (($row['locked'] == 't') ? 'checked="checked"' : '') ." name='locked' /></td>
										</tr>
										<tr>
											<td colspan='2'>
												".forum_permissions(1,2,$row['permissions'])."
											</td>
										</tr>
										<tr>
											<td colspan='2' align='center'><input type='submit' name='submit' value='Edit Forum' /></td>
										</tr>
										<tr>
											<td colspan='2' width='100%'>".(($sub == "") ? '' : '<strong>SubForums</strong>: ' . substr($sub,0,(strlen($sub)-2)))."</td>
										</tr>
										<tr>
											<td colspan='2'><a href='acp.php?action=boards&type=forum&id=".$_GET['id']."&delete'>Delete Forum</a></td>
										</tr>
									</table>
								</form>
							");
						}
						else
						{
							if(!empty($_POST['parent']) AND !empty($_POST['name']) )
							{	
								$views = array();
								$read = array();
								$reply_p = array();
								$reply_t = array();
								if($_POST['view'])
								{
									foreach($_POST['view'] as $v)
									{
										$views[$v] = 't';
									}	
								}
								if($_POST['read'])
								{
									foreach($_POST['read'] as $b)
									{
										$read[$b] = 't';
									}
								}
								if($_POST['reply'])
								{
									foreach($_POST['reply'] as $w)
									{
										$reply_p[$w] = 't';
									}
								}
								if($_POST['topic'])
								{
									foreach($_POST['topic'] as $e)
									{
										$reply_t[$e] = 't';
									}
								}
								$permissions = serialize(array('view' => $views,'read' => $read,'reply' => $reply_p,'topic' => $reply_t));
								$permissions = mysql_real_escape_string($permissions);
								$id = intval(htmlspecialchars($_GET['id']));
								$parent = explode("|",$_POST['parent']);
								$parent_ = ($parent[0] == 'cat') ? "`cid`" : "`sid`";
								$parent2_ = ($parent[0] == 'cat') ? "`sid`" : "`cid`";
								$title = htmlspecialchars($_POST['name']);
								$desc = htmlspecialchars($_POST['desc']);
								$locked = (isset($_POST['locked'])) ? 't' : 'f';
								if( mysql_query("UPDATE `forums` SET `permissions` = '".$permissions."', ".$parent2_." = '0', ".$parent_." = '".$parent[1]."', `title` = '".$title."',`description` = '".$desc."',`locked` = '".$locked."' WHERE `id` = '".$id."'") )
								{
									return "Forum was successfully updated into database." . $parent[0];
								}
								else
								{
									return "Sorry, there was an sql error trying to update data into database.";
								}
							}
							else
							{
								return "You either left a field blank, or you need to create a category before adding any forums.";
							}
						}
					}
					elseif(isset($_GET['delete']) AND !isset($_GET['edit']) )
					{
						$id = intval(htmlspecialchars($_GET['id']));
						if(!isset($_POST['delete']))
						{
							return ("
								<form method='post' action=''>
									<table width='100%'>
										<tr>
											<td width='50%'>Are you Sure you want to delete this Forum?</td><td width='50%'><input type='submit' name='delete' value='Delete' /></td>
										</tr>
									</table>
								</form>
							");
						}
						else
						{
							$id = intval(htmlspecialchars($_GET['id']));
							if( mysql_query("DELETE FROM `forums` WHERE `id` = '".$id."'") )
							{
								return "Forum was deleted successfully.";
							}
							else
							{
								return "There was an error deleteing Forum from Database.";
							}
						}
					}
					else
					{
						return "Error action.";
					}
				break;
			}
		}
	}
	function edit_groups()
	{
	
		switch($_GET['option'])
		{
			case "delete":
				$id = intval(htmlspecialchars($_GET['id']));
				if(!isset($_POST['delete']))
				{
					return ("
						<form method='post' action=''>
							<table width='100%'>
								<tr>	
									<td width='50%'>Are you Sure you want to delete this Group?</td><td width='50%'><input type='submit' name='delete' value='Delete' /></td>
								</tr>
							</table>
						</form>
					");
				}
				else
				{
					$id = intval(htmlspecialchars($_GET['id']));
					if( mysql_query("DELETE FROM `groups` WHERE `id` = '".$id."' AND `default` != 'y'") )
					{
						return "Group was deleted successfully.";
					}
					else
					{
						return "There was an error deleteing Group from Database.";
					}
				}
			break;
			case "edit":
				$id = intval(htmlspecialchars($_GET['id']));
				$group = mysql_query("SELECT * FROM `groups` WHERE `id` = '".$id."'");
				if(mysql_num_rows($group) > 0)
				{
					$sql = mysql_fetch_array($group);
					if(!isset($_POST['submit']))
					{
						
						return
						("
							<form method='post' action=''>
								<table width='100%'>
									<tr>
										<td width='30%'>Group Name<td>
										<td width='70%'><input type='text' name='gn' value='".$sql['name']."' /></td>
									</tr>
									<tr>
										<td width='30%'>Prefix<td>
										<td width='70%'><input type='text' name='gp' value='".str_replace("'","\"",$sql['pre'])."' /></td>
									</tr>
									<tr>
										<td width='30%'>Suffix<td>
										<td width='70%'><input type='text' name='gs' value='".str_replace("'","\"",$sql['suf'])."' /></td>
									</tr>
									<tr>
										<td width='30%'>Can Edit Topic<td>
										<td width='70%'><input type='checkbox' name='gcet' " . (($sql['e_topic'] == 't') ? 'checked="checked"' : '') . " /></td>
									</tr>
									<tr>
										<td width='30%'>Can Edit Posts<td>
										<td width='70%'><input type='checkbox' name='gcep' " . (($sql['e_post'] == 't') ? 'checked="checked"' : '') . " /></td>
									</tr>
									<tr>
										<td width='30%'>Can Edit Others Topics<td>
										<td width='70%'><input type='checkbox' name='gceot' " . (($sql['e_topic_o'] == 't') ? 'checked="checked"' : '') . " /></td>
									</tr>
									<tr>
										<td width='30%'>Can Edit Others Posts<td>
										<td width='70%'><input type='checkbox' name='gceop' " . (($sql['e_post_o'] == 't') ? 'checked="checked"' : '') . " /></td>
									</tr>
									<tr>
										<td width='30%'>Can Move topic<td>
										<td width='70%'><input type='checkbox' name='gcmt' " . (($sql['m_topic'] == 't') ? 'checked="checked"' : '') . " /></td>
									</tr>
									<tr>
										<td width='30%'>Can Open Topic<td>
										<td width='70%'><input type='checkbox' name='gcot' " . (($sql['o_topic'] == 't') ? 'checked="checked"' : '') . " /></td>
									</tr>
									<tr>
										<td width='30%'>Can Close Topic<td>
										<td width='70%'><input type='checkbox' name='gcct' " . (($sql['c_topic'] == 't') ? 'checked="checked"' : '') . " /></td>
									</tr>
									<tr>
										<td width='30%'>Can Delete Topic<td>
										<td width='70%'><input type='checkbox' name='gcdt' " . (($sql['d_topic'] == 't') ? 'checked="checked"' : '') . " /></td>
									</tr>
									<tr>
										<td width='30%'>Can Delete Post<td>
										<td width='70%'><input type='checkbox' name='gcdp' " . (($sql['d_post'] == 't') ? 'checked="checked"' : '') . " /></td>
									</tr>
									<tr>
										<td width='30%'>Can Pin/Unpin Topics<td>
										<td width='70%'><input type='checkbox' name='gcpt' " . (($sql['topic_pin'] == 't') ? 'checked="checked"' : '') . " /></td>
									</tr>
									<tr>
										<td width='30%'>Can view offline Forum<td>
										<td width='70%'><input type='checkbox' name='offline' " . (($sql['offline'] == 't') ? 'checked="checked"' : '') . " /></td>
									</tr>
									<tr>
										<td width='30%'>Is Administrator<td>
										<td width='70%'><input type='checkbox' name='gadmin' " . (($sql['admin'] == 't') ? 'checked="checked"' : '') . " /></td>
									</tr>
									<tr>
										<td colspan='2' align='center'><input type='submit' name='submit' value='Submit' /><td>
									</tr>
								</table>
							</form>
						");
					}
					else
					{
						if(empty($_POST['gn']))
						{
							return "Sorry, you left group name field blank. Please go back and fix to continue.";
						}
						else
						{
							$gn = htmlspecialchars($_POST['gn']);
							$gs = str_replace("'","\"",$_POST['gs']);
							$gp = str_replace("'","\"",$_POST['gp']);
							$gcet = (isset($_POST['gcet'])) ? 't' : 'f';
							$gcep = (isset($_POST['gcep'])) ? 't' : 'f';
							$gceop = (isset($_POST['gceop'])) ? 't' : 'f';
							$gceot = (isset($_POST['gceot'])) ? 't' : 'f';
							$gcmt = (isset($_POST['gcmt'])) ? 't' : 'f';
							$gcot = (isset($_POST['gcot'])) ? 't' : 'f';
							$gcct = (isset($_POST['gcct'])) ? 't' : 'f';
							$gcdt = (isset($_POST['gcdt'])) ? 't' : 'f';
							$gcdp = (isset($_POST['gcdp'])) ? 't' : 'f';
							$gcpt = (isset($_POST['gcpt'])) ? 't' : 'f';
							$gadmin = (isset($_POST['gadmin'])) ? 't' : 'f';
							$offline = (isset($_POST['offline'])) ? 't' : 'f';
							if( mysql_query("UPDATE `groups` SET `offline` = '".$offline."' ,`name` = '".$gn."',`pre` = '".$gp."',`suf` = '".$gs."',`e_topic` = '".$gcet."',`e_post` = '".$gcep."',`e_topic_o` = '".$gceot."',`e_post_o` = '".$gceop."',`m_topic` = '".$gcmt."',`o_topic` = '".$gcot."',`c_topic` = '".$gcct."',`d_topic` = '".$gcdt."',`d_post` = '".$gcdp."',`topic_pin` = '".$gcpt."',`admin` = '".$gadmin."'
												WHERE `id` = '".$id."'") )
							{
								return "Group was successfully updated in database.";
							}
							else
							{
								return "There was an error updating data into the database, please contact Nevux Ability Boards Support Tech. <br /><br /> <em> " .mysql_error(). "</em>";
							}
						}
					}
				}
				else
				{
					return "<em>This group doesn't exist.</em>";
				}
			break;
			case "add":

					if(!isset($_POST['submit']))
					{
						
						return
						("
							<form method='post' action=''>
								<table width='100%'>
									<tr>
										<td width='30%'>Group Name<td>
										<td width='70%'><input type='text' name='gn' /></td>
									</tr>
									<tr>
										<td width='30%'>Prefix<td>
										<td width='70%'><input type='text' name='gp' /></td>
									</tr>
									<tr>
										<td width='30%'>Suffix<td>
										<td width='70%'><input type='text' name='gs' /></td>
									</tr>
									<tr>
										<td width='30%'>Can Edit Topic<td>
										<td width='70%'><input type='checkbox' name='gcet' checked='checked' /></td>
									</tr>
									<tr>
										<td width='30%'>Can Edit Posts<td>
										<td width='70%'><input type='checkbox' name='gcep' checked='checked' /></td>
									</tr>
									<tr>
										<td width='30%'>Can Edit Others Topics<td>
										<td width='70%'><input type='checkbox' name='gceot' /></td>
									</tr>
									<tr>
										<td width='30%'>Can Edit Others Posts<td>
										<td width='70%'><input type='checkbox' name='gceop' /></td>
									</tr>
									<tr>
										<td width='30%'>Can Move topic<td>
										<td width='70%'><input type='checkbox' name='gcmt' /></td>
									</tr>
									<tr>
										<td width='30%'>Can Open Topic<td>
										<td width='70%'><input type='checkbox' name='gcot' /></td>
									</tr>
									<tr>
										<td width='30%'>Can Close Topic<td>
										<td width='70%'><input type='checkbox' name='gcct' /></td>
									</tr>
									<tr>
										<td width='30%'>Can Delete Topic<td>
										<td width='70%'><input type='checkbox' name='gcdt' /></td>
									</tr>
									<tr>
										<td width='30%'>Can Delete Post<td>
										<td width='70%'><input type='checkbox' name='gcdp' /></td>
									</tr>
									<tr>
										<td width='30%'>Can Pin/Unpin Topics<td>
										<td width='70%'><input type='checkbox' name='gcpt' /></td>
									</tr>
									<tr>
										<td width='30%'>Is Administrator<td>
										<td width='70%'><input type='checkbox' name='gadmin' /></td>
									</tr>
									<tr>
										<td colspan='2' align='center'><input type='submit' name='submit' value='Submit' /><td>
									</tr>
								</table>
							</form>
						");
					}
					else
					{
						if(empty($_POST['gn']))
						{
							return "Sorry, you left group name field blank. Please go back and fix to continue.";
						}
						else
						{
							$gn = htmlspecialchars($_POST['gn']);
							$gs = str_replace("'","\"",$_POST['gs']);
							$gp = str_replace("'","\"",$_POST['gp']);
							$gcet = (isset($_POST['gcet'])) ? 't' : 'f';
							$gcep = (isset($_POST['gcep'])) ? 't' : 'f';
							$gceop = (isset($_POST['gceop'])) ? 't' : 'f';
							$gceot = (isset($_POST['gceot'])) ? 't' : 'f';
							$gcmt = (isset($_POST['gcmt'])) ? 't' : 'f';
							$gcot = (isset($_POST['gcot'])) ? 't' : 'f';
							$gcct = (isset($_POST['gcct'])) ? 't' : 'f';
							$gcdt = (isset($_POST['gcdt'])) ? 't' : 'f';
							$gcdp = (isset($_POST['gcdp'])) ? 't' : 'f';
							$gcpt = (isset($_POST['gcpt'])) ? 't' : 'f';
							$gadmin = (isset($_POST['gadmin'])) ? 't' : 'f';
							if( mysql_query("INSERT INTO `groups`(`name`,`pre`,`suf`,`e_topic`,`e_post`,`e_topic_o`,`e_post_o`,`m_topic`,`o_topic`,`c_topic`,`d_topic`,`d_post`,`topic_pin`,`admin`)
												VALUES('".$gn."','".$gp."','".$gs."','".$gcet."','".$gcep."','".$gceot."','".$gceop."','".$gcmt."','".$gcot."','".$gcct."','".$gcdt."','".$gcdp."','".$gcpt."','".$gadmin."')") )
							{
								return "Group was successfully added to database.";
							}
							else
							{
								return "There was an error inserting data into the database, please contact Nevux Ability Boards Support Tech. <br /><br /> <em> " .mysql_error(). "</em>";
							}
						}
					}
			break;
			default:
				$groups = mysql_query("SELECT * FROM `groups`");
				$g = 
				("
					<table width='100%'>
						<tr>
							<td width='33%'><b>Group Name</b></td>
							<td width='33%' align='center'><b>Edit</b></td>
							<td width='33%' align='center'><b>Delete</b></td>
						</tr>
				");
				while( $rows = mysql_fetch_array($groups) )
				{
					$g .= 
					("
						<tr>
							<td width='33%'>".$rows['pre'] . $rows['name'] .$rows['suf'] ."</td>
							<td width='33%' align='center'><a href='acp.php?action=editusergroup&option=edit&id=".$rows['id']."'>Edit</a></td>
							<td width='33%' align='center'>" . (($rows['default'] == 'y') ? 'Disabled' : '<a href="acp.php?action=editusergroup&option=delete&id='.$rows['id'].'">Delete</a>') . "</td>
						</tr>
					");
				}
				$g .= "</table>";
				return $g;
			break;
		}
	}
?>
<?php
/*
****************************************************************
#functions/funcs_main.php File
#Version 1.4
****************************************************************
#Copy Righted 2006-2007(http://nevuxbulletin.com) [nevuxab.info]
#Created By NevuxAB Developement Team
****************************************************************
*/

//--------------------------------
//no_permission function
//--------------------------------
	function no_permission()
	{
		pageerror("Permission Error","<i>Permissions disabled.</i>","You don't have permissions to view/use this content!");
	}
//--------------------------------
//legend function
//--------------------------------
	function legend($group,$name)
	{
		$sql = mysql_query("SELECT `pre`,`suf` FROM `groups` WHERE `id` = '".$group."'");
		$sql_ = mysql_fetch_array($sql);
		return $sql_['pre'] . $name . $sql_['suf'];
	}
//--------------------------------
//update_users function
//--------------------------------
	function update_users()
	{
		global $logged;
		$time = time();
		if( $logged['username'] )
		{
			mysql_query("UPDATE `users` SET `online` = '".$time."' WHERE `username` = '".$logged['username']."' ") or die(pageerror("User Error","There was an error in updating users online time!","There was an error updating current users online time. Contact administrator!"));
		}
		elseif(	!$logged['username'] )
		{
			$time = time();
			$ip = $_SERVER['REMOTE_ADDR'];
			$is_guest = mysql_query("SELECT `ip` FROM `guests` WHERE `ip` = '".$ip."' ");
			$is_guest = mysql_num_rows($is_guest);
			
			if($is_guest === false)
			{
				mysql_query("INSERT INTO `guests` (`ip`,`online`) VALUES('".$ip."','".$time."') ") or die("Guest Error");
			}
			else
			{
				mysql_query("UPDATE `guests` SET `online` = '".$time."' WHERE `ip` = '".$ip."' ") or die("Guest Error");
			}
		}
	}
//--------------------------------
//bbcode_format function
//--------------------------------
	function bbcode_format($var) 
	{
			$search = array(
				'/\[s\](.*?)\[\/s\]/is',        
				'/\[b\](.*?)\[\/b\]/is', 
				'/\[i\](.*?)\[\/i\]/is',                                
				'/\[u\](.*?)\[\/u\]/is',
				'/\[img\](.*?)\[\/img\]/is',
				'/\[url\](.*?)\[\/url\]/is',
				'/\[url\=(.*?)\](.*?)\[\/url\]/is',
				'/\[php\](.*?)\[\/php\]/is'
				);

			$replace = array(
				'<s>$1</s>',
				'<strong>$1</strong>',
				'<em>$1</em>',
				'<u>$1</u>',
				'<img src="$1" />',
				'<a href="$1">$1</a>',
				'<a href="$1">$2</a>',
				'<table width="100%" style="border: 1px solid #000000;background-color: #6E8C89;"><tr><td><b>PHP</b><br><br><br>$1</td></tr></table>'
				);

			$var = preg_replace ($search, $replace, $var);
			$var = preg_replace ("/javascript:/i","http://javascript*******",$var);
		return $var;
	}
//-----------------------------
//GMT Time Zones
//-----------------------------
	function timezone_stamp($time,$gmt)
	{
			//Example: timezone_stamp(time(),-6);
			if($gmt == "")
			{
				$gmt = 0;
			}
			$m = date("m",$time);
			$d = date("j",$time); 
			$y = date("Y",$time); 
			$h = date("g",$time); 
			$i = date("i",$time); 
			$s = date("s",$time);
			$h = $h-($gmt);
			$GMT_TIME = mktime($h,$i,$s,$m,$d,$y);
		return date("M d, Y g:i:s",$GMT_TIME);
	}
//--------------------------------
//attempthacking function
//--------------------------------
	function attempthacking()
	{
	
		echo ('
				<table class="border" cellspacing="0" cellpadding="0" width="100%">
				<tr>
					<td colspan="4" class="catbar"><div class="text">Banned!</div></td>
				</tr>
				<tr>
					<td width="100%" class="sub" align="center">Hacking Logged.</td>
				</tr>
				<tr>
					<td width="100%" class="rows">You are attempting to hack this BB you are now logged and banned until further notice!</td>
				</tr>
			</table>
		');
		include("footer.php");
	}
//--------------------------------
//finished function
//--------------------------------
	function finished($catbar,$titlebar,$message,$refresh=NULL)
	{
		imessage($catbar,$titlebar,$message);
		if($refresh != NULL)
		{
			echo ('<meta http-equiv="refresh" content="3;url='.$refresh.'">');
		}
	}
//--------------------------------
//page function
//--------------------------------
	function page()
	{
		global $logged;
		if($logged['username'])
		{
			$uPage = htmlspecialchars($_GET['act']);
			switch($uPage)
			{
				case "viewforum":
					$id = intval(htmlspecialchars($_GET['id']));
					$forumname = mysql_query("SELECT `title` FROM `forums` WHERE `id` = '".$id."' LIMIT 1 ");
					$forum_n = mysql_fetch_array($forumname);
					$uPage = "<b>Viewing:</b> " . htmlspecialchars($forum_n['title']);
					mysql_query("UPDATE users SET page='".$uPage."' WHERE username='".$logged['username']."' ") or die("error setting page");
				break;
				default:
					$uPage = "Forum Index";
					mysql_query("UPDATE users SET page='".$uPage."' WHERE username='".$logged['username']."' ") or die("error setting page");
				break;
			}
		}
	}
//--------------------------------
//pageerror function
//--------------------------------
	function pageerror($cat,$catdesc,$message)
	{
		global $logged;
		imessage($cat,$catdesc,$message);
		include("footer.php");
		exit(0);
	}
//--------------------------------
//Banned function
//--------------------------------
	function banned()
	{
		pageerror("You are Banned!","You are currently banned","Sorry, but you are banned from this site.");
	}
//--------------------------------
//offline function
//--------------------------------
	function check_offline()
	{
		//
	}
	function offline()
	{
		global $permissions;
		$offmsg = mysql_query("SELECT `reason`,`status` FROM `boardstatus` WHERE id='1' ");
		$offmsg = mysql_fetch_array($offmsg);
		if($offmsg['status'] == 'offline' && $_GET['act'] != 'logout' && $_GET['act'] != 'login' && $permissions['offline'] != 't')
		{
			pageerror("Forum Offline",NULL,$offmsg['reason']);
		}
	}
//--------------------------------
//group function
//--------------------------------
	function group($id)
	{
		switch($id)
		{
			case "5":
				return "Administrator";
					break;
			case "3":
				return "Moderator";
					break;
			case "1":
				return "Member";
					break;
			default:
				return "Guest";
					break;
		}
	}
//--------------------------------
//logs function
//--------------------------------
	function logs($error,$type=NULL)
	{
	
/*---------------------------------------------
//---------------------------------------------
//log types
//---------------------------------------------
1 = topic
2 = forum
3 = cpanel,apanel
4 = login,register
5 = newtopic,newreply,quickreply,all post actions(eg..delete,edit posts..)
6 = hacker
-----------------------------------------------*/
	
		if($type == NULL)
		{
			$type = "0";
		}
		$ip = $_SERVER['REMOTE_ADDR'];
		$date = date("M j,Y g:i:s a");
		$logs = mysql_query("INSERT INTO Logs(`ip`,`date`,`error`,`type`) VALUES('".$ip."','".$date."','".$error."','".$type."') ") or die(pageerror("Log Error","Error Logging data","There was an error logging information"));
	}
//--------------------------------
//do_nav_link function
//--------------------------------
	function do_nav_link()
	{
	
		$pxt = "act";//index.php?act= or index.php?action= etc...
		$act = htmlspecialchars($_GET[$pxt]);
		switch($act)
		{
			case "topicshow":
				$id = intval(htmlspecialchars($_GET['id']));
				$topic = mysql_query("SELECT `fid`,`id`,`title` FROM `topics` WHERE `id` ='".$id."' ") or die("Can't navigate linkation.(Nav1)");
				$idz = mysql_fetch_array($topic);
				$forum = mysql_query("SELECT `id`,`sid`,`title` FROM `forums` WHERE `id` = '".$idz['fid']."' ");
				$forumt = mysql_fetch_array($forum);
				if($forumt['sid'] != 0)
				{
					$SubForum = mysql_query("SELECT `title`,`id` FROM `forums` WHERE `id` = '".$forumt['sid']."'") or die("Can't navigation linkation.(Nav3)");
					$SubForum_ = mysql_fetch_array($SubForum);
					$Parent = "<a href=\"index.php?act=viewforum&id=".$SubForum_['id']."\">".$SubForum_['title']."</a> &raquo; ";
				}
				else
				{
					$Parent = "";
				}
				return "&raquo; <a href=\"index.php\">Index</a> &raquo; " . $Parent . "<a href=\"index.php?act=viewforum&id=".$forumt['id']."\">".$forumt['title']."</a> &raquo; <a href=\"index.php?act=topicshow&id=".$idz['id']."\">".$idz['title']."</a>";
			break;
			case "viewforum":
				$id = intval(htmlspecialchars($_GET['id']));
				$forum = mysql_query("SELECT `title`,`id`,`sid` FROM `forums` WHERE `id` = '".$id."' ") or die("Can't navigate linkation.(Nav2)");
				$forumz = mysql_fetch_array($forum);
				if($forumz['sid'] != 0)
				{
					$SubForum = mysql_query("SELECT `title`,`id` FROM `forums` WHERE `id` = '".$forumz['sid']."'") or die("Can't navigation linkation.(Nav3)");
					$SubForum_ = mysql_fetch_array($SubForum);
					$Parent = "<a href=\"index.php?act=viewforum&id=".$SubForum_['id']."\">".$SubForum_['title']."</a> &raquo;";
				}
				else
				{
					$Parent = "";
				}
				return "&raquo; <a href=\"index.php\">Index</a> &raquo; " . $Parent . " <a href=\"index.php?act=viewforum&id=".$id."\">".$forumz['title']."</a>";
			break;
			case "newtopic":
				$id = intval(htmlspecialchars($_GET['id']));
				$forum = mysql_query("SELECT `title`,`id`,`sid` FROM `forums` WHERE `id` = '".$id."' ") or die("Can't navigate linkation.(Nav2)");
				$forumz = mysql_fetch_array($forum);
				if($forumz['sid'] != 0)
				{
					$SubForum = mysql_query("SELECT `title`,`id` FROM `forums` WHERE `id` = '".$forumz['sid']."'") or die("Can't navigation linkation.(Nav3)");
					$SubForum_ = mysql_fetch_array($SubForum);
					$Parent = "<a href=\"index.php?act=viewforum&id=".$SubForum_['id']."\">".$SubForum_['title']."</a> &raquo;";
				}
				else
				{
					$Parent = "";
				}
				return "&raquo; <a href=\"index.php\">Index</a> &raquo; " . $Parent . " <a href=\"index.php?act=viewforum&id=".$id."\">".$forumz['title']."</a> &raquo; <a href=\"index.php?act=newtopic&id=".$id."\">New Topic</a>";
			break;
			case "newreply":
				$id = intval(htmlspecialchars($_GET['id']));
				$topic = mysql_query("SELECT `fid`,`id`,`title` FROM `topics` WHERE `id` ='".$id."' ") or die("Can't navigate linkation.(Nav1)");
				$idz = mysql_fetch_array($topic);
				$forum = mysql_query("SELECT `id`,`sid`,`title` FROM `forums` WHERE `id` = '".$idz['fid']."' ");
				$forumt = mysql_fetch_array($forum);
				if($forumt['sid'] != 0)
				{
					$SubForum = mysql_query("SELECT `title`,`id` FROM `forums` WHERE `id` = '".$forumt['sid']."'") or die("Can't navigation linkation.(Nav3)");
					$SubForum_ = mysql_fetch_array($SubForum);
					$Parent = "<a href=\"index.php?act=viewforum&id=".$SubForum_['id']."\">".$SubForum_['title']."</a> &raquo; ";
				}
				else
				{
					$Parent = "";
				}
				return "&raquo; <a href=\"index.php\">Index</a> &raquo; " . $Parent . "<a href=\"index.php?act=viewforum&id=".$forumt['id']."\">".$forumt['title']."</a> &raquo; <a href=\"index.php?act=topicshow&id=".$idz['id']."\">".$idz['title']."</a> &raquo; <a href=\"index.php?act=newreply&id=".$id."\">New Reply</a>";
				break;
			case "login":
				return ("&raquo; <a href=\"index.php\">Index</a> &raquo; <a href=\"index.php?act=login\">Login</a>");
				break;
			case "register":
				return("&raquo; <a href=\"index.php\">Index</a> &raquo; <a href=\"index.php?act=register\">Register New Account!</a>");
				break;
			case "lostpass":
				return("&raquo; <a href=\"index.php\">Index</a> &raquo; <a href=\"index.php?act=lostpass\">Password Recovery</a>");
				break;
			case "Cpanel":
				return("&raquo; <a href=\"index.php\">Index</a> &raquo; <a href=\"index.php?act=Cpanel\">Control Panel</a>");
				break;
			default:
				return "&raquo; <a href=\"index.php\">Index</a>";
				break;
		}
	}
//--------------------------------
//email_v() function
//--------------------------------
	function email_v($email)
	{
		if( preg_match("/.*@.*\..*/i",$email) )
		{
			return true;
		}	
		return false;
	}
//--------------------------------
//email_v() function
//--------------------------------
	function user_exists()
	{
		if(func_get_arg(0))
		{
			$name = htmlspecialchars(func_get_arg(0));
			$sql = mysql_query("SELECT `username` FROM `users` WHERE `username` = '".$name."'");
			if(mysql_num_rows($sql) > 0 )
			{
				return true;
			}
			return false;
		}
	}
//-----------------------------
//update_post_count()
//-----------------------------
	function update_post_count()
	{
		global $logged;
		$count = mysql_query("SELECT `post` FROM `users` WHERE `username` = '".$logged['username']."'");
		$count_ = mysql_fetch_array($count);	
		$add = intval($count_['post'])+1;
		mysql_query("UPDATE `users` SET `post` = '".$add."' WHERE `username` = '".$logged['username']."'");
	}
//---------------------------
//get_group_perm()
//---------------------------
	function get_group_perm($level)
	{
		global $logged;
		if($level != NULL OR $level != 0)
		{
			$group = mysql_query("SELECT * FROM `groups` WHERE `id` = '".$level."'");
			$array = mysql_fetch_array($group);
			return $array;
		}
		else
		{
			$group = array(
				'name' => 'Guest',
				'pre' => '<em>',
				'suf' => '</em>',
				'e_topic' => 'f',
				'e_post' => 'f',
				'e_topic_o' => 'f',
				'e_post_o' => 'f',
				'o_topic' => 'f',
				'c_topic' => 'f',
				'd_topic' => 'f',
				'd_post' => 'f',
				'topic_pin' => 'f',
				'admin' => 'f'
			);
			return $group;
		}
	}
//------------------------
//get_user_groups()
//------------------------
	function get_user_groups($grp)
	{
		$groups = mysql_query("SELECT `id`,`name` FROM `groups`");
		$g = "";
		if( mysql_num_rows($groups) )
		{
			while( $group = mysql_fetch_array($groups) )
			{
				if($group['id'] == $grp)
				{
					$g .= "<option selected='selected' value='" . $group['id'] . "'>" . $group['name'] . "</option>";
				}
				else
				{
					$g .= "<option value='" . $group['id'] . "'>" . $group['name'] . "</option>";
				}
			}	
		}
		else
		{
			return "<option value='0'>None</option>";
		}
		return $g;
	}
//--------------------------
//parse_banned_ips()
//-------------------------
	function parse_banned_ips($ip,$type)
	{
		switch($type)
		{
			case "u":
				$ips = split("\n",$ip);
				$i = "";
				foreach($ips as $ipa)
				{
					$i .= htmlspecialchars($ipa) . ",";
				}
				return $i;
			break;
			case "v":
				$i = "";
				$ips = explode(",",$ip);
				foreach($ips as $ipa)
				{
					$i .= $ipa . "\n";
				}
				return $i;
			break;
		}
	}
	function check_if_banned_ip()
	{
		global $logged;
		
		$ips = mysql_query("SELECT `banned` FROM `boardstatus` WHERE `id` = '1'");
		$ip = mysql_fetch_array($ips);
		$ipa = explode(",",$ip['banned']);
		foreach($ipa as $ipz)
		{
			if($_SERVER['REMOTE_ADDR'] == $ipz)
			{
				return true;
			}
		}
	}
	function check_if_banned_user()
	{
		global $logged;
		$user = mysql_query("SELECT `banned` FROM `users` WHERE `username` = '".$logged['username']."'");
		$user_ = mysql_fetch_array($user);
		if($user_['banned'] == 1)
		{
			return true;
		}
	}
	function check_forum_lock($tid)
	{
		$sql = mysql_query("SELECT `locked` FROM `forums` WHERE `id` = '".$tid."'");
		$sql_ = mysql_fetch_array($sql);
		if($sql_['locked'] == 't')
		{
			return true;
		}
	}
	function topic_parent_($id)
	{
		$sql = mysql_query("SELECT `fid` FROM `topics` WHERE `id` = '".$id."'");
		$sql_ = mysql_fetch_array($sql);
		return $sql_['fid'];
	}
	function topic_pagination($id,$total_limit,$type=1)
	{
		if($type == 1)
		{
			$rpz = mysql_query("SELECT * FROM `replies` WHERE `tid` = '".$id."'");
			$rzz = mysql_num_rows($rpz);
			$total_pages = ceil($rzz / $total_limit);
			echo "Page: ";
			if($total_pages > 0)
			{
				for($i = 1; $i <= $total_pages; $i++)
				{
					if(($page) == $i)
					{
						echo "<b>[".$i."]</b> ";
					}
					else 
					{
						echo "[<a href=\"index.php?act=topicshow&amp;id=".$id."&amp;p=".$i."\">".$i."</a>] ";
					}
				}
			}
			else
			{
				echo "<b>[1]</b> ";
			}
		}
		else
		{
			$rpz = mysql_query("SELECT * FROM `topics` WHERE `fid` = '".$id."'");
			$rzz = mysql_num_rows($rpz);
			$total_pages = ceil($rzz / $total_limit);
			echo "Page: ";
			if($total_pages > 0)
			{
				for($i = 1; $i <= $total_pages; $i++)
				{
					if(($page) == $i)
					{
						echo "<b>[".$i."]</b> ";
					}
					else 
					{
						echo "[<a href=\"index.php?act=viewforum&amp;id=".$id."&amp;p=".$i."\">".$i."</a>] ";
					}
				}
			}
			else
			{
				echo "<b>[1]</b> ";
			}
		}
	}
//Global Message Function to be used anywhere.
	function imessage($a="Unknown Clause",$b="",$c="Unknown Clause Occured.",$extra = false,$return = false)
	{
		global $logged;
		$Temp = new Template;
		$Temp->dir = $logged['dskin'];
		$Temp->file = 'message.tpl';
		$Temp->tp();
		$Temp->tr(array("TITLE"=>$a,"STITLE"=>$b,"BODY" => $c));
		if($return == true)
		{
			if($extra != false)
			{
				return $Temp->to() . '<meta http-equiv="refresh" content="3;url='.$extra.'">';
			}
			else
			{
				return $Temp->to();
			}
		}
		else
		{
			$Temp->_print();
		}
	}
	function getgroupname($group)
	{
		$d = mysql_query("SELECT `name` FROM `groups` WHERE `id` = '" . $group . "'");
		$dd = mysql_fetch_array($d);
		return $dd['name'];
	}
	function getid($name)
	{
		if($name == "")
			return "";
		$sql = mysql_query("SELECT `id` FROM `users` WHERE `username` = '" . $name . "' LIMIT 1;");
		$s = mysql_fetch_array($sql);
		return $s['id'];
	}
	function topicName($id)
	{
		$id = intval(htmlspecialchars($id));
		$s = mysql_query("SELECT `title` FROM `topics` WHERE `id` = '".$id."'");
		$ss = mysql_fetch_array($s);
		return $ss['title'];
	}
	function getFirstPost($tid)
	{
		$sql = @mysql_query("SELECT `post` FROM `replies` WHERE `tid` = '".$tid."' LIMIT 1");
		$s = @mysql_fetch_array($sql);
		return $s['post'];
	}
	function getFP($fid,$type)
	{
		global $logged;
		$sql = mysql_query("SELECT `permissions` FROM `forums` WHERE `id` = '".$fid."'");
		$ss = mysql_fetch_array($sql);
		$perms = unserialize($ss['permissions']);
		switch($type)
		{
			case 0:
				if($perms['view'][$logged['level']] == 't')
				{
					return true;
				}
				return false;
			break;
			case 1:
				if($perms['read'][$logged['level']] == 't')
				{
					return true;
				}
				return false;
			break;
			case 2:
				if($perms['reply'][$logged['level']] == 't')
				{
					return true;
				}
				return false;
			break;
			case 3:
				if($perms['topic'][$logged['level']] == 't')
				{
					return true;
				}
				return false;
			break;
		}
	}
//gets a list of <option> groups.
	function ggOptions()
	{
		$groups = "";
		$sql = @mysql_query("SELECT `id`,`name` FROM `groups`");
		if(mysql_num_rows($sql) > 0)
		{
			while($r = mysql_fetch_array($sql))
			{
				extract($r);
				$groups .= "<option value='".$id."'>".$name."</option>";
			}
			return $groups;
		}
	}
?>
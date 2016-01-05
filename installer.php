<?php
/*
****************************************************************
#installer.php File
#Version 1.4
****************************************************************
#Copy Righted 2006-2007(http://nevuxbulletin.com) [nevuxab.info]
#Created By NevuxAB Developement Team
****************************************************************
*/
	if($_GET['setup'] == "database" || $_GET['setup'] == "admin")
	{
		include("config.php");
	}
	else
	{
		include("./classes/template.php");
	}
	$NAB_VERSION = "1.4.0";
	$NAB_STAGE = 0;
	include("./install_temp.php");
	theader();
	switch($_GET['setup'])
	{
		case "config":
			start_config();
		break;	
		case "database":
			start_db();
		break;
		case "admin":
			start_admin();
		break;
		default:
			echo ("
				Welcome new users to the Nevux Ability Boards software Installation script.  Before You can go any futher please make sure that your config.php file is chmoded to 0777.<br /><br /><br />
				Config Writable: ". ((config_chmod()) ? '<font color="green">True</font>' : '<font color="red">False</font>') . " <br /><br />
				" . ((!config_chmod()) ? 'To Proceed Please chmod config.php to 0777.' : '<a href="installer.php?setup=config">Proceed to setup config</a>'));
		break;
	}
	
	function config_chmod()
	{
		if( is_writable("config.php") )
		{
			return true;
		}
		return false;
	}
	//starts to building of the configuration file
	function start_config()
	{
		if( !isset($_POST['start']) )
		{
			echo
			("
			<form method='post' action=''>
				<table width='100%' class='forum' cellspacing='0' cellpadding='0'>
					<tr>
						<td class='category' colspan='2'><div class='cat_title'>SQL Fields</div></td>
					</tr>
					<tr>
						<td class='small_title' colspan='2'></td>
					</tr>
					<tr>
						<td class='rows' valign='top' width='30%'>SQL Host</td>
						<td class='rows' valign='top' width='70%'><input type='text' name='host' value='localhost' /></td>
					</tr>
					<tr>
						<td class='rows' valign='top' width='30%'>SQL Username</td>
						<td class='rows' valign='top' width='70%'><input type='text' name='user' /></td>
					</tr>
					<tr>
						<td class='rows' valign='top' width='30%'>SQL Password</td>
						<td class='rows' valign='top' width='70%'><input type='password' name='pass' /></td>
					</tr>
					<tr>
						<td class='rows' valign='top' width='30%'>SQL Databasename</td>
						<td class='rows' valign='top' width='70%'><input type='text' name='db' /></td>
					</tr>
					<tr>
						<td class='rows' colspan='2' align='center'><input type='submit' name='start' value='Create Config' /></td>
					</tr>
				</table>
			</form>
			");
		}
		else
		{
			if( @mysql_connect( $_POST['host'] , $_POST['user'], $_POST['pass'] ) )
			{
				if( mysql_select_db( $_POST['db'] ) )
				{
					if( file_exists ( "config.php" ) )
					{
						if( is_writable( "config.php") )
						{
							$contents = config_php( $_POST['host'] , $_POST['pass'] , $_POST['user'] , $_POST['db'] );
							$file_name = "config.php";
							$file_handle = fopen($file_name,"w");
							if( fwrite($file_handle,$contents) )
							{
								echo complete("Configuration was completed. Proceed to create the database: <a href='installer.php?setup=database'>Step 2</a>");
							}
							else
							{
								echo error("Sorry, but content couldn't be written to the configuration file, make sure its chmoded and make sure the filesystem functions for PHP is enable else this might not work.");
							}
							fclose($file_handle);
						}
						else
						{
							echo error("Sorry, but your config.php file isn't writable please go chmod config.php to 0777, and refresh page or restart installation.");
						}
					}
					else
					{
						echo error("Sorry, bu the config file doesn't exist, please go create a config.php file and upload it and make sure its chmoded to 0777.");
					}
				}
				else
				{
					echo error("Your sql username connected to the database correctly, but when trying to select Database, it doesn't connect correctly.");
				}
			}
			else
			{
				echo error("Sorry, your sql username,password,host doesn't connect correctly, please fix this in order to install config.");
			}
		}
	}
	function start_db()
	{
		$SQL = array();

		$SQL[0] = "CREATE TABLE `boardstatus` (
  `id` int(100) NOT NULL default '0',
  `status` varchar(255) NOT NULL default 'online',
  `reason` varchar(255) NOT NULL default 'Sorry,But we are currently offline please check back soon.',
  `banner` varchar(255) NOT NULL default '',
  `registration` varchar(255) NOT NULL default 'on',
  `boardname` varchar(255) NOT NULL default 'My Forum Community',
  `announcestatus` varchar(255) NOT NULL default 'on',
  `announcement` longtext NOT NULL,
  `url` varchar(255) NOT NULL default '',
  `coppa` varchar(255) NOT NULL default 'y',
  `banned` varchar(255) NOT NULL default '',
  `postpertopic` int(255) NOT NULL default '7',
  `topicsperforum` int(255) NOT NULL default '7',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM;";
		$SQL[1] = "CREATE TABLE `categories` (
  `id` int(11) NOT NULL auto_increment,
  `title` varchar(24) default NULL,
  `description` varchar(40) default NULL,
  `order` int(255) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM;";
		$SQL[2] = "CREATE TABLE `forums` (
  `id` int(4) NOT NULL auto_increment,
  `cid` int(4) NOT NULL default '0',
  `sid` int(10) NOT NULL default '0',
  `title` varchar(255) NOT NULL default '',
  `description` longtext NOT NULL,
  `last_post_title` varchar(255) NOT NULL default '',
  `last_post_username` varchar(32) NOT NULL default '',
  `topics` int(9) NOT NULL default '0',
  `replies` int(9) NOT NULL default '0',
  `quick_r` varchar(255) NOT NULL default 'on',
  `lastvisited` varchar(255) NOT NULL default '',
  `locked` char(1) NOT NULL default 'f',
  `permissions` longtext NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM;";
		$SQL[3] = "CREATE TABLE `guests` (
		  `id` tinyint(4) NOT NULL auto_increment,
		  `ip` varchar(225) NOT NULL default '',
		  `online` varchar(225) NOT NULL default '',
		  PRIMARY KEY  (`id`)
		) ENGINE=MyISAM;";
		$SQL[4] = "CREATE TABLE `ip` (
		  `id` int(255) NOT NULL auto_increment,
		  `ip` varchar(255) NOT NULL default '',
		  `years` varchar(255) NOT NULL default '',
		  `days` varchar(255) NOT NULL default '',
		  `hours` varchar(255) NOT NULL default '',
		  `minutes` varchar(255) NOT NULL default '',
		  `seconds` varchar(255) NOT NULL default '',
		  PRIMARY KEY  (`id`)
		) ENGINE=MyISAM;";
		$SQL[5] = "CREATE TABLE `Logs` (
		  `log` int(255) NOT NULL auto_increment,
		  `ip` varchar(255) NOT NULL default '',
		  `date` varchar(255) NOT NULL default '',
		  `error` varchar(255) NOT NULL default '',
		  `type` varchar(255) NOT NULL default '',
		  PRIMARY KEY  (`log`)
		) ENGINE=MyISAM;";
		$SQL[6] = "CREATE TABLE `replies` (
  `id` int(9) NOT NULL auto_increment,
  `tid` int(9) NOT NULL default '0',
  `post` longtext NOT NULL,
  `username` varchar(32) NOT NULL default '',
  `date` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM;";
		$SQL[7] = "CREATE TABLE `topics` (
  `id` int(9) NOT NULL auto_increment,
  `timestamp` int(20) NOT NULL default '0',
  `fid` int(4) NOT NULL default '0',
  `title` varchar(255) NOT NULL default '',
  `post` longtext NOT NULL,
  `username` varchar(32) NOT NULL default '',
  `last_post_username` varchar(32) NOT NULL default '',
  `replies` int(9) NOT NULL default '0',
  `views` int(9) NOT NULL default '0',
  `sticky` int(10) NOT NULL default '1',
  `closed` int(10) NOT NULL default '1',
  `description` varchar(30) NOT NULL default '',
  `date` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM;";
		$SQL[8] = "CREATE TABLE `users` (
  `id` tinyint(4) NOT NULL auto_increment,
  `username` varchar(20) NOT NULL default '',
  `password` varchar(50) NOT NULL default '',
  `salt` varchar(255) NOT NULL default '',
  `email` varchar(35) NOT NULL default '',
  `banned` int(11) NOT NULL default '0',
  `level` int(24) NOT NULL default '2',
  `msn` varchar(24) NOT NULL default '',
  `icq` varchar(24) NOT NULL default '',
  `aim` varchar(24) NOT NULL default '',
  `avatar` longtext NOT NULL,
  `online` varchar(12) default NULL,
  `post` int(100) NOT NULL default '0',
  `signature` longtext NOT NULL,
  `ip` varchar(255) NOT NULL default '0',
  `joined` varchar(255) NOT NULL default '',
  `group` varchar(255) NOT NULL default 'Member Group',
  `dskin` varchar(255) NOT NULL default 'pro',
  `page` varchar(255) NOT NULL default 'Index',
  `passval` varchar(255) NOT NULL default '',
  `notepad` varchar(255) NOT NULL default 'This is your notepad to start personal reminder info.',
  `timezone` varchar(255) NOT NULL default '-7',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM;";
		$SQL[9] = "CREATE TABLE `groups` (
  `id` int(255) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `pre` varchar(255) NOT NULL default '',
  `suf` varchar(255) NOT NULL default '',
  `default` varchar(255) NOT NULL default 'n',
  `e_topic` varchar(255) NOT NULL default 't',
  `e_post` varchar(255) NOT NULL default 't',
  `e_topic_o` varchar(255) NOT NULL default 'f',
  `e_post_o` varchar(255) NOT NULL default 'f',
  `m_topic` varchar(255) NOT NULL default 'f',
  `o_topic` varchar(255) NOT NULL default 'f',
  `c_topic` varchar(255) NOT NULL default 'f',
  `d_topic` varchar(255) NOT NULL default 'f',
  `d_post` varchar(255) NOT NULL default 'f',
  `topic_pin` varchar(255) NOT NULL default 'f',
  `admin` varchar(255) NOT NULL default 'f',
  `offline` char(1) NOT NULL default 'f',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM;";
		$SQL[10] = "CREATE TABLE `boardrules` (
		  `id` int(10) NOT NULL auto_increment,
		  `coppa` longtext NOT NULL,
		  PRIMARY KEY  (`id`)
		) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;";
		$SQL[11] = "INSERT INTO `categories` (`id`, `title`, `description`, `order`) VALUES (1, 'Discussion Board', '', 0);";
		$SQL[12] = "INSERT INTO `forums` (`id`, `cid`, `sid`, `title`, `description`, `last_post_title`, `last_post_username`, `topics`, `replies`, `quick_r`, `lastvisited`, `locked`, `permissions`) VALUES (1, 1, 0, 'General Discussion', '', '', '', 0, 0, 'on', 'a:10:{i:1;i:10;s:0:\"\";i:9;i:2;i:8;i:3;i:8;i:4;i:8;i:5;i:8;i:6;i:9;i:7;i:8;i:8;i:8;i:9;i:9;}', 'f', 'a:4:{s:4:\"view\";a:3:{i:0;s:1:\"t\";i:1;s:1:\"t\";i:2;s:1:\"t\";}s:4:\"read\";a:3:{i:0;s:1:\"t\";i:1;s:1:\"t\";i:2;s:1:\"t\";}s:5:\"reply\";a:2:{i:1;s:1:\"t\";i:2;s:1:\"t\";}s:5:\"topic\";a:2:{i:1;s:1:\"t\";i:2;s:1:\"t\";}}');";
		$SQL[13] = "INSERT INTO `boardrules` VALUES (1, 'By Joining Our Site You Agree that you are not going to post anything illegal or any obscenced images or threats towards Nevux Ability Boards. Just By Signing up you agree that you wont spam,harm,threat any one in any way.\r\n-Nevux Ability Boards.');";
		$SQL[14] = "INSERT INTO `groups` (`id`, `name`, `pre`, `suf`, `default`, `e_topic`, `e_post`, `e_topic_o`, `e_post_o`, `m_topic`, `o_topic`, `c_topic`, `d_topic`, `d_post`, `topic_pin`, `admin`, `offline`) VALUES (1, 'Admin', '<b><font color=\"red\">', '</font></b>', 'y', 't', 't', 't', 't', 't', 't', 't', 't', 't', 't', 't', 't');";
		$SQL[15] = "INSERT INTO `groups` (`id`, `name`, `pre`, `suf`, `default`, `e_topic`, `e_post`, `e_topic_o`, `e_post_o`, `m_topic`, `o_topic`, `c_topic`, `d_topic`, `d_post`, `topic_pin`, `admin`, `offline`) VALUES (2, 'Members', '', '', 'y', 't', 't', 'f', 'f', 'f', 'f', 'f', 'f', 'f', 'f', 'f', 'f');";
		$SQL[16] = "INSERT INTO `boardstatus` (`id`, `status`, `reason`, `banner`, `registration`, `boardname`, `announcestatus`, `announcement`, `url`, `coppa`, `banned`, `postpertopic`, `topicsperforum`) VALUES (1, 'online', 'We will be offline for a minute.', '', 'on', 'Your Forum Name', 'off', 'Welcome to My Forum Name Thank you for joining.', 'http://yourwebsite.com/', 'y', '\r,\r,\r,\r,\r,\r,\r,\r,\r,,', 7, '7');";
		$debug = 0;
		$msg = "";
		foreach($SQL as $insert)
		{
			if(! mysql_query($insert) )
			{
				$debug = $debug + 1;
				$msg .= mysql_error() . "<br />";
			}	
		}	
		if( $debug > 0 )
		{
			echo
			("
				<table width='100%' class='forum' cellspacing='0' cellpadding='0'>
					<tr>
						<td class='category'><div class='cat_title'>Adding SQL Tables</div></td>
					</tr>
					<tr>
						<td class='small_title'></td>
					</tr>
					<tr>
						<td class='rows' valign='top' width='100%'>There was an error insertin data into database. " .$msg. "</td>
				</table>
			");
		}
		else
		{
			echo
			("
				<table width='100%' class='forum' cellspacing='0' cellpadding='0'>
					<tr>
						<td class='category'><div class='cat_title'>Adding SQL Tables</div></td>
					</tr>
					<tr>
						<td class='small_title'></td>
					</tr>
					<tr>
						<td class='rows' valign='top' width='100%'>SQL Was Inserted into database successfully! Create admin account <a href='installer.php?setup=admin'>Here</a></td>
					</tr>
					</table>
			");
		}
	}
	function start_admin()
	{
	
		if(!isset($_POST['submit']))
		{
			echo
			("
			<form method='post' action=''>
				<table width='100%' class='forum' cellspacing='0' cellpadding='0'>
					<tr>
						<td class='category' colspan='2'><div class='cat_title'>Create Administrator</div></td>
					</tr>
					<tr>
						<td class='small_title' colspan='2'></td>
					</tr>
					<tr>
						<td class='rows' valign='top' width='30%'>Admin name</td>
						<td class='rows' valign='top' width='70%'><input type='text' name='username' /></td>
					</tr>
					<tr>
						<td class='rows' valign='top' width='30%'>Password</td>
						<td class='rows' valign='top' width='70%'><input type='password' name='pass' /></td>
					</tr>
					<tr>
						<td class='rows' valign='top' width='30%'>Password Again</td>
						<td class='rows' valign='top' width='70%'><input type='password' name='pass2' /></td>
					</tr>
					<tr>
						<td class='rows' valign='top' width='30%'>Email</td>
						<td class='rows' valign='top' width='70%'><input type='text' name='Email' /></td>
					</tr>
					<tr>
						<td class='rows' valign='top' width='30%'>Website[Must be: <em>http://ursite.com/</em>]</td>
						<td class='rows' valign='top' width='70%'><input type='text' name='site' /></td>
					</tr>
					<tr>
						<td class='rows' valign='top' colspan='2' align='center'><input type='submit' name='submit' value='Create Admin Account' /></td>
					</tr>
				</table>
			</form>
			");
		}
		else
		{
			if(!empty($_POST['username']) AND !empty($_POST['pass']) AND !empty($_POST['Email']) AND !empty($_POST['site']) )
			{
				$email = htmlspecialchars($_POST['Email']);
				$username = htmlspecialchars($_POST['username']);
				$WEBSITE = htmlspecialchars($_POST['site']);
				if($_POST['pass'] != $_POST['pass2'])
				{
					echo error("Sorry, these passwords do not match, please go back to fix this problem.");
				}
				else
				{
					$joined = time();
					$level = 1;
					$salt = substr(md5(rand(0,6000)),0,10);
					$password = htmlspecialchars(md5($salt.$_POST['pass']));
					if( mysql_query("INSERT INTO `users` (`username`,`email`,`password`,`salt`,`joined`,`level`) VALUES('".$username."','".$email."','".$password."','".$salt."','".$joined."','".$level."') ") )
					{
						echo complete("Administrator account was created, you can now visit your site located: <a href='index.php'>Here</a>; Make sure you delete installer.php and install_temp.php from database else you can be at complete danger");
						session_destroy();
					}
					else
					{
						echo error("Sorry, there was a problem inserting administrator into database, contact Nevux Ability Boards Support tech.");
					}
				}
			}
			else
			{
				echo error("Sorry, you left a field blank please go back to finish completion of forum installation.");
			}
		}
	
	}
	

	function error($msg)
	{
			return 
			("
				<table width='100%' class='forum' cellspacing='0' cellpadding='0'>
					<tr>
						<td class='category'><div class='cat_title'>AN ERROR HAS OCCURED</div></td>
					</tr>
					<tr>
						<td class='small_title'></td>
					</tr>
					<tr>
						<td class='rows' valign='top' width='100%'>".$msg."</td>
				</table>
			");
	}
	function complete($msg)
	{
			return 
			("
				<table width='100%' class='forum' cellspacing='0' cellpadding='0'>
					<tr>
						<td class='category'><div class='cat_title'>Success</div></td>
					</tr>
					<tr>
						<td class='small_title'></td>
					</tr>
					<tr>
						<td class='rows' valign='top' width='100%'>".$msg."</td>
				</table>
			");
	}
	function config_php($host,$pass,$user,$db)
	{
return "<?php
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

\$_CONFIG = array();
\$_CONFIG['HOST'] = \"".$host."\";
\$_CONFIG['SQL_USER'] = \"".$user."\";
\$_CONFIG['SQL_PASS'] = \"".$pass."\";
\$_CONFIG['SQL_DB']	= \"".$db."\";
\$_CONFIG['VERSION'] = \"1.4.3\";
\$_CONFIG['STAGE'] = 2;

if ( !@mysql_connect(\$_CONFIG['HOST'],\$_CONFIG['SQL_USER'],\$_CONFIG['SQL_PASS']) )
{
\t	die(\"Sorry, we couldn't connect to the database, please check your configuration. [0]\");
}

if( !@mysql_select_db(\$_CONFIG['SQL_DB']) )
{
\t	die(\"Sorry, we couldn't connect to the database, please check your configuration. [1]\");
}

if( isset( \$_SESSION['uid'] ) )
{
\t	\$session_id = htmlspecialchars( \$_SESSION['uid'] );
\t	\$session_pass = htmlspecialchars( \$_SESSION['upass'] );
\t	\$log = mysql_query(\"SELECT * FROM `users` WHERE `id` = '\".\$session_id.\"' AND `password` = '\".\$session_pass.\"' \");
\t	\$logged = mysql_fetch_array(\$log);
\t	\$permissions_ = mysql_query(\"SELECT * FROM `groups` WHERE `id` = '\".\$logged['level'].\"'\");
\t	\$permissions = mysql_fetch_array(\$permissions_);
}
elseif( !isset( \$_SESSION['uid']) )
{
\t	\$permissions = array();
\t	\$logged = array();
\t	\$logged['level'] = 0;
\t	\$logged['dskin'] = \"pro\";
}\n
if(!file_exists(\"./installer.php\"))
{
	include(\"./functions/funcs_main.php\");
}
include(\"./classes/template.php\");
\$NAV = (\$logged['username']) ? 'Welcome back <strong>' .\$logged['username']. '</strong> (<a href=\"index.php?act=logout\">Logout</a>), <a href=\"index.php?act=Cpanel\">Control Panel</a> ' . ((\$permissions['admin'] == 't') ? ' | <a href=\"acp.php\">Admin Panel</a>' : '') . \"\" : \"Welcome back Guests, <a href='index.php?act=login'>Login</a> Or <a href='index.php?act=register'>Register</a>\";
?>";
}
	
tfooter();
?>
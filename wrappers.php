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
	include("config.php");
	switch($_GET['act'])
	{
		case "online":
			function users_online()
			{
				$all_users = "";
				echo ("<b>Users Online:</b><br />");
				$time = time()-300;
				$users = mysql_query("SELECT `level`,`username` FROM users WHERE online >= '$time' ORDER BY online DESC");
				$USZ = mysql_num_rows($users);
				if($USZ == 0)
				{
					echo "There hasn't been anyone online in the past 5 minutes.";
				}
				else
				{
					while($uzas = mysql_fetch_array( $users ) )
					{
						$all_users .= "<a style='text-decoration:none;' href='?act=profile&amp;profile;id=" . getid($uzas['username']) . "' title=\"View " . $uzas['username'] . "'s Profile\">" .legend($uzas['level'],$uzas['username'])."</a>,";
					}
				}
				echo substr($all_users,0,strlen($all_users)-1);
			}
			users_online();
		break;
		case "boardlegend":
			$groups = mysql_query("SELECT * FROM `groups`");
			$g = "";
			while($row = mysql_fetch_array($groups))
			{
				$g .= $row['pre'] . $row['name'] . $row['suf'] . ", ";
			}
			echo "<b>Board Legend</b><br />" . substr($g,0,strlen($g)-2);
		break;
		case "boardstats":
			echo boardstatistics();
		break;
	}
	function boardstatistics()
	{
		$bs = mysql_query("SELECT * FROM `users`");
		$bsU = mysql_num_rows($bs);
		$bs2 = mysql_query("SELECT * FROM `replies`");
		$bsR = mysql_num_rows($bs2);
		$bs3 = mysql_query("SELECT * FROM `topics`");
		$bsT = mysql_num_rows($bs3);
		$bs4 = mysql_query("SELECT `username` , `joined` FROM `users` ORDER BY `id` DESC LIMIT 1 ");
		$bs4 = mysql_fetch_array($bs4);
		$wrappers = "We have a total of <span class=\"wrapper\">".$bsU."</span> member(s) that made a total of <span class=\"wrapper\">".$bsR."</span> posts & <span class=\"wrapper\">".$bsT."</span> topics.<br />Newest user that joined was <span class=\"wrapper\">".$bs4['username']."</span>";
		return $wrappers;
	}
?>
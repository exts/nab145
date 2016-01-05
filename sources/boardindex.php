<?php
/*
****************************************************************
#sources/boardindex.php File
#Version 1.4
****************************************************************
#Copy Righted 2006-2007(http://nevuxbulletin.com) [nevuxab.info]
#Created By NevuxAB Developement Team
****************************************************************
*/
	if(!defined("NABPROOF"))
	{
		die("Hacking attempt");
	}
	function RUN_Index()
	{
		global $logged;
		//lets get the categories from DB ordering then by there custom order 0 is default
		$nc = mysql_query("SELECT `id`,`title` FROM `categories` ORDER By categories.order ") or die(mysql_error());
		//do a loop threw ALL categories and show the selected forums and forum information
			while($c = mysql_fetch_array( $nc ) )
			{
				$forum_count = 0;
				$forums_html = "";
			//show the categories header
				$Temp = new Template;
				$Temp->dir = $logged['dskin'];
				$Temp->file = "category_header.tpl";
				$Temp->tp();
				$Temp->tr(array('CAT_NAME' => $c['title']));
				$forums_html .= $Temp->html;
			
			//lets get the forums and its information from DB
				$nc2 = mysql_query("SELECT `locked`,`description`,`id`, `title`,`replies`,`topics`,`lastvisited` FROM `forums` WHERE `cid` = '".$c['id']."'") or die(mysql_error());
			
			//lets loop threw all forums and get its individual forum information
				while( $f = mysql_fetch_array($nc2) )
				{
			//select the total replies for each forum
					$total_topics_f = mysql_query("SELECT topics.id,replies.tid FROM `topics`,`replies` WHERE `fid` = '".$f['id']."' AND topics.id = replies.tid") or die(mysql_error());
					$total_topics_f_2 = mysql_num_rows($total_topics_f);
			
			//select topic data from the database from these forums
					$nlastpost = mysql_query("SELECT `id`,`title`,`username` FROM `topics` WHERE `fid` ='".$f['id']."' ORDER BY timestamp DESC") or die(mysql_error());
					$nlastpost1 = mysql_fetch_array($nlastpost);
					$topicid = $nlastpost1['id'];
					$topicuser = $nlastpost1['username'];
					$topicnumber = mysql_num_rows($nlastpost);
			//get latest replies from current forum
					$allreplies = mysql_query("SELECT * FROM `replies` WHERE `tid` = '".$topicid."' ORDER BY `id` DESC ") or die(mysql_error());
					$all_replies = mysql_fetch_array($allreplies);
					
					//check if forum is locked 
					if($f['locked'] == 't')
					{
						$no_new_post = "<img src='styles/".$logged['dskin']."/flocked.png' alt='Locked' />";
					}
					else
					{
						if($f['lastvisited'] == "")
						{
							$no_new_post = ($total_topics_f_2 != 0) ? "<img src='styles/".$logged['dskin']."/New.png' alt='New' />" : "<img src='styles/".$logged['dskin']."/No%20New.png' alt='No-New' />";
						}
						else
						{
							$lID = $logged['id'];
							$last_vdata = unserialize($f['lastvisited']);
							$no_new_post = ($last_vdata[$lID] < $total_topics_f_2) ? "<img src='styles/".$logged['dskin']."/New.png' alt='New' />" : "<img src='styles/".$logged['dskin']."/No%20New.png' alt='No-New' />";
						}
					}
					//check if there we're any replies
						if( empty( $nlastpost1['title'] ) )
							$IN = "No Topics Here.";
						else
							$IN = "<a href=\"index.php?act=topicshow&id=".$nlastpost1['id']."\">".$nlastpost1['title']."</a>";
							
						if( $all_replies['username'] == "" )
						{
							$BY = $topicuser;
						}
						else
						{
							$BY = $all_replies['username'];
						}
					$subforums = "";
					$subForum = mysql_query("SELECT * FROM `forums` WHERE `sid` = '".$f['id']."'");
					if(mysql_num_rows($subForum) > 0)
					{
						while($subForumz = mysql_fetch_array($subForum))
						{
							$subforums .= "<a href='index.php?act=viewforum&id=".$subForumz['id']."'>".$subForumz['title']."</a>, ";
						}
					}	
					$subforums = ($subforums != "") ? "<b>Children</b>: " . substr($subforums,0,strlen($subforums)-2) : '';
					//show the forums
					if(getFP($f['id'],0))
					{
						$Temp = new Template;
						$Temp->dir = $logged['dskin'];
						$Temp->file = "idxforum.tpl";
						$Temp->tp();
						$Temp->tr(array(
							'NEWPOST' => $no_new_post,
							'FORUM_ID' => $f['id'],
							'FORUM_NAME' => $f['title'],
							'FORUM_DESC' => $f['description'],
							'TOPICS' => $topicnumber,
							'REPLIES' => $total_topics_f_2,
							'LASTPOSTER' => $BY,
							'UID' => getid($BY),
							'TOPIC_LINK' => $IN,
							'SUBFORUMS' => $subforums
						));
						$forums_html .= $Temp->html;
						$forum_count = $forum_count + 1;
					}
				}
				$forums_html .= "</table>";
				if($forum_count > 0)
				{
					echo $forums_html;
				}
			}

	}//end function
	
	function RUN_Wrappers()
	{
		global $logged;
		$Temp = new Template;
		$Temp->dir = $logged['dskin'];
		$Temp->file = "boardstats.tpl";
		$Temp->tp();
		$Temp->tr(array("WRAPPER" => users_online()));
		echo $Temp->html;
	}
	function users_online()
	{	
			$all_users = "";
			$all_users .= "<b>Users Online:</b><br />";
			$time = time()-300;
			$users = mysql_query("SELECT `level`,`username` FROM `users` WHERE `online` >= '".$time."' ORDER BY `online` DESC");
			$USZ = mysql_num_rows($users);
			if($USZ == 0)
			{
				return $all_users .= "There hasn't been anyone online in the past 5 minutes.";
			}
			else
			{
				while( $uzas = mysql_fetch_array( $users ) )
				{
					$all_users .= "<a style='text-decoration:none;' href='?act=profile&amp;profile;id=" . getid($uzas['username']) . "' title=\"View " . $uzas['username'] . "'s Profile\">" .legend($uzas['level'],$uzas['username'])."</a>,";
				}
			}
		return substr($all_users,0,strlen($all_users)-1);
	}
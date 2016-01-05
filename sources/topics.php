<?php
/*
****************************************************************
#sources/topics.php File
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
//----------------------------
//RUN_topic() function
//----------------------------
		function RUN_Topic()
		{
			global $logged,$permissions;
			if( is_numeric($_GET['id']) AND !empty($_GET['id']) )
			{
				$id = intval(htmlspecialchars($_GET['id']));
			}
			else
			{
				pageerror("Topic Error","","Sorry, but there wasn't a topic id present.");
			}

			if(!getFP(topic_parent_($id),1))
			{
				pageerror("Permission Error","","Sorry, but you don't have permissions viewing this topic.");
			}
				//do some post stuff
			//total replies pagination limit
			$ppt = mysql_query("SELECT `postpertopic` FROM `boardstatus` LIMIT 1");
			$p_p_t = mysql_fetch_array($ppt);
			$total_limit = $p_p_t['postpertopic'];
			topic_pagination($id,$total_limit);
			$main = mysql_query("SELECT * FROM `topics` WHERE `id` = '".$id."' ");
			$tmain = mysql_fetch_array($main);
			$umain = mysql_query("SELECT * FROM `users` WHERE `username` = '".$tmain['username']."'");
			$fuser = mysql_fetch_array($umain);
			$isSticked = ($tmain['sticky'] == 1) ? "<a href='mode.php?type=sticktopic&tid=".$id."'>Sticky</a>":"<a href='mode.php?type=unsticktopic&tid=".$id."'>Un-Sticky</a>";
			$isLocked = ($tmain['closed'] == 1) ? "<a href='mode.php?type=closetopic&tid=".$id."'>Lock</a>":"<a href='mode.php?type=opentopic&tid=".$id."'>Un-Lock</a>";
			
			//check if user has permissions
			if($permissions['admin'] == 't' || $permissions['e_topic'] == 't')
			{
				$modet = "<a href='mode.php?type=edit&post=topic&id=".$id."'>Edit</a> | <a href='mode.php?type=move&post=topic&id=".$id."'>Move Topic</a> | ". $isSticked . " | " . $isLocked;
			}
			elseif($logged['username'] == $tmain['username'] && $permissions['e_topic'] == 't')
			{
				$modet = "<a href='mode.php?type=edit&post=topic&id=".$id."'>Edit</a>";
			}
			else
			{
				$modet = "";
			}
			echo "<br />" .  run_buttons($id);
			$Temp = new Template;
			$Temp->dir = $logged['dskin'];
			$Temp->file = "topic_title.tpl";
			$Temp->tp();
			$Temp->tr(array(
			'TITLE' => $tmain['title']
			));
			echo $Temp->html;
			
			//if($_GET['p'] == 1 || !isset($_GET['p']) )
		//	{
				echo ('
						<tr>
							<td colspan="2" class="small_title"><span style="float:left;"><b>Posted On:</b> '.timezone_stamp($tmain['timestamp'],$logged['timezone']).'</span><span style="float:right" class="small_title_link">'.$modet.'</span></td>
						</tr>
				');
			//}
			if( !isset($_GET['p']) || empty($_GET['p']) || $_GET['p'] == 0)
			{
				$page = 1;
			}
			else
			{
				if(!is_numeric($_GET['p']))
				{
					pageerror("Page Error","","Didn't specify a correct page id.");
				}
				else
				{
					$page = intval( mysql_real_escape_string( $_GET['p'] ));
				}
			}
			

			$limit_start = ( ( $page * $total_limit ) - $total_limit );
			//get replies
			$replies = mysql_query("SELECT * FROM `replies` WHERE `tid` = '" . $id . "' ORDER BY `id` LIMIT $limit_start,$total_limit") or die(mysql_error(__FILE__,__LINE__));
			$has_replys = mysql_num_rows($replies);
			if($has_replys != 0)
			{
				//check to see if there are any replies :D
				while($replys = mysql_fetch_array( $replies ) )
				{
					//check if user has permissions
					if($permissions['admin'] == 't' || $permissions['d_post'] == 't')
					{
						$modep = "<a href='mode.php?type=edit&post=reply&id=".$replys['id']."&tid=".$id."'>Edit</a> | <a href='mode.php?type=delete&post=reply&id=".$replys['id']."&tid=".$id."'>Delete</a>";
					}
					elseif($logged['username'] == $replys['username'])
					{
						$modep = "<a href='mode.php?type=edit&post=reply&id=".$replys['id']."&tid=".$id."'>Edit</a>";
					}
					else
					{
						$modep = "";
					}
					
					$usez = mysql_query("SELECT * FROM `users` WHERE `username` = '".$replys['username']."'");
					$useri = mysql_fetch_array($usez);
					
					//show replies
					$Temp = new Template;
					$Temp->dir = $logged['dskin'];
					$Temp->file = "topic_post.tpl";
					$Temp->tp();
					$Temp->tr(array(
					'OPTIONS' => $modep,
					'POSTER' => $replys['username'],
					'AVY' => (!empty($useri['avatar'])) ? '<img width="100" height="100" src="'.$useri['avatar'].'" alt="" /><br />' : '',
					'DATE' => timezone_stamp($replys['date'],$logged['timezone']),
					'GROUP' => group($useri['level']),
					'UID' => $useri['id'],
					'UPOST' => $useri['post'],
					'POST' => nl2br(bbcode_format($replys['post'])) . "<br />__________________<br />" . (($useri['signature'] == '') ? '&nbsp;' : nl2br(bbcode_format($useri['signature'])) )
					));
					echo $Temp->html;
				}
			}
			else
			{
				echo
				("
					<tr>
						<td width='100%' class='rows' align='center'><em>There isn't any posts in this topic</em></td>
					</tr>
				");
			}
			echo (' </table> '.run_buttons($id).'<br />');
			topic_pagination($id,$total_limit);
			add_views($id);
		}
		function run_buttons($id)
		{
			global $logged;
			$info = mysql_query("SELECT `sticky`,`closed` FROM `topics` WHERE `id` = '".$id."' ") or die("couldn't run buttons");
			$info = mysql_fetch_array($info);
			$closed = ($info['closed'] == "1") ? '<a href="index.php?act=newreply&amp;id='.$id.'"><img src="styles/'.$logged['dskin'].'/Add%20reply.png" alt="New Reply" style="border:1px solid black;margin-bottom:5px;margin-top: 5px;" /></a>':'<img src="styles/'.$logged['dskin'].'/Lockd.png" alt="Closed" style="border:1px solid black;margin-bottom:5px;margin-top: 5px;" /><br />';
			return $closed;
		}
		function add_views($id)
		{
			$id = intval(htmlspecialchars($id));
			$views = mysql_query("SELECT `views` FROM `topics` WHERE `id` = '".$id."'");
			$count = mysql_fetch_array($views);
			$add = intval($count['views'])+1;
			if( !mysql_query("UPDATE `topics` SET `views` = '".$add."' WHERE `id` = '".$id."'") )
			{
				echo "Error updating views in topic.";
			}
		}

?>
<?php
/*
****************************************************************
#sources/forums.php File
#Version 1.4
****************************************************************
#Copy Righted 2006-2007(http://nevuxbulletin.com) [nevuxab.info]
#Created By NevuxAB Developement Team
****************************************************************
*/
				if( !defined("NABPROOF") )
				{
					die("Hacking attempt");
				}
				function RUN_Forums()
				{
					global $logged;
					if( is_numeric($_GET['id']) )
					{
						$id = intval(htmlspecialchars($_GET['id']));//finish checking 
					}
					else
					{
						pageerror("Forum Error","","Sorry, but there wasn't a forum id present.");
					}
					
					//Show Subforums here
					$SubForums = mysql_query("SELECT * FROM `forums` WHERE `sid` = '".$id."'");
					if( mysql_num_rows($SubForums) > 0 )
					{
						$Temp = new Template;
						$Temp->dir = $logged['dskin'];
						$Temp->file = "category_header.tpl";
						$Temp->tp();
						$Temp->tr(array("CAT_NAME" => "SubForums"));
						echo $Temp->html;
						while( $SubForum = mysql_fetch_array($SubForums) )
						{
							//select the total replies for each forum
							$total_replies = mysql_query("SELECT topics.id,replies.tid FROM `topics`,`replies` WHERE `fid` = '".$SubForum['id']."' AND topics.id = replies.tid") or die(mysql_error());
							$total_replies_ = mysql_num_rows($total_replies);
					
							//select topic data from the database from these forums
							$nlastpost = mysql_query("SELECT `id`,`title`,`username` FROM `topics` WHERE `fid` ='".$SubForum['id']."' ORDER BY timestamp DESC") or die(mysql_error());
							$nlastpost1 = mysql_fetch_array($nlastpost);
							$topicid = $nlastpost1['id'];
							$topicuser = $nlastpost1['username'];
							$topicnumber = mysql_num_rows($nlastpost);
							//get latest replies from current forum
							$allreplies = mysql_query("SELECT * FROM `replies` WHERE `tid` = '".$topicid."' ORDER BY `id` DESC ") or die(mysql_error());
							$all_replies = mysql_fetch_array($allreplies);
							
							if($f['lastvisited'] == "")
							{
								$no_new_post = ($total_topics_f_2 != 0) ? "<img src='styles/default/New.png' alt='New' />" : "<img src='styles/default/No%20New.png' alt='No-New' />";
							}
							else
							{
								$lID = $logged['id'];
								$last_vdata = unserialize($f['lastvisited']);
								$no_new_post = ($last_vdata[$lID] < $total_topics_f_2) ? "<img src='styles/default/New.png' alt='New' />" : "<img src='styles/default/No%20New.png' alt='No-New' />";
							}
							//check if there we're any replies
							if( empty( $nlastpost1['title'] ) )
							{
								$IN = "No Topics Here.";
							}
							else
							{
								$IN = "<a href=\"index.php?act=topicshow&id=".$nlastpost1['id']."\">".$nlastpost1['title']."</a>";
							}
							if( $all_replies['username'] == "" )
							{
								$BY = $topicuser;
							}
							else
							{
								$BY = $all_replies['username'];
							}
							if(getFP($SubForum['id']))
							{
								$Temp = new Template;
								$Temp->dir = $logged['dskin'];
								$Temp->file = "idxforum.tpl";
								$Temp->tp();
								$Temp->tr(array(
									'NEWPOST' => $no_new_post,
									'FORUM_ID' => $SubForum['id'],
									'FORUM_NAME' => $SubForum['title'],
									'FORUM_DESC' => $SubForum['description'],
									'TOPICS' => $topicnumber,
									'REPLIES' => $total_replies_,
									'LASTPOSTER' => $BY,
									'TOPIC_LINK' => $IN,
									'SUBFORUMS' => ''
								));
								echo $Temp->html;
							}
						}
						echo "</table><br /><br /><br />";
					}
					//End Subforums here.
					//total replies pagination limit
					$ppt = mysql_query("SELECT `topicsperforum` FROM `boardstatus` LIMIT 1;");
					$p_p_t = mysql_fetch_array($ppt);
					$total_limit = intval($p_p_t['topicsperforum']);
					if( !isset($_GET['p']) || empty($_GET['p']) || $_GET['p'] == 0 )
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
					//do topic stuff with pagination
					$topics = mysql_query("SELECT * FROM `topics` WHERE `fid` = '".$id."' AND `sticky` = '1' ORDER BY `timestamp` DESC LIMIT $limit_start,$total_limit") or die(mysql_error());
					$TTtopics = mysql_query("SELECT * FROM `topics` WHERE `fid` = '".$id."' ");
					$has_topics = mysql_num_rows($TTtopics);
					$forum_title = mysql_query("SELECT `locked`,`title`,`lastvisited` FROM `forums` WHERE `id` = '".$id."' ") or die("Couldn't fetch forum info");
					$forum = mysql_fetch_array($forum_title);
					if(!getFP($id,0))
					{
						pageerror("Permission Error","","Sorry, but you don't have permissions viewing this forum.");
					}
					topic_pagination($id,$total_limit,0);
					echo "<br /><br />";
					if($forum['locked'] == 't')
					{
						echo
						("
							<img src='styles/".$logged['dskin']."/Lockd.png' /><br />
						");
					}
					else
					{
						echo 
						("
							<a href=\"index.php?act=newtopic&id=".$id."\"><img src=\"styles/".$logged['dskin']."/New%20topic.png\" alt='New Topic' style='border:1px solid black;margin-bottom:1px;' /></a>
						");
					}
					if($has_topics == 0)
					{
						$Temp = new Template;
						$Temp->dir = $logged['dskin'];
						$Temp->file = "forums_none.tpl";
						$Temp->tp();
						$Temp->tr(array('FORUM_NAME' => $forum['title']));
						echo $Temp->html;
					}
					else
					{
						$Temp = new Template;
						$Temp->dir = $logged['dskin'];
						$Temp->file = "forums_header.tpl";
						$Temp->tp();
						$Temp->tr(array('FORUM_NAME' => $forum['title']));
						echo $Temp->html;
						
						//do a check to see if topics are stickied
						$pinned_t = mysql_query("SELECT * FROM `topics` WHERE `fid` = '".$id."' AND `sticky` = '0' ORDER BY `timestamp` DESC");
						$totalpins = mysql_num_rows($pinned_t);
				        //show pinned topics
				        if($totalpins != 0 )
						{
							echo ('
				                <tr>
				                    <td width="100%" class="small_title" colspan="4"><span>Pinned Topics</span></td>
				                </tr>
							');
							$totalrepliesever = 0;
				            while( $pinned = mysql_fetch_array( $pinned_t ) )
							{
				                $replizS = mysql_query("SELECT * FROM `replies` WHERE `tid` = '".$pinned['id']."' ");
								$replizS = mysql_num_rows($replizS);
								$lastS = mysql_query("SELECT `username`,`date` FROM `replies` WHERE tid='".$topic_info['id']."' ORDER BY `id` DESC");
				                $lastpS = mysql_fetch_array($lastS);
								(($lastposterS = mysql_num_rows($lastS)) != 0) ? $ltpS = $lastpS['username'] AND $ltpdS = date("m-d-y",$lastpS['date']) : $ltpS = $pinned['username'] AND $ltpdS = date("m-d-y",$pinned['timestamp']);
								$totalrepliesever += $replizS;
								//Output pinned topics
								$Temp = new Template;
								$Temp->dir = $logged['dskin'];
								$Temp->file = "forums_content.tpl";
								$Temp->tp();
								$Temp->tr(array(
								'VIEWS' => $pinned['views'],
								'REPLIES' => $replizS,
								'TID' => $pinned['id'],
								'TNAME' => $pinned['title'],
								'AUTHOR' => $pinned['username'],
								'UID' => getid($pinned['username']),
								'DESC' => $pinned['description'],
								'LASTP' => $ltpS,
								'UID_2' => getid($ltpS),
								'DATE' => $ltpdS
								));
								echo $Temp->html;
							}
							if( mysql_num_rows($topics) > 0)
							{
								echo 
								('
					                <tr>
					                    <td width="100%" class="small_title" colspan="4"><span>Normal Topics</span></td>
					                </tr>
								');
							}
						}
						while( $topic_info = mysql_fetch_array($topics) )
						{
							$repliz = mysql_query("SELECT * FROM `replies` WHERE `tid` = '".$topic_info['id']."' ");
							$repliz = mysql_num_rows($repliz);
							$totalrepliesever = $totalrepliesever+$repliz;
							$last = mysql_query("SELECT `username`,`date` FROM `replies` WHERE `tid` = '".$topic_info['id']."' ORDER BY `id` DESC");
							$lastp = mysql_fetch_array($last);
							(($lastposter = mysql_num_rows($last)) != 0) ? $ltp = $lastp['username'] AND $ltpd = date("m-d-y",$lastp['date']) : $ltp = $topic_info['username'] AND $ltpd = date("m-d-y",$topic_info['timestamp']);
							//output normal topics
							$Temp = new Template;
							$Temp->dir = $logged['dskin'];
							$Temp->file = "forums_content.tpl";
							$Temp->tp();
							$Temp->tr(array(
								'VIEWS' => $topic_info['views'],
								'REPLIES' => $repliz,
								'TID' => $topic_info['id'],
								'TNAME' => $topic_info['title'],
								'AUTHOR' => $topic_info['username'],
								'UID' => getid($topic_info['username']),
								'DESC' => $topic_info['description'],
								'LASTP' => $ltp,
								'UID_2' => getid($ltp),
								'DATE' => $ltpd
							));
							echo $Temp->html;
						}
						
						$lfvisit = $forum['lastvisited'];
						$lID = $logged['id'];
						if($lfvisit == "")
						{
							$user_lv = serialize(array($logged['id'] => $totalrepliesever));
							$up_lfv = mysql_query("UPDATE `forums` SET `lastvisited` ='".$user_lv."' WHERE `id` = '".$id."' ") or die("error updating last visited");
						}
						else
						{
							$lfvi = unserialize($lfvisit);
							if($lfvi[$lID] < $totalrepliesever || $lfvi[$lID] == "")
							{
								$lfvi[$lID] = $totalrepliesever;
								$up_lfv = mysql_query("UPDATE `forums` SET `lastvisited` ='".serialize($lfvi)."' WHERE `id` = '".$id."' ") or die("error updating last visited");
							}
						}
						echo ('
							</table>
						');
				    }
				}//end function
					
					
?>
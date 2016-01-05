<?php
/*
****************************************************************
#sources/post.php File
#Version 1.3.1.2
****************************************************************
#Copy Righted 2006-2007(http://nevuxbulletin.com)
#Created By NevuxAB Developement Team
****************************************************************
*/

		if( !defined("NABPROOF") )
		{
			die("Hacking attempt");
		}
		//--------------------------------
		//do_newreply function
		//--------------------------------
			function RUN_Newreply()
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
				//New reply to topic
				$date = time();
				$user = $logged['username'];
				$post = htmlspecialchars($_POST['post']);
				if(!getFP(topic_parent_($id),2))
				{
					pageerror("Permission Error","","Sorry, but you don't have permissions to reply to this topic.");
				}
				if(check_forum_lock(topic_parent_($id)) AND $permissions['admin'] != 't')
				{
					pageerror("Forum Locked","","Sorry, you can't post a topic in here because this forum is locked");
				}
				else
				{
					if( !isset($_POST['newreply']) )
					{
						$Temp = new Template;
						$Temp->dir = $logged['dskin'];
						$Temp->file = "newreply.tpl";
						$Temp->tp();
						$Temp->tr(array(
							'POST' => '',
							'NAME' => 'New Reply'
						));
						echo $Temp->html;
					}
					else
					{
						if( !$logged['username'] )
						{
							die(pageerror("Reply Error","There was an error replying to topic!","You don't have permissions to reply to these topics!"));
						}
						if( empty($post) )
						{
							die(pageerror("Reply Error","There was an error replying to topic!","You left the post empty please go back and insert information!"));
						}
						update_post_count();
						finished("Reply Added!","New Reply was Created!","Thank you now your reply was sucessfully created!","index.php?act=topicshow&id=".$id);
						mysql_query("INSERT INTO `replies` (`tid`,`post`,`username`,`date`) VALUES('".$id."','".$post."','".$user."','".$date."') ") or die(pageerror("Reply Error","There was a problem adding reply","Something went wrong adding new reply"));
						mysql_query("UPDATE `topics` SET `timestamp` = '".time()."' WHERE `id` = '".$id."'")  or die(pageerror("Reply Error","There was a problem adding reply","Something went wrong trying to update the topics timestamp."));
					}
				}
			}

		//--------------------------------
		//do_newtopic function
		//--------------------------------
			function RUN_Newtopic()
			{
				global $logged,$permissions;
				if( is_numeric($_GET['id']) AND !empty($_GET['id']))
				{
					$id = intval(htmlspecialchars($_GET['id']));
				}
				else
				{
					pageerror("Topic Error","","Sorry, but there wasn't a forum id present.");
				}
				if(!getFP($id,3))
				{
					pageerror("Permission Error","","Sorry, but you don't have permissions to post a new topic.");
				}
				if(check_forum_lock($id) AND $permissions['admin'] != 't')
				{
					pageerror("Forum Locked","","Sorry, you can't post a topic in here because this forum is locked");
				}
				else
				{
					$post = htmlspecialchars($_POST['post']);
					$ttitle = htmlspecialchars($_POST['title']);
					$tdesc = htmlspecialchars($_POST['tdesc']);
					$time = time();
					if( !isset($_POST['newtopic']) )
					{
						$Temp = new Template;
						$Temp->dir = $logged['dskin'];
						$Temp->file = "newtopic.tpl";
						$Temp->tp();
						$Temp->tr(array(
							'TOPIC_NAME' => 'New Topic',
							'TITLE' => '',
							'DESC' => '',
							'POST' => '',
							'<<HIDE>>' => '',
							'<<HIDE_2>>' => ''
						));
						echo $Temp->html;
					}
					else
					{
						if(!$logged['username'])
						{
							pageerror("Topic Error","There was an error creating topic","You don't have permissions to post a new topic!");
						}
						if( empty($post) )
						{
							pageerror("Topic Error","There was an error creating topic","Please check your post because you left the topic post blank!");
						}
						elseif(empty($ttitle))
						{
							pageerror("Topic Error","There was an error creating topic","Please check your post because you left the topic title blank!");
						}
						else
						{
							update_post_count();
							$newtopic = mysql_query("INSERT INTO `topics`(`fid`,`date`,`timestamp`,`title`,`username`,`description`) VALUES('".$id."','".$time."','".$time."','".$ttitle."','".$logged['username']."','".$tdesc."')") or die(pageerror("Topic Error","Something went wrong in SQL","Sorry, but your topic couldn't be created please contact the administrator with this error"));
							$nreply = mysql_query("SELECT `id` FROM `topics` ORDER BY `id` DESC LIMIT 1") or die("ERROR");
							$nreply = mysql_fetch_array($nreply);
							finished("Topic Created!","New Topic was Created!","Thank you now your topic was sucessfully created.","index.php?act=topicshow&id=".$nreply['id']);
							$new_reply = mysql_query("INSERT INTO `replies` (`tid`,`post`,`username`,`date`) VALUES('".$nreply['id']."','".$post."','".$logged['username']."','".$time."')")  or die(pageerror("Reply Error","There was a problem adding reply","Something went wrong adding new reply"));
						}
					}
				}
			}
?>
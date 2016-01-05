<?php
/*
****************************************************************
#mode.php File
#Version 1.4
****************************************************************
#Copy Righted 2006-2007(http://nevuxbulletin.com)
#Created By NevuxAB Developement Team
****************************************************************
*/


//Runs to moderating mods for posts and topics
function moderator()
{
	//global vars
	global $logged,$permissions;
	//set up permissions id's and type id's
	$id = intval( htmlspecialchars( trim( strip_tags( $_GET['id'] ) ) ) );
	$type = htmlspecialchars( trim( strip_tags( $_GET['type'] ) ) );
	$tid = intval( htmlspecialchars( $_GET['tid'] ) );
	$posts = htmlspecialchars( $_GET['post'] );
	
	//check what type and modes where in
	switch($type)
	{
		case "edit":
			switch($posts)
			{
				case "topic":
				echo "TOPIC";
					//gets topic data from database
					$tdata_ = mysql_query("SELECT * FROM `topics` WHERE `id` = '" . $id . "' ");
					$tdata = mysql_fetch_array($tdata_);
					
					//if they don't have permission
					if( ($permissions['e_topic'] != 't' AND $logged['username'] != $tdata['username']) || $permissions['admin'] != 't' )
					{
						logs("Invalid User","1");
						pageerror("Topic Error","There was an error editing topic.","You don't have permissions to edit this topic!");
					}
					
					if( !isset( $_POST['newtopic'] ) )
					{
						$Temp = new Template;
						$Temp->dir = $logged['dskin'];
						$Temp->file = "newtopic.tpl";
						$Temp->tp();
						$Temp->tr(array(
							'TOPIC_NAME' => 'Edit Topic',
							'TITLE' => $tdata['title'],
							'DESC' => $tdata['description'],
							'POST' => '',
							'<<HIDE>>' => '<!--',
							'<<HIDE_2>>' => '-->'
						));
						echo $Temp->html;
					}
					else
					{
					
						if( empty($_POST['title']) )
						{
							pageerror("Topic Error","There was an error editing topic","Please check your post because you left the topic post or topic title blank!");
						}
						else
						{
							$ttitle = htmlspecialchars($_POST['title']);
							$description = htmlspecialchars($_POST['tdesc']);
							$ndata = @mysql_query("UPDATE `topics` SET `description` = '".$description."', `title` = '".$ttitle."' WHERE `id` = '".$id."' ");
							if( !$ndata )
							{
								pageerror("Topic Error","There was an error editing topic","There was an error updating sql: " . mysql_error());
							}
							finished("Topic Updated!","Current Topic was Updated!","Thank you now your topic was sucessfully updated.","index.php?act=topicshow&id=".$id);
						}
					}
				break;
				case "reply":
					//Selects reply data from db
					$pdata_ = mysql_query("SELECT * FROM `replies` WHERE `id` = '".$id."' ");
					$pdata = mysql_fetch_array($pdata_);
					
					if( ($permissions['e_post'] != 't' AND $logged['username'] != $pdata['username']) or ($permissions['admin'] != 't') )
					{
						logs("Invalid User","1");
						pageerror("Reply Error","There was an error editing reply.","You don't have permissions to edit this post!");
					}
					if( !isset( $_POST['newreply'] ) )
					{
						$Temp = new Template;
						$Temp->dir = $logged['dskin'];
						$Temp->file = "newreply.tpl";
						$Temp->tp();
						$Temp->tr(array(
							'POST' => $pdata['post'],
							'NAME' => "Edit Reply"
						));
						echo $Temp->html;
					}
					else
					{
						$rpost = htmlspecialchars( $_POST['post'] );
						if( empty( $_POST['post'] ) )
						{
							pageerror("Reply Error","There was an error editing reply.","You left the post field blank please go back and check again!");
						}
						else
						{
							$pdata = @mysql_query("UPDATE `replies` SET `post` = '".$rpost."' WHERE `id` = '".$id."' ");
							if( !$pdata )
							{
								pageerror("Reply Error","There was an error editing reply.","There was an sql error: " . mysql_error());
							}
							finished("Reply Updated!","Current Post was Updated!","Thank you now your reply was sucessfully updated.","index.php?act=topicshow&id=".$tid);
						}
					}
				break;
			}
		break;
		case "delete":
			//Checks if user has permissiosn
			if( $permissions['d_post'] != 't' )
			{
				logs("Invalid User","1");
				pageerror("Topic Error","There was an error opening topic.","You don't have permissions to open this topic!");
			}
			
			switch( $posts )
			{
				case "reply":
					if( !isset( $_POST['submit'] ) )
					{
						$Temp = new Template;
						$Temp->dir = $logged['dskin'];
						$Temp->file = "mode_delete.tpl";
						$Temp->tp();
						echo $Temp->html;
					}
					else
					{
						if( isset( $_POST['del'] ) )
						{
							if( mysql_query("DELETE FROM `replies` WHERE `id` = '" . $id . "'") )
							{
								finished("Post deleted Successfully","","Your post was deleted successfully, please wait while your being redirected.","index.php?act=topicshow&id=".$tid);
							}
							else
							{
								pageerror("Deletion Error","","There was a problem deleting post, please contact the NevuxAB Support Tech.");
							}
						}
						else
						{
							finished("Post Message","","No action was taken, please wait while your being redirected.","index.php?act=topicshow&id=".$tid);
						}
					}
				break;
			}
		break;
		case "opentopic":
			if( $permissions['o_topic'] != 't' )
			{
				logs("Invalid User","1");
				pageerror("Topic Error","There was an error opening topic.","You don't have permissions to open this topic!");
			}
			$otopic = mysql_query("SELECT `closed` FROM `topics` WHERE `id` = '" . $tid . "' ");
			$otopic = mysql_fetch_array($otopic);
			if( $otopic['closed'] == 1 )
			{
				pageerror("Topic Error","","Topic is already Opened!");
			}
			else
			{
				$topic_update = mysql_query("UPDATE `topics` SET `closed` = '1' WHERE `id` = '" . $tid . "' ");
				if( !$topic_update )
				{
					pageerror("Topic Error","","There was an error updating topic: " . mysql_error());
				}
				finished("Topic Updated!","Current Topic was Pinned!","Thank you now your topic was sucessfully opened.","index.php?act=topicshow&id=".$tid);
			}
		break;
		case "closetopic":
			if( $permissions['c_topic'] != 't' )
			{
				logs("Invalid User","1");
				pageerror("Topic Error","There was an error closing topic.","You don't have permissions to close this topic!");
			}
			$ctopic = mysql_query("SELECT `closed` FROM `topics` WHERE `id` = '" . $tid . "' ");
			$ctopic = mysql_fetch_array($ctopic);
			if( $ctopic['closed'] == 0 )
			{
				pageerror("Topic Error","","Topic is already closed!");
			}
			else
			{
				$topic_update = mysql_query("UPDATE `topics` SET `closed` = '0' WHERE `id` = '" . $tid . "' ");
				if( !$topic_update )
				{
					pageerror("Topic Error","","There was an error updating topic: " . mysql_error());
				}
				finished("Topic Updated!","Current Topic was Closed!","Thank you now your topic was sucessfully Closed.","index.php?act=topicshow&id=".$tid);
			}
		break;
		case "sticktopic":
			if($permissions['topic_pin'] != 't')
			{
				logs("Invalid User","1");
				pageerror("Topic Error","There was an error Pinning topic.","You don't have permissions to Pin this topic!");
			}
			$stopic = mysql_query("SELECT `sticky` FROM `topics` WHERE `id` = '".$tid."' ");
			$stopic = mysql_fetch_array($stopic);
					
			if( $stopic['sticky'] == 0 )
			{
				pageerror("Topic Error","","Topic is already Pinned!");
			}
			else
			{
				$topic_update = mysql_query("UPDATE `topics` SET `sticky` = '0' WHERE `id` = '" . $tid . "' ");
				if( !$topic_update )
				{
					pageerror("Topic Error","","There was an error updating topic: " . mysql_error());
				}
				finished("Topic Updated!","Current Topic was Pinned!","Thank you now your topic was sucessfully Pinned.","index.php?act=topicshow&id=".$tid);
			}
		break;
		case "unsticktopic":
			if( $permissions['topic_pin'] != 't' )
			{
				logs("Invalid User","1");
				pageerror("Topic Error","There was an error Un-Pinning topic.","You don't have permissions to Un-Pin this topic!");
			}
			$ustopic = mysql_query("SELECT `sticky` FROM `topics` WHERE `id` = '".$tid."' ");
			$ustopic = mysql_fetch_array($ustopic);
			
			if($ustopic['sticky'] == 1)
			{
				pageerror("Topic Error","","Topic is already Un-Pinned!");
			}
			else
			{
				$topic_update = mysql_query("UPDATE topics SET sticky = '1' WHERE `id` = '".$tid."' ");
				if( !$topic_update )
				{
					pageerror("Topic Error","","There was an error updating topic: " . mysql_error());
				}
				finished("Topic Updated!","Current Topic was Un-Pinned!","Thank you now your topic was sucessfully Un-Pinned.","index.php?act=topicshow&id=$tid");
			}
		break;
		case "move":
			if( $permissions['m_topic'] != 't' )
			{
				logs("Invalid User","1");
				pageerror("Topic Error","There was an error moving topic.","You don't have permissions to move this topic!");
			}
			$nparent = htmlspecialchars($_POST['to']);
			if( isset( $_POST['update'] ) )
			{
				$topic_update = mysql_query("UPDATE `topics` SET `fid` = '" . $nparent . "' WHERE `id` = '" . $id . "'");
				if( !$topic_update )
				{
					pageerror("Topic Error","","There was an error updating topic: " . mysql_error());
				}
				finished("Topic Updated!","Current Topic was Pinned!","Thank you now your topic was moved sucessfully.","index.php?act=topicshow&id=".$id);
			}
			else
			{
				$gettopics = mysql_query("SELECT * FROM `forums` ORDER BY `id` ASC");
				$values = "";
				while (	$showtopics = MySQL_Fetch_Array($gettopics)	)
				{
					$values .= "<option value='" . $showtopics['id'] . "'>" . $showtopics['title'] . "</option>";
				}
				$Temp = new Template;
				$Temp->dir = $logged['dskin'];
				$Temp->file = "mode_move.tpl";
				$Temp->tp();
				$Temp->tr(array(
					'OPTIONS' => $values
				));
				echo $Temp->html;
			}
		break;
	}
}
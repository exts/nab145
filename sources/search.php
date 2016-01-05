<?php
/*
****************************************************************
#classes/search.php File
#Version 1.4
****************************************************************
#Copy Righted 2006-2007(http://nevuxbulletin.com) [nevuxab.info]
#Created By NevuxAB Developement Team
****************************************************************
*/
if(!defined("NABPROOF"))
{
	die("Hacking attempt.");
}	

function research()
{
	global $logged;
	$Temp = new Template;
	$Temp->dir = $logged['dskin'];
	$Temp->file = "search_body.tpl";
	$Temp->tp(__LINE__,__FILE__);
	$Temp->tr(array(
		'BODY' => search(),
		'DESC' => 'Searching...'
	));
	echo $Temp->html;
}
function search()
{
	global $logged;
	if(!isset($_POST['submit']))
	{
		$Temp = new Template;
		$Temp->dir = $logged['dskin'];
		$Temp->file = "search_find.tpl";
		$Temp->tp(__LINE__,__FILE__);
		return $Temp->html;
	}
	else
	{
		$t = ($_POST['s'] == 2) ? 1 : 2;
		$type = ($_POST['s'] == 2) ? '`topics`' : '`replies`';
		$type_ = ($_POST['s'] == 2) ? '`title`' : '`post`';
		$query = htmlspecialchars($_POST['query']);
		$sql = mysql_query("SELECT * FROM " . $type . " WHERE " . $type_ . " LIKE '%" . $query . "%'");
		
		if( empty($query) || $query == "")
		{
			pageerror("Search Error","Something was blank.","Looks like you left the search field blank, please go back and try again.");
		}
		if( mysql_num_rows($sql) <= 0 )
		{
			pageerror("Search Error","Not found","Looks like there wasn't post or topic in the database that matched your query.");
		}
		
		
		$content = "";
		while( $row = mysql_fetch_array( $sql ) )
		{
			switch($t)
			{
				case 1:
					if( getFP($row['fid'],1) )
					{
						$Temp = new Template;
						$Temp->dir = $logged['dskin'];
						$Temp->file = "search_msg.tpl";
						$Temp->tp(__LINE__,__FILE__);
						$Temp->tr(array(
							'TID' => $row['id'],
							'TNAME' => $row['title'],
							'UID' => getid($row['username']),
							'DATE' => (!empty($row['date'])) ? date("m-d-y",$row['date']) : 'unknown',
							'MESSAGE' => nl2br(bbcode_format(getFirstPost($row['id']))),
							'POSTER' => $row['username']
						));
						$content .= $Temp->html;
					}
				break;
				case 2:
					if( getFP(topic_parent_($row['id']),1) )
					{
						$Temp = new Template;
						$Temp->dir = $logged['dskin'];
						$Temp->file = "search_msg.tpl";
						$Temp->tp(__LINE__,__FILE__);
						$Temp->tr(array(
							'TID' => $row['tid'],
							'TNAME' => topicName($row['title']),
							'UID' => getid($row['username']),
							'DATE' => (!empty($row['date'])) ? date("m-d-y",$row['date']) : 'unknown',
							'MESSAGE' => nl2br(bbcode_format($row['post'])),
							'POSTER' => $row['username']
						));
						$content .= $Temp->html;
					}
				break;
			}
		}
		if( empty($content) || $content == "")
		{
			pageerror("Search Error","Not found","Looks like there wasn't post or topic in the database that matched your query.");
		}
		return $content;
	}
}
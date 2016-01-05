<?php
/*
****************************************************************
#sources/admin/temp.php File
#Version 1.4
****************************************************************
#Copy Righted 2006-2007(http://nevuxbulletin.com) [nevuxab.info]
#Created By NevuxAB Developement Team
****************************************************************
*/

	function theader()
	{
		echo
		('
			<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
		');
		echo
		("
						<html xmlns=\"http://www.w3.org/1999/xhtml\">
							<head>
								<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\" />
								<meta name=\"description\" content=\"NevuxBB 1.3.1 Forum\" />
								<meta name=\"keywords\" content=\"Community,BulletinBoard,BB,Internet, Demonic,NAB 1.3.1,Free,Forum software, Forum\" />
								<meta name=\"expires\" content=\"+1 days\" />
								<meta name=\"reply-to\" content=\"scheols@gmail.com\" />
								<meta name=\"robots\" content=\"index follow\" />
								<title>Administration Panel - Nevux Support Forum - (Powered By Nevux Ability Boards)</title>
								<link rel=\"stylesheet\" type=\"text/css\" href=\"./styles/pro/style.css\" />
							</head>
							<body>
								<div id='banner'>
									<div class='banner'><img src='styles/pro/images/banner.jpg' alt='Banner' /></div>
									<div class='b_right'>
										<a href='#'>Search</a> | <a href='#'>Members List</a><br /><br />
									</div>
									<div class='clear'></div>
								</div>
								<div id='wrapper'>
									<div id='forum'>
		");
	}
	function footer()
	{
		echo
		("
									</div>
								</div>
								<div id='footer'>
									Powered By Nevux Ability Boards 1.4.5 © 2006 - 2007
								</div>
							</body>
						</html>
		");
	}
	function left_nav()
	{
		echo
		("
									<table width='100%' cellspacing='4' cellpadding='0'>
										<tr>
											<td width='30%' valign=\"top\">
												<table width='100%' class=\"forum\" cellspacing='0' cellpadding='0'>
													<tr>
														<td class='category'><div class='cat_title'>Navigation</div></td>
													</tr>
													<tr>
														<td class='small_title'><span>Administration</span></td>
													</tr>
													<tr>
														<td class='rows' valign='top'>
															<br />
															<ul>
																<li><a href='acp.php'>Home</a></li>
																<li><a href='acp.php?action=config'>Configuration</a></li>
																<li><a href='index.php'>Return to Forums</a></li>
															</ul>
															<br />
														</td>
													</tr>
													<tr>
														<td class='small_title'><span>Forums</span></td>
													</tr>
													<tr>
														<td class='rows' valign='top'>
														<br />
															<ul>
																<li><a href='acp.php?action=newforum'>New Forum</a></li>
																<li><a href='acp.php?action=newcat'>New Category</a></li>
																<li><a href='acp.php?action=boards'>Manage Forums/Cats</a></li>
															</ul>
															<br />
														</td>
													</tr>
													<tr>
														<td class='small_title'><span>Members</span></td>
													</tr>
													<tr>
														<td class='rows' valign='top'>
															<br />
															<ul>
																<li><a href='acp.php?action=adduser'>Add User</a></li>
																<li><a href='acp.php?action=edituser'>Edit User</a></li>
																<li><a href='acp.php?action=editusergroup&option=add'>Add User Group</a></li>
																<li><a href='acp.php?action=editusergroup'>Edit UserGroups</a></li>
															</ul>
															<br />
														</td>
													</tr>
												</table>
											</td>
		");
	}
	function right_body()
	{
		echo
		("
											<td width='70%' valign=\"top\">
												<table width='100%' class='forum' cellspacing='0' cellpadding='0'>
													<tr>
														<td class='category'><div class='cat_title'>Content</div></td>
													</tr>
													<tr>
														<td class='small_title'>".func_get_arg(1)."</td>
													</tr>
													<tr>
														<td class='rows' valign='top'>
														".func_get_arg(0)."
														</td>
													</tr>
												</table>
											</td>
										</tr>
									</table>
		");
	}
?>
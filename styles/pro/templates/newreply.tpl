						<form method='post' action=''>	
							<table class='forum' width='100%' cellpadding='0' cellspacing='0'>
								<tr>
									<td class='category' colspan='4'><div class='cat_title'>{NAME}</div></td>
								</tr>
								<tr>
									<td class='small_title' colspan='4'></td>
								</tr>
								<tr>
									<td class='common' width='20%'>Bulletin Board Code</td>
									<td class='common' width='80%'>
										<input class='bbcode' type='button' name='B' value='B' onClick='bb_code(this)' />
										<input class='bbcode' type='button' name='I' value='I' onClick='bb_code(this)' />
										<input class='bbcode' type='button' name='S' value='S' onClick='bb_code(this)' />
										<input class='bbcode' type='button' name='U' value='U' onClick='bb_code(this)' />
										<input class='bbcode' type='button' name='A' value='A' onClick='bb_code(this)' />
										<input class='bbcode' type='button' name='IMG' value='IMG' onClick='bb_code(this)' />
									</td>
								</tr>
								<tr>
									<td class='common' width='20%' valign='top'>Post body</td>
									<td width='80%' class='common'><textarea name='post' style='width: 99%' rows='15'>{POST}</textarea></td>
								</tr>
								<tr>
									<td colspan='2' class='common' align='center'><input class='bbcode' type="submit" name="newreply" value="{NAME}" /></td>
								</tr>
							</table>
						</form>
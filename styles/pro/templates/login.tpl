				<!--Start Category-->
				<form action='' method='post'>
					<table class='forum' width='100%' cellpadding='0' cellspacing='0'>
						<tr>
							<td class='category' colspan='4'><div class="cat_title">Please Login</div></td>
						</tr>
						<tr>
							<td class='small_title' colspan='4'></td>
						</tr>
						<tr>
							<td class='common' width='100%' align='center'>
								<fieldset class='fieldset'>
									<legend>Login with the correct Details or (<a href="index.php?act=lostpass">Lost Password?</a>)</legend>
									<table width='100%'>
										<tr>
											<td width="30%">Username:</td>
											<td width="70%"><input type="text" name="name" /></td>
										</tr>
										<tr>
											<td width="30%">Password: <input type="hidden" name="n2" value="{NUM_}" /><input type="hidden" name="n1" value="{NUM}" /></td>
											<td width="70%"><input type="password" name="password" /></td>
										</tr>
										<tr>
											<td width="30%">Validation Key({NUM} + {NUM_}) =</td>
											<td width="70%"><input type="text" name="valkey" /></td>
										</tr>
										<tr>
											<td width="100%" align="center" colspan="2"><input type="submit" name="login" value="Login!" /></td>
										</tr>
									</table>
								</fieldset>
							</td>
						</tr>
					</table>
				</form>
				<!--End Category-->
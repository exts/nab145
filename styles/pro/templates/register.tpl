				<form method="post">
					<table class="forum" cellspacing="0" cellpadding="0" width="100%">
						<tr>
							<td colspan="4" class="category"><div class="text">Register</div></td>
						</tr>
						<tr>
							<td colspan="2" class="small_title"><span>Register with the correct Details.</span></td>
						</tr>
						<tr>
							<td width='50%' class='common'>
								<table cellspacing="0" cellpadding="0" width="100%">
									<tr>
										<td width="40%" class="common"><b>Username</b>:</td>
										<td width="60%" class="common"><input type="text" name="name" /></td>
									</tr>
									<tr>
										<td width="40%" class="common"><b>Password</b>:</td>
										<td width="60%" class="common"><input type="password" name="password" /></td>
									</tr>
									<tr>
										<td width="40%" class="common"><b>Password Again</b>:</td>
										<td width="60%" class="common"><input type="password" name="password2" /></td>
									</tr>
									<tr>
										<td width="40%" class="common"><b>Email Address</b>:</td>
										<td width="60%" class="common"><input type="text" name="email" /></td>
									</tr>
									<tr>
										<td width="40%" class="common"><b>Email Address Again</b>:</td>
										<td width="60%" class="common"><input type="text" name="email2" /></td>
									</tr>
								</table>
							</td>
							<td width='50%' valign='top' class='common'>
								<table cellspacing="0" cellpadding="0" width="100%">
									<tr>
										<td width="100%" class="common">
										<b>Timezone</b>:<br />
											<select name="timezone">
												<option value="-12" class="" >(GMT -12:00) Eniwetok, Kwajalein</option>
												<option value="-11" class="" >(GMT -11:00) Midway Island, Samoa</option>
												<option value="-10" class="" >(GMT -10:00) Hawaii</option>
												<option value="-9" class="" >(GMT -9:00) Alaska</option>
												<option value="-8" class="" >(GMT -8:00) Pacific Time (US &amp; Canada)</option>
												<option value="-7" class="" >(GMT -7:00) Mountain Time (US &amp; Canada)</option>
												<option value="-6" class="" >(GMT -6:00) Central Time (US &amp; Canada), Mexico City</option>
												<option value="-5" class="" >(GMT -5:00) Eastern Time (US &amp; Canada), Bogota, Lima</option>
												<option value="-4" class="" >(GMT -4:00) Atlantic Time (Canada), Caracas, La Paz</option>
												<option value="-3" class="" >(GMT -3:00) Brazil, Buenos Aires, Georgetown</option>
												<option value="-2" class="" >(GMT -2:00) Mid-Atlantic</option>
												<option value="-1" class="" >(GMT -1:00 hour) Azores, Cape Verde Islands</option>
												<option value="0" class="" selected="selected">(GMT) Western Europe Time, London, Lisbon, Casablanca</option>
												<option value="1" class="" >(GMT +1:00 hour) Brussels, Copenhagen, Madrid, Paris</option>
												<option value="2" class="" >(GMT +2:00) Kaliningrad, South Africa</option>
												<option value="3" class="" >(GMT +3:00) Baghdad, Riyadh, Moscow, St. Petersburg</option>
												<option value="4" class="" >(GMT +4:00) Abu Dhabi, Muscat, Baku, Tbilisi</option>
												<option value="5" class="" >(GMT +5:00) Ekaterinburg, Islamabad, Karachi, Tashkent</option>
												<option value="6" class="" >(GMT +6:00) Almaty, Dhaka, Colombo</option>
												<option value="7" class="" >(GMT +7:00) Bangkok, Hanoi, Jakarta</option>
												<option value="8" class="" >(GMT +8:00) Beijing, Perth, Singapore, Hong Kong</option>
												<option value="9" class="" >(GMT +9:00) Tokyo, Seoul, Osaka, Sapporo, Yakutsk</option>
												<option value="10" class="" >(GMT +10:00) Eastern Australia, Guam, Vladivostok</option>
												<option value="11" class="" >(GMT +11:00) Magadan, Solomon Islands, New Caledonia</option>
												<option value="12" class="" >(GMT +12:00) Auckland, Wellington, Fiji, Kamchatka</option>
											</select>
										</td>
									</tr>
									<tr>
										<td width='100%' class='common'>
											<b>Image verification</b>:<br />
											<input type='text' name='verify' /><br /><br />
											<img src='captcha.php' alt='Image is not displaying Please refresh, or contact the administrator' /><br /><br /><i>If you can't see the image above please refresh the page.</i><br /><br /><br /><br /><br />&nbsp;
											
										</td>
									</tr>
								</table>
							</td>
						</tr>
						<tr>
							<td colspan="2" class="small_title"><span>Read the TOS and finish registration! Do You agree? <input type="checkbox" name="TOS" /></span></td>
						</tr>
						<tr>
							<td width="100%" class="common" colspan="2" align="center"><textarea style="width: 98%" rows="10" readonly="readonly">{TOS}</textarea></td>
						</tr>
						<tr>
							<td width="100%" class="common" align="center" colspan="3"><input type="submit" name="register" value="Register!" /></td>
						</tr>
					</table>
				</form>
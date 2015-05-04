<div class="va-main-wrap">
	<h1 class="va-page-title"><i class="icon-time"></i> Vacancy Settings</h1>
	<div id="va-tabs">
		<h2 class="nav-tab-wrapper">
			<?php $active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'va-general'; ?>
			<a href="?page=va-settings&tab=va-general" class="nav-tab <?php echo $active_tab == 'va-general' ? 'nav-tab-active' : ''; ?>">General</a>
			<a href="?page=va-settings&tab=va-labels" class="nav-tab <?php echo $active_tab == 'va-labels' ? 'nav-tab-active' : ''; ?>">Labels</a>
			<a href="?page=va-settings&tab=va-forms" class="nav-tab <?php echo $active_tab == 'va-forms' ? 'nav-tab-active' : ''; ?>">Forms</a>
			<a href="?page=va-settings&tab=va-notifications" class="nav-tab <?php echo $active_tab == 'va-notifications' ? 'nav-tab-active' : ''; ?>">Notifications</a>
			<a href="?page=va-settings&tab=va-setup-usage" class="nav-tab <?php echo $active_tab == 'va-setup-usage' ? 'nav-tab-active' : ''; ?>">Setup & Usage</a>
			<?php echo apply_filters('va_pro_tabs', '', $active_tab); ?>
			<?php echo apply_filters('va_ecp_tabs', '', $active_tab); ?>
			<span id="va-shortcode">To show Vacancy on the frontend use the shortcode <code>[vacancy]</code></span>
			<span class="va-clearer"></span>
		</h2>
	</div>
	<div id="va-settings-wrap">
		<?php if($active_tab == 'va-general') : ?>
			<div id="va-general">
				<form method="post" action="">
					<h2 class="va-tab-title">General Settings</h2><hr/>
					<p>Here you can customize the general Vacancy settings to your liking.</p>
					<br/>
					<p>
						<label>Show <?php echo $this->va_settings['reservation_single']; ?> Details</label>
						<select name="va_show_reservation_details">
							<option value="yes" <?php if($this->va_settings['show_reservation_details'] == "yes"){echo 'selected';};?>>Yes</option>
							<option value="no" <?php if($this->va_settings['show_reservation_details'] == "no"){echo 'selected';};?>>No</option>
						</select>
					</p>					
					<p>
						<label>Require Login</label>
						<select name="va_require_login">
							<option value="yes" <?php if($this->va_settings['require_login'] == "yes"){echo 'selected';};?>>Yes</option>
							<option value="no" <?php if($this->va_settings['require_login'] == "no"){echo 'selected';};?>>No</option>
						</select>
					</p>
					<p>
						<label>Hide WP admin bar</label>
						<select name="va_hide_admin_bar">
							<option value="yes" <?php if($this->va_settings['hide_admin_bar'] == "yes"){echo 'selected';};?>>Yes</option>
							<option value="no" <?php if($this->va_settings['hide_admin_bar'] == "no"){echo 'selected';};?>>No</option>
						</select>
						<div id="va-show-admin-bar" style="display:none;">
							<p>BUT, Still Show WP admin bar for</p>
							<?php global $wp_roles; ?>
							<ul>
							<?php foreach($wp_roles->roles as $role => $data) : ?>
								<li>
									<input id="<?php echo $role; ?>" type="checkbox" name="va_show_admin_bar_for[]" value="<?php echo $role; ?>" <?php if(is_array($this->va_settings['show_admin_bar_for'])){if(in_array($role, $this->va_settings['show_admin_bar_for'])){echo 'checked';}}else if($role == $this->va_settings['show_admin_bar_for']){echo 'checked';} ?>/> <label for="<?php echo $role; ?>"><?php echo $role; ?></label>
									<span class="va-clearer"></span>
								</li>
							<?php endforeach; ?>
							</ul>
						</div>
					</p>
					<p>
						<label>Default <?php echo $this->va_settings['venue_single']; ?></label>
						<?php $venues = $this->va_get_venues(); ?>
						<?php if($venues->have_posts()) : ?>
						<select id="va-venue-id" name="va_default_venue">
							<?php while($venues->have_posts()) : $venues->the_post(); ?>
								<option value="<?php the_ID(); ?>" <?php if(get_option('va_default_venue') == get_the_ID()){echo 'selected';} ?>><?php the_title(); ?></option>
							<?php endwhile; ?>
						</select>
						<?php else : ?>
							<p>No <?php echo $this->va_settings['venue_plural']; ?> have been created yet. <a href="/wp-admin/post-new.php?post_type=va_venue">Create new <?php echo $this->va_settings['venue_single']; ?></a>
						<?php endif; ?>
					</p>
					<p>
						<label>Day View Start Time</label>
						<?php echo $this->va_get_time_select('va_day_start_time',$this->va_settings['day_start_time']); ?>
					</p>
					<p>
						<label>Day View End Time</label>
						<?php echo $this->va_get_time_select('va_day_end_time',$this->va_settings['day_end_time']); ?>
					</p>
					<p>
						<label>Use Setup/Cleanup Times?</label>
						<select name="va_setup_cleanup">
							<option value="yes" <?php if($this->va_settings['setup_cleanup'] == 'yes'){echo 'selected';}?>>Yes</option>
							<option value="no" <?php if($this->va_settings['setup_cleanup'] == 'no'){echo 'selected';}?>>No</option>
						</select>
					</p>
					<p>
						Message to display after successful <?php echo $this->va_settings['reservation_single']; ?> submission<br/>
						<input type="text" name="va_reservation_success_message" size="80" value="<?php echo $this->va_settings['reservation_success_message']; ?>"/>
					</p>
					<input class="button button-primary" type="submit" name="va_update_settings" value="Update Settings" />
				</form>
			</div>
		<?php elseif($active_tab == 'va-forms') : ?>
			<div id="va-forms">
				<form method="post" action="">
					<h2 class="va-tab-title"><?php echo $this->va_settings['reservation_single']; ?> End Time Options</h2><hr/>
					<div id="va-end-time-options">
						<div class="reservation-end-time-type">
							<p>
								<label style="width:auto;"><input id="va-standard-end-time" type="radio" name="va_end_time_type" value="standard" <?php if($this->va_settings['end_time_type'] == 'standard'){echo 'checked';};?> /> Standard End Time Selection (any length)</label><br/>
								<label style="width:auto;"><input id="va-fixed-end-time" type="radio" name="va_end_time_type" value="fixed" <?php if($this->va_settings['end_time_type'] == 'fixed'){echo 'checked';};?> /> Fixed <?php echo $this->va_settings['reservation_single']; ?> Length</label><br/>
								<label style="width:auto;"><input id="va-minmax-end-time" type="radio" name="va_end_time_type" value="minmax" <?php if($this->va_settings['end_time_type'] == 'minmax'){echo 'checked';};?> /> Min/Max <?php echo $this->va_settings['reservation_single']; ?> Length Selection</label>
							</p>
						</div>
						<span class="va-clearer"></span>
						<div class="reservation-fixed-end-times" style="display:none;">
							<p>
								<?php echo $this->va_settings['reservation_plural']; ?> last for:<br/>
								<select name="va_end_time_length_hr">
								<?php for($i=0;$i<24;$i++) : ?>
									<option value="<?php echo $i; ?>" <?php if($this->va_settings['end_time_length_hr'] == $i){echo 'selected';};?>><?php echo $i; ?></option>
								<?php endfor; ?>
								</select> hours 
								<select name="va_end_time_length_min">
									<option value="00" <?php if($this->va_settings['end_time_length_min'] == '00'){echo 'selected';};?>>00</option>
									<option value="15" <?php if($this->va_settings['end_time_length_min'] == '15'){echo 'selected';};?>>15</option>
									<option value="30" <?php if($this->va_settings['end_time_length_min'] == '30'){echo 'selected';};?>>30</option>
									<option value="45" <?php if($this->va_settings['end_time_length_min'] == '45'){echo 'selected';};?>>45</option>
								</select> minutes
							</p>
						</div>	
						<span class="va-clearer"></span>				
						<div class="reservation-minmax-end-times" style="display:none;">
							<p>
								Minimum <?php echo $this->va_settings['reservation_single']; ?> Length:<br/><span class="description">(if a user chooses a 1:00pm start time and this setting is 30 minutes, the first available end time will be 1:30pm)</span><br/>
								<select name="va_end_time_min_length_hr">
								<?php for($i=0;$i<24;$i++) : ?>
									<option value="<?php echo $i; ?>" <?php if($this->va_settings['end_time_min_length_hr'] == $i){echo 'selected';};?>><?php echo $i; ?></option>
								<?php endfor; ?>
								</select> hours 
								<select name="va_end_time_min_length_min">
									<option value="00" <?php if($this->va_settings['end_time_min_length_min'] == '00'){echo 'selected';};?>>00</option>
									<option value="15" <?php if($this->va_settings['end_time_min_length_min'] == '15'){echo 'selected';};?>>15</option>
									<option value="30" <?php if($this->va_settings['end_time_min_length_min'] == '30'){echo 'selected';};?>>30</option>
									<option value="45" <?php if($this->va_settings['end_time_min_length_min'] == '45'){echo 'selected';};?>>45</option>
								</select> minutes
							</p>							
							<p>
								Maximum <?php echo $this->va_settings['reservation_single']; ?> Length:<br/><span class="description">(if a user chooses a 1:00pm start time and this setting is 2 hours, the last available end time will be 3:00pm)</span><br/>
								<select name="va_end_time_max_length_hr">
								<?php for($i=0;$i<24;$i++) : ?>
									<option value="<?php echo $i; ?>" <?php if($this->va_settings['end_time_max_length_hr'] == $i){echo 'selected';};?>><?php echo $i; ?></option>
								<?php endfor; ?>
								</select> hours 
								<select name="va_end_time_max_length_min">
									<option value="00" <?php if($this->va_settings['end_time_max_length_min'] == '00'){echo 'selected';};?>>00</option>
									<option value="15" <?php if($this->va_settings['end_time_max_length_min'] == '15'){echo 'selected';};?>>15</option>
									<option value="30" <?php if($this->va_settings['end_time_max_length_min'] == '30'){echo 'selected';};?>>30</option>
									<option value="45" <?php if($this->va_settings['end_time_max_length_min'] == '45'){echo 'selected';};?>>45</option>
								</select> minutes
							</p>
							<p>								
								<?php echo $this->va_settings['reservation_single']; ?> Time Interval:<br/><span class="description">(end times available to the user will be in intervals of this setting. make sure it fits evenly between your Min/Max settings above)</span><br/>
								<select name="va_end_time_minmax_interval">
									<option value="0.25" <?php if($this->va_settings['end_time_minmax_interval'] == '0.25'){echo 'selected';};?>>15 minutes</option>
									<option value="0.5" <?php if($this->va_settings['end_time_minmax_interval'] == '0.5'){echo 'selected';};?>>30 minutes</option>
									<option value="0.75" <?php if($this->va_settings['end_time_minmax_interval'] == '0.75'){echo 'selected';};?>>45 minutes</option>
									<option value="1" <?php if($this->va_settings['end_time_minmax_interval'] == '1'){echo 'selected';};?>>1 hour</option>
									<option value="1.25" <?php if($this->va_settings['end_time_minmax_interval'] == '1.25'){echo 'selected';};?>>1 hour 15 minutes</option>
									<option value="1.5" <?php if($this->va_settings['end_time_minmax_interval'] == '1.5'){echo 'selected';};?>>1 hour 30 minutes</option>
									<option value="1.75" <?php if($this->va_settings['end_time_minmax_interval'] == '1.75'){echo 'selected';};?>>1 hour 45 minutes</option>
									<option value="2" <?php if($this->va_settings['end_time_minmax_interval'] == '2'){echo 'selected';};?>>2 hours</option>
									<option value="2.25" <?php if($this->va_settings['end_time_minmax_interval'] == '2.25'){echo 'selected';};?>>2 hours 15 minutes</option>
									<option value="2.5" <?php if($this->va_settings['end_time_minmax_interval'] == '2.5'){echo 'selected';};?>>2 hours 30 minutes</option>
									<option value="2.75" <?php if($this->va_settings['end_time_minmax_interval'] == '2.75'){echo 'selected';};?>>2 hours 45 minutes</option>
									<option value="3" <?php if($this->va_settings['end_time_minmax_interval'] == '3'){echo 'selected';};?>>3 hours</option>
									<option value="3.25" <?php if($this->va_settings['end_time_minmax_interval'] == '3.25'){echo 'selected';};?>>3 hours 15 minutes</option>
									<option value="3.5" <?php if($this->va_settings['end_time_minmax_interval'] == '3.5'){echo 'selected';};?>>3 hours 30 minutes</option>
									<option value="3.75" <?php if($this->va_settings['end_time_minmax_interval'] == '3.75'){echo 'selected';};?>>3 hours 45 minutes</option>
									<option value="4" <?php if($this->va_settings['end_time_minmax_interval'] == '4'){echo 'selected';};?>>4 hours</option>
									<option value="4.25" <?php if($this->va_settings['end_time_minmax_interval'] == '4.25'){echo 'selected';};?>>4 hours 15 minutes</option>
									<option value="4.5" <?php if($this->va_settings['end_time_minmax_interval'] == '4.5'){echo 'selected';};?>>4 hours 30 minutes</option>
									<option value="4.75" <?php if($this->va_settings['end_time_minmax_interval'] == '4.75'){echo 'selected';};?>>4 hours 45 minutes</option>
									<option value="5" <?php if($this->va_settings['end_time_minmax_interval'] == '5'){echo 'selected';};?>>5 hours</option>
								</select>
							</p>
							<p>
								Match Interval to Calendar Intervals<br/>
								<select name="va_match_minmax_interval">
									<option value="no" <?php if($this->va_settings['match_minmax_interval'] == 'no'){echo 'selected';};?>>No</option>
									<option value="yes" <?php if($this->va_settings['match_minmax_interval'] == 'yes'){echo 'selected';};?>>Yes</option>
								</select>
							</p>
						</div>
					</div>
					<br/>
					<h2 class="va-tab-title">Form Field Visibility</h2><hr/>
					<div id="va-show-form-fields">
						<p>
							Display these fields on the <?php echo $this->va_settings['reservation_single']; ?> form:<br/>
							<span class="description">(Name, Email, Date, Start Time & End Time are always required and cannot be hidden)</span>
						</p>
						
						<ul>
						<?php
							if(!empty($this->va_settings['title_label'])){
								$title = $this->va_settings['title_label'];
							}else{
								$title = $this->va_settings['reservation_single'] . ' Title';
							}
							if(!empty($this->va_settings['phone_label'])){
								$phone = $this->va_settings['phone_label'];
							}else{
								$phone = 'Phone';
							}							
							if(!empty($this->va_settings['reservation_type_label'])){
								$type = $this->va_settings['reservation_type_label'];
							}else{
								$type = $this->va_settings['reservation_single'] . ' Type';
							}							
							if(!empty($this->va_settings['description_label'])){
								$description = $this->va_settings['description_label'];
							}else{
								$description = $this->va_settings['reservation_single'] . ' Description';
							}							
							if(!empty($this->va_settings['setup_needs_label'])){
								$setup = $this->va_settings['setup_needs_label'];
							}else{
								$setup = 'Setup Needs';
							}					
							if(!empty($this->va_settings['av_needs_label'])){
								$av = $this->va_settings['av_needs_label'];
							}else{
								$av = 'A/V Tech Needs: (ie. Screen, Projector, Speakers, Microphone, etc.)';
							}
							$fields = array(
								//'end_time' => 'End Time',
								'setup_time' => 'Setup Time',
								'cleanup_time' => 'Cleanup Time',
								'title' => $title,
								'venue' => $this->va_settings['venue_single'],
								'location' => $this->va_settings['location_single'],
								'phone' => $phone,
								'type' => $type,
								'description' => $description,
								'setup_needs' => $setup,
								'av_needs' => $av
							); 
						?>
						<?php foreach($fields as $key => $value) : ?>
							<li>
								<input id="<?php echo $key; ?>" type="checkbox" name="va_show_form_fields[]" value="<?php echo $key; ?>" <?php if(is_array($this->va_settings['show_form_fields'])){if(in_array($key, $this->va_settings['show_form_fields'])){echo 'checked';}}else if($key == $this->va_settings['show_form_fields']){echo 'checked';} ?>/> <label for="<?php echo $key; ?>"><?php echo $value; ?></label>
								<span class="va-clearer"></span>
							</li>
						<?php endforeach; ?>
						</ul>
					</div>
					<br/>
					<h2 class="va-tab-title">Form Field Labels</h2><hr/>
					<p>This allows you to update the labels on the reservation form. Leave blank for default values shown.</p>
					<div class="form-field-labels">
						<p>
							<label><?php echo $this->va_settings['reservation_single']; ?> Title:</label>
							<input type="text" name="va_title_label" value="<?php echo $this->va_settings['title_label'];?>"/>
						</p>					
						<p>
							<label>Your Name:</label>
							<input type="text" name="va_name_label" value="<?php echo $this->va_settings['name_label'];?>"/>
						</p>
						<p>
							<label>Phone:</label>
							<input type="text" name="va_phone_label" value="<?php echo $this->va_settings['phone_label']; ?>"/>
						</p>					
						<p>
							<label>Email:</label>
							<input type="text" name="va_email_label" value="<?php echo $this->va_settings['email_label'];?>"/>
						</p>
						<p>
							<label><?php echo $this->va_settings['reservation_single']; ?> Type:</label>
							<input type="text" name="va_reservation_type_label" value="<?php echo $this->va_settings['reservation_type_label'];?>"/>
						</p>					
						<p>
							<label><?php echo $this->va_settings['reservation_single']; ?> Description:</label>
							<input type="text" name="va_description_label" value="<?php echo $this->va_settings['description_label'];?>"/>
							
						</p>					
						<p>
							<label>Setup Needs:</label>
							<input type="text" name="va_setup_needs_label" value="<?php echo $this->va_settings['setup_needs_label'];?>"/>
						</p>					
						<p>
							<label>A/V Tech Needs: (ie. Screen, Projector, Speakers, Microphone, etc.)</label>
							<input type="text" name="va_av_needs_label" value="<?php echo $this->va_settings['av_needs_label'];?>"/>
						</p>
					</div>
					<br/>
					<input type="hidden" name="va_save_form_settings" />
					<input class="button button-primary" type="submit" name="va_update_settings" value="Update Settings" />
				</form>
			</div>
		<?php elseif($active_tab == 'va-notifications') : ?>
			<div id="va-notifications">
				<form method="post" action="">
					<h2 class="va-tab-title">Notification Settings</h2><hr/>
					<p>These notification settings are for the emails that Vacancy can send when various actions occur. Customize these to your liking.</p>
					<h3>Choose when Notifications are sent:</h3>
					<p>
						<strong>Send admin notification when a new <?php echo $this->va_settings['reservation_single']; ?> is submitted</strong> &nbsp;
						<select name="va_admin_new_notification">
							<option value="yes" <?php if($this->va_settings['admin_new_notification'] == "yes"){echo 'selected';};?>>Yes</option>
							<option value="no" <?php if($this->va_settings['admin_new_notification'] == "no"){echo 'selected';};?>>No</option>
						</select>
					</p>
					<p>
						<strong>Send user notification when a new <?php echo $this->va_settings['reservation_single']; ?> is submitted</strong> &nbsp;
						<select name="va_user_new_notification">
							<option value="yes" <?php if($this->va_settings['user_new_notification'] == "yes"){echo 'selected';};?>>Yes</option>
							<option value="no" <?php if($this->va_settings['user_new_notification'] == "no"){echo 'selected';};?>>No</option>
						</select>
					</p>	
					<p>
						<strong>Send user notification when a <?php echo $this->va_settings['reservation_single']; ?> is approved/denied</strong> &nbsp;
						<select name="va_user_approved_notification">
							<option value="yes" <?php if($this->va_settings['user_approved_notification'] == "yes"){echo 'selected';};?>>Yes</option>
							<option value="no" <?php if($this->va_settings['user_approved_notification'] == "no"){echo 'selected';};?>>No</option>
						</select>
					</p>
					<h3>This is where your Notifications are from:</h3>
					<p>
						<strong>FROM Email name</strong><br/>
						<input type="text" name="va_from_email_name" size="80" value="<?php echo $this->va_settings['from_email_name']; ?>"/>
						<br/><span class="description">the name to appear as who notifications are from</span>
					</p>
					<p>
						<strong>FROM Email address</strong><br/>
						<input type="text" name="va_from_email_address" size="80" value="<?php echo $this->va_settings['from_email_address']; ?>"/>
						<br/><span class="description">this should match your domain to help avoid spam filtering</span>
					</p>	
					<h3>These are where Admin Notifications can go to:</h3>
					<p>This will be chosen by the user when submitting a <?php echo $this->va_settings['reservation_single']; ?>. Leave the second label and email address blank if you only want to give the user one option.</p>
					<table>
					<tr>
						<td>
							<p id="va-admin-email-label-one">
								<strong>Label</strong><br/>
								<input type="text" name="va_admin_email_label_one" size="20" value="<?php echo $this->va_settings['admin_email_label_one']; ?>"/>
								<br/><span class="description"> (i.e. Rentals)</span>
							</p>	
						</td>
						<td>
							<p id="va-admin-email-one">
								<strong>TO email address(es)</strong><br/>
								<input type="text" name="va_admin_email_one" size="54" value="<?php echo $this->va_settings['admin_email_one']; ?>"/>
								<br/><span class="description">comma separate multiple emails</span>
							</p>
						</td>
					</tr>
					<tr>
						<td>
							<p id="va-admin-email-label-two">
								<strong>Label</strong><br/>
								<input type="text" name="va_admin_email_label_two" size="20" value="<?php echo $this->va_settings['admin_email_label_two']; ?>"/>
								<br/><span class="description"> (i.e. Student Groups)</span>
							</p>	
						</td>
						<td>
							<p id="va-admin-email-two">
								<strong>TO email address(es)</strong><br/>
								<input type="text" name="va_admin_email_two" size="54" value="<?php echo $this->va_settings['admin_email_two']; ?>"/>
								<br/><span class="description">comma separate multiple emails</span>
							</p>
						</td>
					</tr>
					</table>
					<h3>This is the content of your Notifications:</h3>
					<p>
						<strong>User notification for new <?php echo $this->va_settings['reservation_single']; ?> subject line</strong><br/>
						<input type="text" name="va_user_subject_line_new" size="80" value="<?php echo $this->va_settings['user_subject_line_new']; ?>"/>
						<br/><span class="description">leave blank for default</span>
					</p>
					<p>
						<strong>User notification for approved/denied <?php echo $this->va_settings['reservation_single']; ?> subject line</strong><br/>
						<input type="text" name="va_user_subject_line_approved" size="80" value="<?php echo $this->va_settings['user_subject_line_approved']; ?>"/>
						<br/><span class="description">leave blank for default</span>
					</p>
					<p>
						<strong>Email Notification Header</strong><br/>
						<textarea name="va_notification_header" rows="8" cols="80"><?php echo get_option('va_notification_header'); ?></textarea>
						<br/><span class="description">leave blank for default</span>
					</p>
					<p>
						<strong>Email Notification Footer</strong><br/>
						<textarea name="va_notification_footer" rows="8" cols="80"><?php echo get_option('va_notification_footer'); ?></textarea>
						<br/><span class="description">Organization contact info will generally go here</span>
					</p>
					<input class="button button-primary" type="submit" name="va_update_settings" value="Update Settings" />
				</form>
			</div>
		<?php elseif($active_tab == 'va-labels') : ?>
			<div id="va-labels">
				<form method="post" action="">
					<h2 class="va-tab-title">Update Labels</h2><hr/>
					<p>This allows you to update all the labels of the content types of Vacancy. We recommended choosing labels that will better clarify your situation. Examples for "Venue/Location/Reservation" might be "Restaurant/Table/Reservation" or "Building/Room/Appointment".</p>
					<br/>
					<p>
						<label>Single Venue label</label>
						<input type="text" name="va_venue_single" value="<?php echo $this->va_settings['venue_single']; ?>"/>
					</p>
					<p>
						<label>Plural Venue label</label>
						<input type="text" name="va_venue_plural" value="<?php echo $this->va_settings['venue_plural'];?>"/>
					</p>
					<p>
						<label>Single Location label</label>
						<input type="text" name="va_location_single" value="<?php echo $this->va_settings['location_single']; ?>"/>
					</p>
					<p>
						<label>Plural Location label</label>
						<input type="text" name="va_location_plural" value="<?php echo $this->va_settings['location_plural'];?>"/>
					</p>
					<p>
						<label>Single Reservation label</label>
						<input type="text" name="va_reservation_single" value="<?php echo $this->va_settings['reservation_single']; ?>"/>
					</p>
					<p>
						<label>Plural Reservation label</label>
						<input type="text" name="va_reservation_plural" value="<?php echo $this->va_settings['reservation_plural'];?>"/>
					</p>
					<input class="button button-primary" type="submit" name="va_update_settings" value="Update Settings" />
				</form>
			</div>		
		<?php elseif($active_tab == 'va-setup-usage') : ?>
			<?php // remove setup nag if notice was clicked ?>
			<?php if(isset($_GET['notice']) && $_GET['notice'] == '1'){update_option('va_setup_usage', 1);}?>
			<div id="va-setup-usage">
				<h2 class="va-tab-title">Setting up and Using Vacancy</h2><hr/>
				<p>Vacancy is a reservation system that is comprised of 3 interacting content types:</p?>
				<br/>
				<h3>The Basics</h3>
				<ul>
					<li><strong><?php echo $this->va_settings['venue_plural']; ?></strong> - These are the overarching places your <?php echo $this->va_settings['location_plural']; ?> will be assigned to. An example might be "The Chamber of Commerce".</li>
					<li><strong><?php echo $this->va_settings['location_plural']; ?></strong> - These are sections of a <?php echo $this->va_settings['venue_single']; ?> will be assigned to. An example might be "Conference Room".</li>
					<li><strong><?php echo $this->va_settings['reservation_plural']; ?></strong> - These are what your users will be creating when they make a reservation. Each <?php echo $this->va_settings['reservation_single']; ?> will be assigned to one or more <?php echo $this->va_settings['location_plural']; ?>.</li>
				</ul>
				<h3>Initial Setup</h3>
				<ul>
					<li>The first thing you'll need to do is <a target="_blank" href="/wp-admin/edit.php?post_type=va_venue">create a <?php echo $this->va_settings['venue_single']; ?></a>. This is done by clicking on the "<?php echo $this->va_settings['venue_plural']; ?>" submenu item under "Vacancy" in the Wordpress admin sidebar and then adding a new post. Give your <?php echo $this->va_settings['venue_single']; ?> a title and complete the meta fields.</li>
					<li>Next you'll need to <a target="_blank" href="/wp-admin/edit.php?post_type=va_location">create a <?php echo $this->va_settings['location_single']; ?></a>. This is done by clicking on the "<?php echo $this->va_settings['location_plural']; ?>" submenu item under "Vacancy" in the Wordpress admin sidebar and then adding a new post. Give your <?php echo $this->va_settings['location_single']; ?> a title and complete the meta fields. Each <?php echo $this->va_settings['location_single']; ?> can use the availability of the <?php echo $this->va_settings['venue_single']; ?> you assign it to, or can use its own availability.</li>
					<li>Once you've created your <?php echo $this->va_settings['venue_plural']; ?> & <?php echo $this->va_settings['location_plural']; ?> you should visit the "General", "Labels", "Forms" and "Notifications" tabs above. Browse through the settings and configure Vacancy to behave the way you want it to.</li>
				</ul>
				<h3>Usage</h3>
				<ul>
					<li>Vacancy is displayed to the users of your site through a shortcode. Simply put the shortcode <code>[vacancy]</code> in any page or post and enjoy!</li>
					<li>Users will submit <?php echo $this->va_settings['reservation_single']; ?> requests from the Vacancy interface generated on the page that you placed the shortcode.</li>
					<li>Submitted requests will be created with a "Pending" status. When viewing the <?php echo $this->va_settings['reservation_single']; ?> request you can change the its status to "Approved" or "Denied". This will help keep your <?php echo $this->va_settings['reservation_single']; ?> system organized and also keep the user informed by sending out notifications of the status change (if configured in the <a href="/wp-admin/admin.php?page=va-settings&tab=va-notifications">Notifications settings tab</a>).</li>
					<li>If you ever need to reorder the way your <?php echo $this->va_settings['location_plural']; ?> display on the front end you can simply set their "menu order" page attribute to order them as you wish.</li>
				</ul>
				<h3>The Calendar View</h3>
				<ul>
					<li>Vacancy has an <a href="/wp-admin/admin.php?page=va-admin-calendar">Administrator Calendar View</a> for you to easily find, view and edit existing <?php echo $this->va_settings['reservation_plural']; ?> or even create new ones. Be careful though, Administrators can create <?php echo $this->va_settings['reservation_plural']; ?> that can conflict with existing <?php echo $this->va_settings['reservation_plural']; ?> or <?php echo $this->va_settings['venue_single']; ?>/<?php echo $this->va_settings['location_single']; ?> availability schedules.</li>
				</ul>
			</div>
		<?php elseif($active_tab == 'va-pro') : ?>
			<?php echo apply_filters('va_pro_settings', ''); ?>
		<?php elseif($active_tab == 'va-ecp') : ?>
			<?php echo apply_filters('va_ecp_settings', ''); ?>
		<?php endif; ?>
	</div>
</div>
<script type="text/javascript">
    jQuery(document).ready(function($){
        $('#va-venue-id').chosen({
            placeholder_text_single: "Select a <?php echo $this->va_settings['venue_single']; ?>"
        });	

		if($('select[name="va_hide_admin_bar"]').val() == 'yes'){
			$('#va-show-admin-bar').show();
		}
		$('select[name="va_hide_admin_bar"]').on('change', function(){
			$('#va-show-admin-bar').slideToggle('fast');
		});

		// show/hide end time types
		var selectedVal = "";
		var selected = $("input[type='radio'][name='va_end_time_type']:checked");
		if(selected.length > 0){
		    selectedVal = selected.val();
		}

		if(selectedVal == 'fixed'){
			$('.reservation-fixed-end-times').fadeIn('fast');
		}else if(selectedVal == 'minmax'){
			$('.reservation-minmax-end-times').fadeIn('fast');
		}

    	// toggle fixed and min/max
    	$('.reservation-end-time-type label').on('click', function(){
			var selectedVal = $('input', this).val();
    		if(selectedVal == 'fixed'){
				$('.reservation-fixed-end-times').fadeIn('fast');
				$('.reservation-minmax-end-times').hide();
			}else if(selectedVal == 'minmax'){
				$('.reservation-minmax-end-times').fadeIn('fast');
				$('.reservation-fixed-end-times').hide();
			}else {
				$('.reservation-fixed-end-times').hide();
				$('.reservation-minmax-end-times').hide();
			}
		});	
    });
</script>
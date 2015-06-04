<div class="va-main-wrap">
	<h1 class="va-page-title"><i class="icon-time"></i> <?php _e('Vacancy Settings'); ?></h1>
	<div id="va-tabs">
		<h2 class="nav-tab-wrapper">
			<?php $active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'va-general'; ?>
			<a href="?page=va-settings&tab=va-general" class="nav-tab <?php echo $active_tab == 'va-general' ? 'nav-tab-active' : ''; ?>"><?php _e('General','vpe');?></a>
			<a href="?page=va-settings&tab=va-labels" class="nav-tab <?php echo $active_tab == 'va-labels' ? 'nav-tab-active' : ''; ?>"><?php _e('Labels','vpe');?></a>
			<a href="?page=va-settings&tab=va-forms" class="nav-tab <?php echo $active_tab == 'va-forms' ? 'nav-tab-active' : ''; ?>"><?php _e('Forms','vpe');?></a>
			<a href="?page=va-settings&tab=va-notifications" class="nav-tab <?php echo $active_tab == 'va-notifications' ? 'nav-tab-active' : ''; ?>"><?php _e('Notifications','vpe');?></a>
			<a href="?page=va-settings&tab=va-setup-usage" class="nav-tab <?php echo $active_tab == 'va-setup-usage' ? 'nav-tab-active' : ''; ?>"><?php _e('Setup & Usage','vpe');?></a>
			<?php echo apply_filters('va_pro_tabs', '', $active_tab); ?>
			<?php echo apply_filters('va_ecp_tabs', '', $active_tab); ?>
			<span id="va-shortcode"><?php _e('To show Vacancy on the frontend use the shortcode','vpe');?> <code>[vacancy]</code></span>
			<span class="va-clearer"></span>
		</h2>
	</div>
	<div id="va-settings-wrap">
		<?php if($active_tab == 'va-general') : ?>
			<div id="va-general">
				<form method="post" action="">
					<h2 class="va-tab-title"><?php _e('General Settings','vpe'); ?></h2><hr/>
					<p><?php _e('Here you can customize the general Vacancy settings to your liking','vpe'); ?>.</p>
					<br/>
					<p>
						<label><?php printf(__('Show %1$s Details','vpe'),$this->va_settings['reservation_single']); ?></label>
						<select name="va_show_reservation_details">
							<option value="yes" <?php if($this->va_settings['show_reservation_details'] == "yes"){echo 'selected';};?>><?php _e('Yes','vpe'); ?></option>
							<option value="no" <?php if($this->va_settings['show_reservation_details'] == "no"){echo 'selected';};?>><?php _e('No','vpe'); ?></option>
						</select>
					</p>					
					<p>
						<label><?php _e('Require Login','vpe'); ?></label>
						<select name="va_require_login">
							<option value="yes" <?php if($this->va_settings['require_login'] == "yes"){echo 'selected';};?>><?php _e('Yes','vpe'); ?></option>
							<option value="no" <?php if($this->va_settings['require_login'] == "no"){echo 'selected';};?>><?php _e('No','vpe'); ?></option>
						</select>
					</p>
					<p>
						<label><?php _e('Hide WP admin bar','vpe'); ?></label>
						<select name="va_hide_admin_bar">
							<option value="yes" <?php if($this->va_settings['hide_admin_bar'] == "yes"){echo 'selected';};?>><?php _e('Yes','vpe'); ?></option>
							<option value="no" <?php if($this->va_settings['hide_admin_bar'] == "no"){echo 'selected';};?>><?php _e('No','vpe'); ?></option>
						</select>
						<div id="va-show-admin-bar" style="display:none;">
							<p><?php _e('BUT, Still Show WP admin bar for','vpe'); ?></p>
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
						<label><?php printf(__('Default %1$s','vpe'),$this->va_settings['venue_single']); ?></label>
						<?php $venues = $this->va_get_venues(); ?>
						<?php if($venues->have_posts()) : ?>
						<select id="va-venue-id" name="va_default_venue">
							<?php while($venues->have_posts()) : $venues->the_post(); ?>
								<option value="<?php the_ID(); ?>" <?php if(get_option('va_default_venue') == get_the_ID()){echo 'selected';} ?>><?php the_title(); ?></option>
							<?php endwhile; ?>
						</select>
						<?php else : ?>
							<p><?php printf(__('No %1$s have been created yet','vpe'),$this->va_settings['venue_plural']); ?>. <a href="/wp-admin/post-new.php?post_type=va_venue"><?php printf(__('Create new %1$s','vpe'),$this->va_settings['venue_single']); ?></a>
						<?php endif; ?>
					</p>
					<p>
						<label><?php _e('Day View Start Time','vpe'); ?></label>
						<?php echo $this->va_get_time_select('va_day_start_time',$this->va_settings['day_start_time']); ?>
					</p>
					<p>
						<label><?php _e('Day View End Time','vpe'); ?></label>
						<?php echo $this->va_get_time_select('va_day_end_time',$this->va_settings['day_end_time']); ?>
					</p>
					<p>
						<label><?php _e('Use Setup/Cleanup Times','vpe'); ?>?</label>
						<select name="va_setup_cleanup">
							<option value="yes" <?php if($this->va_settings['setup_cleanup'] == 'yes'){echo 'selected';}?>><?php _e('Yes','vpe'); ?></option>
							<option value="no" <?php if($this->va_settings['setup_cleanup'] == 'no'){echo 'selected';}?>><?php _e('No','vpe'); ?></option>
						</select>

					</p>
					<p>
						<?php printf(__('Message to display after successful %1$s submission','vpe'),$this->va_settings['reservation_single']); ?><br/>
						<input type="text" name="va_reservation_success_message" size="80" value="<?php echo $this->va_settings['reservation_success_message']; ?>"/>
					</p>
					<input class="button button-primary" type="submit" name="va_update_settings" value="Update Settings" />
				</form>
			</div>
		<?php elseif($active_tab == 'va-forms') : ?>
			<div id="va-forms">
				<form method="post" action="">
					<h2 class="va-tab-title"><?php printf(__('%1$s End Time Options','vpe'),$this->va_settings['reservation_single']); ?></h2><hr/>
					<div id="va-end-time-options">
						<div class="reservation-end-time-type">
							<p>
								<label style="width:auto;"><input id="va-standard-end-time" type="radio" name="va_end_time_type" value="standard" <?php if($this->va_settings['end_time_type'] == 'standard'){echo 'checked';};?> /> <?php _e('Standard End Time Selection (any length)','vpe'); ?></label><br/>
								<label style="width:auto;"><input id="va-fixed-end-time" type="radio" name="va_end_time_type" value="fixed" <?php if($this->va_settings['end_time_type'] == 'fixed'){echo 'checked';};?> /> <?php printf(__('Fixed %1$s Length','vpe'), $this->va_settings['reservation_single']); ?></label><br/>
								<label style="width:auto;"><input id="va-minmax-end-time" type="radio" name="va_end_time_type" value="minmax" <?php if($this->va_settings['end_time_type'] == 'minmax'){echo 'checked';};?> /> <?php printf(__('Min/Max %1$s Length Selection','vpe'),$this->va_settings['reservation_single']); ?></label>
							</p>
						</div>
						<span class="va-clearer"></span>
						<div class="reservation-fixed-end-times" style="display:none;">
							<p>
								<?php printf(__('%1$s last for','vpe'),$this->va_settings['reservation_plural']); ?>:<br/>
								<select name="va_end_time_length_hr">
								<?php for($i=0;$i<24;$i++) : ?>
									<option value="<?php echo $i; ?>" <?php if($this->va_settings['end_time_length_hr'] == $i){echo 'selected';};?>><?php echo $i; ?></option>
								<?php endfor; ?>
								</select> <?php _e('hours','vpe'); ?> 
								<select name="va_end_time_length_min">
									<option value="00" <?php if($this->va_settings['end_time_length_min'] == '00'){echo 'selected';};?>>00</option>
									<option value="15" <?php if($this->va_settings['end_time_length_min'] == '15'){echo 'selected';};?>>15</option>
									<option value="30" <?php if($this->va_settings['end_time_length_min'] == '30'){echo 'selected';};?>>30</option>
									<option value="45" <?php if($this->va_settings['end_time_length_min'] == '45'){echo 'selected';};?>>45</option>
								</select> <?php _e('minutes','vpe'); ?>
							</p>
						</div>	
						<span class="va-clearer"></span>				
						<div class="reservation-minmax-end-times" style="display:none;">
							<p>
								<?php printf(__('Minimum %1$s Length','vpe'),$this->va_settings['reservation_single']); ?>:<br/><span class="description">(<?php _e('if a user chooses a 1:00pm start time and this setting is 30 minutes, the first available end time will be 1:30pm','vpe');?>)</span><br/>
								<select name="va_end_time_min_length_hr">
								<?php for($i=0;$i<24;$i++) : ?>
									<option value="<?php echo $i; ?>" <?php if($this->va_settings['end_time_min_length_hr'] == $i){echo 'selected';};?>><?php echo $i; ?></option>
								<?php endfor; ?>
								</select> <?php _e('hours','vpe'); ?>
								<select name="va_end_time_min_length_min">
									<option value="00" <?php if($this->va_settings['end_time_min_length_min'] == '00'){echo 'selected';};?>>00</option>
									<option value="15" <?php if($this->va_settings['end_time_min_length_min'] == '15'){echo 'selected';};?>>15</option>
									<option value="30" <?php if($this->va_settings['end_time_min_length_min'] == '30'){echo 'selected';};?>>30</option>
									<option value="45" <?php if($this->va_settings['end_time_min_length_min'] == '45'){echo 'selected';};?>>45</option>
								</select> <?php _e('minutes','vpe'); ?>
							</p>							
							<p>
								<?php printf(__('Maximum %1$s Length','vpe'),$this->va_settings['reservation_single']); ?>:<br/><span class="description">(<?php _e('if a user chooses a 1:00pm start time and this setting is 2 hours, the last available end time will be 3:00pm','vpe'); ?>)</span><br/>
								<select name="va_end_time_max_length_hr">
								<?php for($i=0;$i<24;$i++) : ?>
									<option value="<?php echo $i; ?>" <?php if($this->va_settings['end_time_max_length_hr'] == $i){echo 'selected';};?>><?php echo $i; ?></option>
								<?php endfor; ?>
								</select> <?php _e('hours','vpe'); ?> 
								<select name="va_end_time_max_length_min">
									<option value="00" <?php if($this->va_settings['end_time_max_length_min'] == '00'){echo 'selected';};?>>00</option>
									<option value="15" <?php if($this->va_settings['end_time_max_length_min'] == '15'){echo 'selected';};?>>15</option>
									<option value="30" <?php if($this->va_settings['end_time_max_length_min'] == '30'){echo 'selected';};?>>30</option>
									<option value="45" <?php if($this->va_settings['end_time_max_length_min'] == '45'){echo 'selected';};?>>45</option>
								</select> <?php _e('minutes','vpe'); ?>
							</p>
							<p>								
								<?php printf(__('%1$s Time Interval','vpe'),$this->va_settings['reservation_single']); ?>:<br/><span class="description">(<?php _e('end times available to the user will be in intervals of this setting. make sure it fits evenly between your Min/Max settings above','vpe'); ?>)</span><br/>
								<select name="va_end_time_minmax_interval">
									<option value="0.25" <?php if($this->va_settings['end_time_minmax_interval'] == '0.25'){echo 'selected';};?>><?php _e('15 minutes','vpe'); ?></option>
									<option value="0.5" <?php if($this->va_settings['end_time_minmax_interval'] == '0.5'){echo 'selected';};?>><?php _e('30 minutes','vpe'); ?></option>
									<option value="0.75" <?php if($this->va_settings['end_time_minmax_interval'] == '0.75'){echo 'selected';};?>><?php _e('45 minutes','vpe'); ?></option>
									<option value="1" <?php if($this->va_settings['end_time_minmax_interval'] == '1'){echo 'selected';};?>><?php _e('1 hour','vpe'); ?></option>
									<option value="1.25" <?php if($this->va_settings['end_time_minmax_interval'] == '1.25'){echo 'selected';};?>><?php _e('1 hour 15 minutes','vpe'); ?></option>
									<option value="1.5" <?php if($this->va_settings['end_time_minmax_interval'] == '1.5'){echo 'selected';};?>><?php _e('1 hour 30 minutes','vpe'); ?></option>
									<option value="1.75" <?php if($this->va_settings['end_time_minmax_interval'] == '1.75'){echo 'selected';};?>><?php _e('1 hour 45 minutes','vpe'); ?></option>
									<option value="2" <?php if($this->va_settings['end_time_minmax_interval'] == '2'){echo 'selected';};?>><?php _e('2 hours','vpe'); ?></option>
									<option value="2.25" <?php if($this->va_settings['end_time_minmax_interval'] == '2.25'){echo 'selected';};?>><?php _e('2 hours 15 minutes','vpe'); ?></option>
									<option value="2.5" <?php if($this->va_settings['end_time_minmax_interval'] == '2.5'){echo 'selected';};?>><?php _e('2 hours 30 minutes','vpe'); ?></option>
									<option value="2.75" <?php if($this->va_settings['end_time_minmax_interval'] == '2.75'){echo 'selected';};?>><?php _e('2 hours 45 minutes','vpe'); ?></option>
									<option value="3" <?php if($this->va_settings['end_time_minmax_interval'] == '3'){echo 'selected';};?>><?php _e('3 hours','vpe'); ?></option>
									<option value="3.25" <?php if($this->va_settings['end_time_minmax_interval'] == '3.25'){echo 'selected';};?>><?php _e('3 hours 15 minutes','vpe'); ?></option>
									<option value="3.5" <?php if($this->va_settings['end_time_minmax_interval'] == '3.5'){echo 'selected';};?>><?php _e('3 hours 30 minutes','vpe'); ?></option>
									<option value="3.75" <?php if($this->va_settings['end_time_minmax_interval'] == '3.75'){echo 'selected';};?>><?php _e('3 hours 45 minutes','vpe'); ?></option>
									<option value="4" <?php if($this->va_settings['end_time_minmax_interval'] == '4'){echo 'selected';};?>><?php _e('4 hours','vpe'); ?></option>
									<option value="4.25" <?php if($this->va_settings['end_time_minmax_interval'] == '4.25'){echo 'selected';};?>><?php _e('4 hours 15 minutes','vpe'); ?></option>
									<option value="4.5" <?php if($this->va_settings['end_time_minmax_interval'] == '4.5'){echo 'selected';};?>><?php _e('4 hours 30 minutes','vpe'); ?></option>
									<option value="4.75" <?php if($this->va_settings['end_time_minmax_interval'] == '4.75'){echo 'selected';};?>><?php _e('4 hours 45 minutes','vpe'); ?></option>
									<option value="5" <?php if($this->va_settings['end_time_minmax_interval'] == '5'){echo 'selected';};?>><?php _e('5 hours','vpe'); ?></option>
								</select>
							</p>
							<p>
								<?php _e('Match Interval to Calendar Intervals','vpe'); ?><br/>
								<select name="va_match_minmax_interval">
									<option value="no" <?php if($this->va_settings['match_minmax_interval'] == 'no'){echo 'selected';};?>><?php _e('No','vpe'); ?></option>
									<option value="yes" <?php if($this->va_settings['match_minmax_interval'] == 'yes'){echo 'selected';};?>><?php _e('Yes','vpe'); ?></option>
								</select>
							</p>
						</div>
					</div>
					<br/>
					<h2 class="va-tab-title"><?php _e('Form Field Visibility','vpe'); ?></h2><hr/>
					<div id="va-show-form-fields">
						<p>
							<?php printf(__('Display these fields on the %1$s form','vpe'),$this->va_settings['reservation_single']); ?>:<br/>
							<span class="description">(<?php _e('Name, Email, Date, Start Time & End Time are always required and cannot be hidden','vpe'); ?>)</span>
						</p>
						
						<ul>
						<?php
							if(!empty($this->va_settings['title_label'])){
								$title = $this->va_settings['title_label'];
							}else{
								$title = sprintf(__('%1$s Title','vpe'),$this->va_settings['reservation_single']);
							}
							if(!empty($this->va_settings['phone_label'])){
								$phone = $this->va_settings['phone_label'];
							}else{
								$phone = __('Phone','vpe');
							}							
							if(!empty($this->va_settings['reservation_type_label'])){
								$type = $this->va_settings['reservation_type_label'];
							}else{
								$type = sprintf(__('%1$s Type','vpe'),$this->va_settings['reservation_single']);
							}							
							if(!empty($this->va_settings['description_label'])){
								$description = $this->va_settings['description_label'];
							}else{
								$description = sprintf(__('%1$s Description','vpe'),$this->va_settings['reservation_single']);
							}							
							if(!empty($this->va_settings['setup_needs_label'])){
								$setup = $this->va_settings['setup_needs_label'];
							}else{
								$setup = __('Setup Needs','vpe');
							}					
							if(!empty($this->va_settings['av_needs_label'])){
								$av = $this->va_settings['av_needs_label'];
							}else{
								$av = __('A/V Tech Needs: (ie. Screen, Projector, Speakers, Microphone, etc.)','vpe');
							}
							$fields = array(
								//'end_time' => 'End Time',
								'setup_time' => __('Setup Time','vpe'),
								'cleanup_time' => __('Cleanup Time','vpe'),
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
					<h2 class="va-tab-title"><?php _e('Form Field Labels','vpe'); ?></h2><hr/>
					<p><?php _e('This allows you to update the labels on the reservation form','vpe'); ?>. <?php _e('Leave blank for default values shown','vpe'); ?>.</p>
					<div class="form-field-labels">
						<p>
							<label><?php printf(__('%1$s Title','vpe'),$this->va_settings['reservation_single']); ?>:</label>
							<input type="text" name="va_title_label" value="<?php echo $this->va_settings['title_label'];?>"/>
						</p>					
						<p>
							<label><?php _e('Your Name','vpe'); ?>:</label>
							<input type="text" name="va_name_label" value="<?php echo $this->va_settings['name_label'];?>"/>
						</p>
						<p>
							<label><?php _e('Phone','vpe'); ?>:</label>
							<input type="text" name="va_phone_label" value="<?php echo $this->va_settings['phone_label']; ?>"/>
						</p>					
						<p>
							<label><?php _e('Email','vpe'); ?>:</label>
							<input type="text" name="va_email_label" value="<?php echo $this->va_settings['email_label'];?>"/>
						</p>
						<p>
							<label><?php printf(__('%1$s Type','vpe'),$this->va_settings['reservation_single']); ?>:</label>
							<input type="text" name="va_reservation_type_label" value="<?php echo $this->va_settings['reservation_type_label'];?>"/>
						</p>					
						<p>
							<label><?php printf(__('%1$s Description'),$this->va_settings['reservation_single']); ?>:</label>
							<input type="text" name="va_description_label" value="<?php echo $this->va_settings['description_label'];?>"/>
						</p>					
						<p>
							<label><?php _e('Setup Needs','vpe'); ?>:</label>
							<input type="text" name="va_setup_needs_label" value="<?php echo $this->va_settings['setup_needs_label'];?>"/>
						</p>					
						<p>
							<label><?php _e('A/V Tech Needs: (ie. Screen, Projector, Speakers, Microphone, etc.)','vpe'); ?></label>
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
					<h2 class="va-tab-title"><?php _e('Notification Settings','vpe'); ?></h2><hr/>
					<p><?php _e('These notification settings are for the emails that Vacancy can send when various actions occur','vpe'); ?>. <?php _e('Customize these to your liking','vpe'); ?>.</p>
					<h3><?php _e('Choose when Notifications are sent','vpe'); ?>:</h3>
					<p>
						<strong><?php printf(__('Send admin notification when a new %1$s is submitted','vpe'),$this->va_settings['reservation_single']); ?></strong> &nbsp;
						<select name="va_admin_new_notification">
							<option value="yes" <?php if($this->va_settings['admin_new_notification'] == "yes"){echo 'selected';};?>><?php _e('Yes','vpe'); ?></option>
							<option value="no" <?php if($this->va_settings['admin_new_notification'] == "no"){echo 'selected';};?>><?php _e('No','vpe'); ?></option>
						</select>
					</p>
					<p>
						<strong><?php printf(__('Send user notification when a new %1$s is submitted','vpe'),$this->va_settings['reservation_single']); ?></strong> &nbsp;
						<select name="va_user_new_notification">
							<option value="yes" <?php if($this->va_settings['user_new_notification'] == "yes"){echo 'selected';};?>><?php _e('Yes','vpe'); ?></option>
							<option value="no" <?php if($this->va_settings['user_new_notification'] == "no"){echo 'selected';};?>><?php _e('No','vpe'); ?></option>
						</select>
					</p>	
					<p>
						<strong><?php printf(__('Send user notification when a %1$s is approved/denied','vpe'),$this->va_settings['reservation_single']); ?></strong> &nbsp;
						<select name="va_user_approved_notification">
							<option value="yes" <?php if($this->va_settings['user_approved_notification'] == "yes"){echo 'selected';};?>><?php _e('Yes','vpe'); ?></option>
							<option value="no" <?php if($this->va_settings['user_approved_notification'] == "no"){echo 'selected';};?>><?php _e('No','vpe'); ?></option>
						</select>
					</p>
					<h3><?php _e('This is where your Notifications are from','vpe'); ?>:</h3>
					<p>
						<strong><?php _e('FROM Email name','vpe'); ?></strong><br/>
						<input type="text" name="va_from_email_name" size="80" value="<?php echo $this->va_settings['from_email_name']; ?>"/>
						<br/><span class="description"><?php _e('the name to appear as who notifications are from','vpe'); ?></span>
					</p>
					<p>
						<strong><?php _e('FROM Email address','vpe'); ?></strong><br/>
						<input type="text" name="va_from_email_address" size="80" value="<?php echo $this->va_settings['from_email_address']; ?>"/>
						<br/><span class="description"><?php _e('this should match your domain to help avoid spam filtering','vpe'); ?></span>
					</p>	
					<h3><?php _e('These are where Admin Notifications can go to','vpe'); ?>:</h3>
					<p><?php printf(__('This will be chosen by the user when submitting a %1$s','vpe'),$this->va_settings['reservation_single']); ?>. <?php _e('Leave the second label and email address blank if you only want to give the user one option','vpe'); ?>.</p>
					<table>
					<tr>
						<td>
							<p id="va-admin-email-label-one">
								<strong><?php _e('Label','vpe'); ?></strong><br/>
								<input type="text" name="va_admin_email_label_one" size="20" value="<?php echo $this->va_settings['admin_email_label_one']; ?>"/>
								<br/><span class="description"> (<?php _e('i.e. Rentals','vpe'); ?>)</span>
							</p>	
						</td>
						<td>
							<p id="va-admin-email-one">
								<strong><?php _e('TO email address(es)','vpe'); ?></strong><br/>
								<input type="text" name="va_admin_email_one" size="54" value="<?php echo $this->va_settings['admin_email_one']; ?>"/>
								<br/><span class="description"><?php _e('comma separate multiple emails','vpe'); ?></span>
							</p>
						</td>
					</tr>
					<tr>
						<td>
							<p id="va-admin-email-label-two">
								<strong><?php _e('Label','vpe'); ?></strong><br/>
								<input type="text" name="va_admin_email_label_two" size="20" value="<?php echo $this->va_settings['admin_email_label_two']; ?>"/>
								<br/><span class="description"> (<?php _e('i.e. Student Groups','vpe'); ?>)</span>
							</p>	
						</td>
						<td>
							<p id="va-admin-email-two">
								<strong><?php _e('TO email address(es)','vpe'); ?></strong><br/>
								<input type="text" name="va_admin_email_two" size="54" value="<?php echo $this->va_settings['admin_email_two']; ?>"/>
								<br/><span class="description"><?php _e('comma separate multiple emails','vpe'); ?></span>
							</p>
						</td>
					</tr>
					</table>
					<h3><?php _e('This is the content of your Notifications','vpe'); ?>:</h3>
					<p>
						<strong><?php printf(__('User notification for new %1$s subject line','vpe'),$this->va_settings['reservation_single']); ?></strong><br/>
						<input type="text" name="va_user_subject_line_new" size="80" value="<?php echo $this->va_settings['user_subject_line_new']; ?>"/>
						<br/><span class="description"><?php _e('leave blank for default','vpe'); ?></span>
					</p>
					<p>
						<strong><?php printf(__('User notification for approved/denied %1$s subject line','vpe'),$this->va_settings['reservation_single']); ?></strong><br/>
						<input type="text" name="va_user_subject_line_approved" size="80" value="<?php echo $this->va_settings['user_subject_line_approved']; ?>"/>
						<br/><span class="description"><?php _e('leave blank for default','vpe'); ?></span>
					</p>
					<p>
						<strong><?php _e('Email Notification Header','vpe'); ?></strong><br/>
						<textarea name="va_notification_header" rows="8" cols="80"><?php echo get_option('va_notification_header'); ?></textarea>
						<br/><span class="description"><?php _e('leave blank for default','vpe'); ?></span>
					</p>
					<p>
						<strong><?php _e('Email Notification Footer','vpe'); ?></strong><br/>
						<textarea name="va_notification_footer" rows="8" cols="80"><?php echo get_option('va_notification_footer'); ?></textarea>
						<br/><span class="description"><?php _e('Organization contact info will generally go here','vpe'); ?></span>
					</p>
					<input class="button button-primary" type="submit" name="va_update_settings" value="Update Settings" />
				</form>
			</div>
		<?php elseif($active_tab == 'va-labels') : ?>
			<div id="va-labels">
				<form method="post" action="">
					<h2 class="va-tab-title"><?php _e('Update Labels','vpe'); ?></h2><hr/>
					<p><?php _e('This allows you to update all the labels of the content types of Vacancy','vpe'); ?>. <?php _e('We recommended choosing labels that will better clarify your situation','vpe'); ?>. <?php _e('Examples for "Venue/Location/Reservation" might be "Restaurant/Table/Reservation" or "Building/Room/Appointment"','vpe'); ?>.</p>
					<br/>
					<p>
						<label><?php _e('Single Venue label','vpe'); ?></label>
						<input type="text" name="va_venue_single" value="<?php echo $this->va_settings['venue_single']; ?>"/>
					</p>
					<p>
						<label><?php _e('Plural Venue label','vpe'); ?></label>
						<input type="text" name="va_venue_plural" value="<?php echo $this->va_settings['venue_plural'];?>"/>
					</p>
					<p>
						<label><?php _e('Single Location label','vpe'); ?></label>
						<input type="text" name="va_location_single" value="<?php echo $this->va_settings['location_single']; ?>"/>
					</p>
					<p>
						<label><?php _e('Plural Location label','vpe'); ?></label>
						<input type="text" name="va_location_plural" value="<?php echo $this->va_settings['location_plural'];?>"/>
					</p>
					<p>
						<label><?php _e('Single Reservation label','vpe'); ?></label>
						<input type="text" name="va_reservation_single" value="<?php echo $this->va_settings['reservation_single']; ?>"/>
					</p>
					<p>
						<label><?php _e('Plural Reservation label','vpe'); ?></label>
						<input type="text" name="va_reservation_plural" value="<?php echo $this->va_settings['reservation_plural'];?>"/>
					</p>
					<input class="button button-primary" type="submit" name="va_update_settings" value="Update Settings" />
				</form>
			</div>		
		<?php elseif($active_tab == 'va-setup-usage') : ?>
			<?php // remove setup nag if notice was clicked ?>
			<?php if(isset($_GET['notice']) && $_GET['notice'] == '1'){update_option('va_setup_usage', 1);}?>
			<div id="va-setup-usage">
				<h2 class="va-tab-title"><?php _e('Setting up and Using Vacancy','vpe'); ?></h2><hr/>
				<p><?php _e('Vacancy is a reservation system that is comprised of 3 interacting content types','vpe'); ?>:</p?>
				<br/>
				<h3><?php _e('The Basics','vpe'); ?></h3>
				<ul>
					<li><strong><?php echo $this->va_settings['venue_plural']; ?></strong> - <?php printf(__('These are the overarching places your %1$s will be assigned to','vpe'),$this->va_settings['location_plural']); ?>. <?php _e('An example might be "The Chamber of Commerce"','vpe'); ?>.</li>
					<li><strong><?php echo $this->va_settings['location_plural']; ?></strong> - <?php printf(__('These are sections of a %1$s will be assigned to','vpe'),$this->va_settings['venue_single']); ?>. <?php _e('An example might be "Conference Room"','vpe'); ?>.</li>
					<li><strong><?php echo $this->va_settings['reservation_plural']; ?></strong> - <?php _e('These are what your users will be creating when they make a reservation','vpe'); ?>. <?php printf(__('Each %1$s will be assigned to one or more %2$s','vpe'),$this->va_settings['reservation_single'],$this->va_settings['location_plural']); ?>.</li>
				</ul>
				<h3><?php _e('Initial Setup','vpe'); ?></h3>
				<ul>
					<li><?php printf(__('The first thing you\'ll need to do is %1$screate a%2$s','vpe'),'<a target="_blank" href="/wp-admin/edit.php?post_type=va_venue">',$this->va_settings['venue_single']); ?></a>. <?php printf(__('This is done by clicking on the "%1$s" submenu item under "Vacancy" in the Wordpress admin sidebar and then adding a new post','vpe'),$this->va_settings['venue_plural']); ?>. <?php printf(__('Give your %1$s a title and complete the meta fields','vpe'),$this->va_settings['venue_single']); ?>.</li>
					<li><?php printf(__('Next you\'ll need to %1$screate a %2$s','vpe'),'<a target="_blank" href="/wp-admin/edit.php?post_type=va_location">',$this->va_settings['location_single']); ?></a>. <?php printf(__('This is done by clicking on the "%1$s" submenu item under "Vacancy" in the Wordpress admin sidebar and then adding a new post','vpe'),$this->va_settings['location_plural']); ?>. <?php printf(__('Give your %1$s a title and complete the meta fields','vpe'),$this->va_settings['location_single']); ?>. <?php printf(__('Each %1$s can use the availability of the %2$s you assign it to, or can use its own availability','vpe'),$this->va_settings['location_single'],$this->va_settings['venue_single']); ?>.</li>
					<li><?php printf(__('Once you\'ve created your %1$s & %2$s you should visit the "General", "Labels", "Forms" and "Notifications" tabs above','vpe'),$this->va_settings['venue_plural'],$this->va_settings['location_plural']); ?>. <?php _e('Browse through the settings and configure Vacancy to behave the way you want it to','vpe'); ?>.</li>
				</ul>
				<h3><?php _e('Usage','vpe'); ?></h3>
				<ul>
					<li><?php _e('Vacancy is displayed to the users of your site through a shortcode','vpe'); ?>. <?php printf(__('Simply put the shortcode %1$s in any page or post and enjoy','vpe'),'<code>[vacancy]</code>'); ?>!</li>
					<li><?php printf(__('Users will submit %1$s requests from the Vacancy interface generated on the page that you placed the shortcode','vpe'),$this->va_settings['reservation_single']); ?>.</li>
					<li><?php _e('Submitted requests will be created with a "Pending" status','vpe'); ?>. <?php printf(__('When viewing the %1$s request you can change the its status to "Approved" or "Denied"','vpe'),$this->va_settings['reservation_single']); ?>. <?php printf(__('This will help keep your %1$s system organized and also keep the user informed by sending out notifications of the status change (if configured in the %2$sNotifications settings tab%3$s','vpe'),$this->va_settings['reservation_single'],'<a href="/wp-admin/admin.php?page=va-settings&tab=va-notifications">','</a>)'); ?>.</li>
					<li><?php printf(__('If you ever need to reorder the way your %1$s display on the front end you can simply set their "menu order" page attribute to order them as you wish','vpe'),$this->va_settings['location_plural']); ?>.</li>
				</ul>
				<h3><?php _e('The Calendar View','vpe'); ?></h3>
				<ul>
					<li><?php printf(__('Vacancy has an %1$sAdministrator Calendar View%2$s for you to easily find, view and edit existing %3$s or even create new ones','vpe'),'<a href="/wp-admin/admin.php?page=va-admin-calendar">','</a>',$this->va_settings['reservation_plural']); ?>. <?php printf(__('Be careful though, Administrators can create %1$s that can conflict with existing %2$s or %3$s/%4$s availability schedules','vpe'),$this->va_settings['reservation_plural'],$this->va_settings['reservation_plural'],$this->va_settings['venue_single'],$this->va_settings['location_single']); ?>.</li>
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
            placeholder_text_single: "<?php printf(__('Select a %1$s','vpe'),$this->va_settings['venue_single']); ?>"
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
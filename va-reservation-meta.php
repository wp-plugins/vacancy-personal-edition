<?php global $post; ?>
<?php $reservation = $post; ?>
<?php wp_nonce_field('save_reservation_meta', 'reservation_meta_nonce'); ?>
<div>
    <div id="va-fields">
        <p>
            <?php echo apply_filters('va_ecp_status_note', null, $post->ID); ?>
            <label><?php printf(__('%1$s status','vpe'),$this->va_settings['reservation_single']); ?></label>
            <select id="va-reservation-status" name="va_reservation_status">
                <?php $stauses = array('Pending', 'Approved', 'Denied', 'Blocked'); ?>
                <?php foreach($stauses as $status) : ?>
                    <option value="<?php echo strtolower($status); ?>" 
                        <?php if(get_post_meta($reservation->ID, 'va_reservation_status', true) == strtolower($status)){echo 'selected';} ?>
                    ><?php echo $status; ?></option>
                <?php endforeach; ?>
            </select><br/><span class="description"><?php printf(__('TIP: \'Denied\' %1$s won\'t be displayed on the calendar. \'Blocked\' %2$s will appear on the calendar as unavailable but not styled','vpe'),$this->va_settings['reservation_plural'],$this->va_settings['reservation_plural']); ?>.</span><br/>
            <br/><label><?php printf(__('Comments %1$s(sent to the submittor via %2$s status change notification)','vpe'),'<em>',$this->va_settings['reservation_single']); ?></em></label></br/><textarea name="va_reservation_comments" rows="6" cols="80"><?php echo get_post_meta($reservation->ID, 'va_reservation_comments', true); ?></textarea>
        </p>
        <h3><?php echo $this->va_settings['venue_single']; ?> and <?php echo $this->va_settings['location_single']; ?></h3>
        <?php $venue_id; ?>
        <?php $venues = $this->va_get_venues(); ?>
        <?php if($venues->have_posts()) : ?>
        <p>
            <label><?php printf(__('Which %1$s is this %2$s for','vpe'),$this->va_settings['venue_single'],$this->va_settings['reservation_single']); ?>?</label>
            <select id="va-venue-id" name="va_venue_id">
            <?php while($venues->have_posts()) : $venues->the_post(); ?>
                <option value="<?php the_ID();?>" 
                    <?php 
                        if(isset($_GET['va_venue_id'])){
                            if(sanitize_text_field($_GET['va_venue_id']) == get_the_ID()){echo 'selected';}
                        }else{
                            if($venue_id = get_post_meta($reservation->ID, 'va_venue_id', true)){
                                if($venue_id == get_the_ID()){echo 'selected';}
                            }else if(get_option('va_default_venue') == get_the_ID()){
                                echo 'selected';
                                $venue_id = get_the_ID();
                            } 
                        }
                    ?>
                ><?php the_title(); ?></option>
            <?php endwhile; ?>
            </select>
        </p>
        <?php endif; ?>  
        <div id="va-locations">
            <?php 
                $args = array(
                    'post_type' => 'va_location',
                    'posts_per_page' => -1,
                    'meta_key' => 'va_venue_id',
                    'meta_value' => $venue_id
                );
                $locations = new WP_Query($args);
            ?>
            <?php if($locations->have_posts()) : ?>
            <p>
                <label><?php printf(__('This %1$s is for which %2$s','vpe'),$this->va_settings['reservation_single'],$this->va_settings['location_plural']); ?>? </label>
                <select id="va-location-id" name="va_location_id[]" multiple>
                <option></option>
                <?php while($locations->have_posts()) : $locations->the_post(); ?>
                    <option value="<?php the_ID();?>" 
                        <?php 
                            if(isset($_GET['va_location_id'])){
                                if(sanitize_text_field($_GET['va_location_id']) == get_the_ID()){
                                    echo "selected";
                                }
                            }else{
                                $location_ids = get_post_meta($reservation->ID, 'va_location_id', true);
                                if(!empty($location_ids)){
                                    if(is_array($location_ids)){
                                        if(in_array(get_the_ID(), $location_ids)){
                                            echo "selected";
                                        }
                                    }
                                    else if(get_the_ID() == $location_ids){
                                        echo "selected";
                                    }
                                } 
                            }
                        ?>
                    ><?php the_title(); ?></option>
                <?php endwhile; ?>
                </select>
            </p>
            <?php endif; ?>
        </div>
        <p>
            <label><?php _e('Date','vpe'); ?></label> <input id="va-reservation-datepicker" type="text" value="<?php if(isset($_GET['va_reservation_date'])){echo date('m/d/Y', strtotime(sanitize_text_field($_GET['va_reservation_date'])));}elseif(get_post_meta($reservation->ID, 'va_reservation_date', true)){echo date('m/d/Y', strtotime(get_post_meta($reservation->ID, 'va_reservation_date', true)));} ?>" />
            <input id="va-reservation-date" name="va_reservation_date" type="hidden" value="<?php if(isset($_GET['va_reservation_date'])){echo sanitize_text_field($_GET['va_reservation_date']);}elseif(get_post_meta($reservation->ID, 'va_reservation_date', true)){echo get_post_meta($reservation->ID, 'va_reservation_date', true);} ?>"/>
        </p>
        <p>
            <label><?php printf(__('Setup Start Time (before %1$s)','vpe'),$this->va_settings['reservation_single']); ?></label>
            <?php $value = get_post_meta($reservation->ID, 'va_start_setup_time', true); ?>
            <?php echo $this->va_get_time_select('va_start_setup_time', $value); ?>
        </p> 
        <p>
            <label><?php _e('Start Time','vpe'); ?></label>
            <?php if(isset($_GET['va_start_time'])){$value = sanitize_text_field($_GET['va_start_time']);}else{$value = get_post_meta($reservation->ID, 'va_start_time', true);} ?>
            <?php echo $this->va_get_time_select('va_start_time', $value); ?>
        </p>
        <p>
            <label><?php _e('End Time','vpe'); ?></label>
            <?php if(isset($_GET['va_end_time'])){$value = sanitize_text_field($_GET['va_end_time']);}else{$value = get_post_meta($reservation->ID, 'va_end_time', true);} ?>
            <?php echo $this->va_get_time_select('va_end_time', $value); ?>
        </p>  
        <p>
            <label><?php printf(__('Cleanup End Time (after %1$s)','vpe'),$this->va_settings['reservation_single']); ?></label>
            <?php $value = get_post_meta($reservation->ID, 'va_end_cleanup_time', true); ?>
            <?php echo $this->va_get_time_select('va_end_cleanup_time', $value); ?>
        </p>
		<p>
			<label><?php _e('Set Up Needs','vpe'); ?></label></br/>
			<textarea name="va_reservation_setup" rows="6" cols="80"><?php echo get_post_meta($reservation->ID, 'va_reservation_setup', true); ?></textarea>
		</p>		
		<p>
			<label><?php _e('A/V Tech Needs','vpe'); ?></label></br/>
			<textarea name="va_reservation_av" rows="6" cols="80"><?php echo get_post_meta($reservation->ID, 'va_reservation_av', true); ?></textarea>
		</p>

        <h3><?php _e('Contact Information','vpe'); ?></h3>
        <p>
            <label><?php _e('Name','vpe'); ?> </label>
            <input type="text" name="va_reservation_name" value="<?php echo apply_filters('va_before_meta_display', get_post_meta($reservation->ID, 'va_reservation_name', true)); ?>" />
        </p> 
        <p>
            <label><?php _e('Phone','vpe'); ?> </label>
            <input type="tel" name="va_reservation_phone" value="<?php echo apply_filters('va_before_meta_display', get_post_meta($reservation->ID, 'va_reservation_phone', true)); ?>" />
        </p>  
        <p>
            <label><?php _e('Email','vpe'); ?> </label>
            <input type="email" name="va_reservation_email" value="<?php echo apply_filters('va_before_meta_display', get_post_meta($reservation->ID, 'va_reservation_email', true)); ?>" />
        </p>
        <br/>
        <a class="button" href="/wp-admin/admin.php?page=va-admin-calendar"><i class="icon-calendar"></i> <?php _e('View Calendar','vpe'); ?></a><br/>
        <span class="description"><?php printf(__('Tip: Remember to update this %1$s first','vpe'),$this->va_settings['reservation_single']); ?>!</span>
    </div>
</div>
<script type="text/javascript">
    jQuery(document).ready(function($){
        // run chosen on load
        $('#va-venue-id').chosen({
            placeholder_text_single: "<?php printf(__('Select a %1$s','vpe'),$this->va_settings['venue_single']); ?>",
            width: "auto"
        });     
        $('#va-reservation-status').chosen({ 
            disable_search: true,
            width: "auto"
        });    
        $('#va-location-id').chosen({
            placeholder_text_multiple: "<?php printf(__('Select some %1$s','vpe'),$this->va_settings['location_plural']); ?>",
            width: "50%"
        });
        $('#va-reservation-datepicker').datepicker({
            dateFormat: "mm/dd/yy",
            altField: "#va-reservation-date",
            altFormat: "yy-mm-dd"
        });
       
        // ajax call to populate locations for selected venue
        $('#va-venue-id').on('change',function(){
            var venue_id = $(this).val();
            var data = {
                'action': 'va_get_venue_locations',
                'venue_id': venue_id
            };
            $.post(ajaxurl, data, function(response){
                $('#va-locations').html(response); 
                $('#va-location-id').chosen({
                    placeholder_text_multiple: "Select some <?php echo $this->va_settings['location_plural']; ?>",
                    width: "50%"
                });
            }); 
        });
    });
</script>
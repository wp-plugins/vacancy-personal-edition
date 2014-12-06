<?php global $post; ?>
<?php $reservation = $post; ?>
<?php wp_nonce_field('save_reservation_meta', 'reservation_meta_nonce'); ?>
<div>
    <div id="va-fields">
        <p>
            <?php echo apply_filters('va_ecp_status_note', null, $post->ID); ?>
            <label><?php echo $this->va_settings['reservation_single']; ?> status </label>
            <select id="va-reservation-status" name="va_reservation_status">
                <?php $stauses = array('Pending', 'Approved', 'Denied'); ?>
                <?php foreach($stauses as $status) : ?>
                    <option value="<?php echo strtolower($status); ?>" 
                        <?php if(get_post_meta($reservation->ID, 'va_reservation_status', true) == strtolower($status)){echo 'selected';} ?>
                    ><?php echo $status; ?></option>
                <?php endforeach; ?>
            </select><br/><span class="description">TIP: 'Pending' and 'Approved' will block out thier timeslot. 'Denied' simply won't show this <?php echo $this->va_settings['reservation_single']; ?> on the calendar</span><br/>
            <br/><label><?php echo $this->va_settings['reservation_single']; ?> Comments</label></br/><textarea name="va_reservation_comments" rows="6" cols="80"><?php echo get_post_meta($reservation->ID, 'va_reservation_comments', true); ?></textarea>
        </p>
        <h3><?php echo $this->va_settings['venue_single']; ?> and <?php echo $this->va_settings['location_single']; ?></h3>
        <?php $venue_id; ?>
        <?php $venues = $this->va_get_venues(); ?>
        <?php if($venues->have_posts()) : ?>
        <p>
            <label>Which <?php echo $this->va_settings['venue_single']; ?> is this <?php echo $this->va_settings['reservation_single']; ?> for? </label>
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
                <label>This <?php echo $this->va_settings['reservation_single']; ?> is for which <?php echo $this->va_settings['location_plural']; ?>? </label>
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
            <label>Date</label> <input id="va-reservation-datepicker" type="text" value="<?php if(isset($_GET['va_reservation_date'])){echo date('m/d/Y', strtotime(sanitize_text_field($_GET['va_reservation_date'])));}elseif(get_post_meta($reservation->ID, 'va_reservation_date', true)){echo date('m/d/Y', strtotime(get_post_meta($reservation->ID, 'va_reservation_date', true)));} ?>" />
            <input id="va-reservation-date" name="va_reservation_date" type="hidden" value="<?php if(isset($_GET['va_reservation_date'])){echo sanitize_text_field($_GET['va_reservation_date']);}elseif(get_post_meta($reservation->ID, 'va_reservation_date', true)){echo get_post_meta($reservation->ID, 'va_reservation_date', true);} ?>"/>
        </p>
        <p>
            <label>Setup Start Time (before <?php echo $this->va_settings['reservation_single']; ?>)</label>
            <?php $value = get_post_meta($reservation->ID, 'va_start_setup_time', true); ?>
            <?php echo $this->va_get_time_select('va_start_setup_time', $value); ?>
        </p> 
        <p>
            <label>Start Time</label>
            <?php if(isset($_GET['va_start_time'])){$value = sanitize_text_field($_GET['va_start_time']);}else{$value = get_post_meta($reservation->ID, 'va_start_time', true);} ?>
            <?php echo $this->va_get_time_select('va_start_time', $value); ?>
        </p>
        <p>
            <label>End Time</label>
            <?php if(isset($_GET['va_end_time'])){$value = sanitize_text_field($_GET['va_end_time']);}else{$value = get_post_meta($reservation->ID, 'va_end_time', true);} ?>
            <?php echo $this->va_get_time_select('va_end_time', $value); ?>
        </p>  
        <p>
            <label>Cleanup End Time (after <?php echo $this->va_settings['reservation_single']; ?>)</label>
            <?php $value = get_post_meta($reservation->ID, 'va_end_cleanup_time', true); ?>
            <?php echo $this->va_get_time_select('va_end_cleanup_time', $value); ?>
        </p>

        <h3>Contact Information</h3>
        <p>
            <label>Name </label>
            <input type="text" name="va_reservation_name" value="<?php echo get_post_meta($reservation->ID, 'va_reservation_name', true); ?>" />
        </p> 
        <p>
            <label>Phone </label>
            <input type="tel" name="va_reservation_phone" value="<?php echo get_post_meta($reservation->ID, 'va_reservation_phone', true); ?>" />
        </p>  
        <p>
            <label>Email </label>
            <input type="email" name="va_reservation_email" value="<?php echo get_post_meta($reservation->ID, 'va_reservation_email', true); ?>" />
        </p>
        <br/>
        <a class="button" href="/wp-admin/admin.php?page=va-admin-calendar"><i class="icon-calendar"></i> View Calendar</a><br/>
        <span class="description">Tip: Remember to update this <?php echo $this->va_settings['reservation_single']; ?> first!</span>
    </div>
</div>
<script type="text/javascript">
    jQuery(document).ready(function($){
        // run chosen on load
        $('#va-venue-id').chosen({
            placeholder_text_single: "Select a <?php echo $this->va_settings['venue_single']; ?>",
            width: "auto"
        });     
        $('#va-reservation-status').chosen({ 
            disable_search: true,
            width: "auto"
        });    
        $('#va-location-id').chosen({
            placeholder_text_multiple: "Select some <?php echo $this->va_settings['location_plural']; ?>",
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
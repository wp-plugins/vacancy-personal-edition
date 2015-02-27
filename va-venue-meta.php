<?php global $post; ?>
<?php wp_nonce_field('save_venue_meta', 'venue_meta_nonce'); ?>

<div id="va-fields">
    <p id="va-venue-offsite">
        <input type="radio" id="va-venue-offsite-2" name="va_venue_offsite" value="no" <?php if((get_post_meta($post->ID, 'va_venue_offsite', true) == "no") || (get_post_meta($post->ID, 'va_venue_offsite', true) == "")){echo 'checked';} ?> />
        <label for="va-venue-offsite-2">This <?php echo $this->va_settings['venue_single']; ?> is NOT offsite, use details and availability below.</label><br/>
        <input type="radio" id="va-venue-offsite-1" name="va_venue_offsite" value="yes" <?php if(get_post_meta($post->ID, 'va_venue_offsite', true) == "yes"){echo 'checked';} ?> />
        <label for="va-venue-offsite-1">This <?php echo $this->va_settings['venue_single']; ?> IS offsite and will have NO <?php echo $this->va_settings['location_plural']; ?> associated with it. <em>NOTE: This also means that scheduling conflicts will be ignored.</em></label>
    </p>
   
    <div class="va-venue-general" style="display:none;"> 
        <h3>Contact Information</h3>
        <p>
            <label for="va-address">Address</label><br/>
            <input type="text" name="va_address" id="va-address" value="<?php echo get_post_meta($post->ID, 'va_address', true); ?>" />
        </p>            
        <p>
            <label for="va-city">City</label><br/>
            <input type="text" name="va_city" id="va-city" value="<?php echo get_post_meta($post->ID, 'va_city', true); ?>" />
        </p>            
        <p>
            <label for="va-state">State or Province (abbreviation)</label><br/>
            <input type="text" name="va_state" id="va-state" value="<?php echo get_post_meta($post->ID, 'va_state', true); ?>" />
        </p>            
        <p>
            <label for="va-zipcode">Zipcode</label><br/>
            <input type="text" name="va_zipcode" id="va-zipcode" value="<?php echo get_post_meta($post->ID, 'va_zipcode', true); ?>" />
        </p>            
        <p>
            <label for="va-country">Country</label><br/>
            <input type="text" name="va_country" id="va-country" value="<?php echo get_post_meta($post->ID, 'va_country', true); ?>" />
        </p>    
        <p>
            <label for="va-contact-email">Contact Email</label><br/>
            <input type="text" name="va_contact_email" id="va-contact-email" value="<?php echo get_post_meta($post->ID, 'va_contact_email', true); ?>" />
        </p> 
        <p>
            <label for="va-phone">Phone</label><br/>
            <input type="text" name="va_phone" id="va-phone" value="<?php echo get_post_meta($post->ID, 'va_phone', true); ?>" />
        </p>   
        <p>
            <label for="va-website">Website</label><br/>
            <input type="text" name="va_website" id="va-website" value="<?php echo get_post_meta($post->ID, 'va_website', true); ?>" />
        </p> 
    </div>
   
    <div class="va-availability" style="display:none;">  
        <h3><?php echo $this->va_settings['reservation_single']; ?> availability</h3>
        <?php $days = array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'); ?>
        <?php foreach($days as $day) : ?>
        <div>
            <h4><?php echo $day; ?></h4>
            <label>Start</label><br/>
            <?php echo $this->va_get_time_select('va_venue_'.strtolower($day).'_start', get_post_meta($post->ID, 'va_venue_'. strtolower($day). '_start', true)); ?><br/>
            <label>End</label><br/>
            <?php echo $this->va_get_time_select('va_venue_'.strtolower($day).'_end', get_post_meta($post->ID, 'va_venue_'. strtolower($day). '_end', true)); ?><br/>
        </div>  
        <?php endforeach; ?> 
        <span class="va-clearer"></span> 
        <br/>
    </div>
    <script type="text/javascript">
        jQuery(document).ready(function($){
            if($('#va-venue-offsite-2').prop('checked')){
                $('.va-availability, .va-venue-general').show();
            }
            $('#va-venue-offsite').on('change', function(){
                $('.va-availability, .va-venue-general').slideToggle('fast');
            });
        });
    </script>
</div>

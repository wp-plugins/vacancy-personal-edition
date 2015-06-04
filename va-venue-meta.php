<?php global $post; ?>
<?php wp_nonce_field('save_venue_meta', 'venue_meta_nonce'); ?>

<div id="va-fields">
    <p id="va-venue-offsite">
        <input type="radio" id="va-venue-offsite-2" name="va_venue_offsite" value="no" <?php if((get_post_meta($post->ID, 'va_venue_offsite', true) == "no") || (get_post_meta($post->ID, 'va_venue_offsite', true) == "")){echo 'checked';} ?> />
        <label for="va-venue-offsite-2"><?php printf(__('This %1$s is NOT offsite, use details and availability below','vpe'),$this->va_settings['venue_single']); ?>.</label><br/>
        <input type="radio" id="va-venue-offsite-1" name="va_venue_offsite" value="yes" <?php if(get_post_meta($post->ID, 'va_venue_offsite', true) == "yes"){echo 'checked';} ?> />
        <label for="va-venue-offsite-1"><?php printf(__('This %1$s IS offsite and will have NO %2$s associated with it','vpe'),$this->va_settings['venue_single'],$this->va_settings['location_plural']); ?>. <em><?php _e('NOTE: This also means that scheduling conflicts will be ignored','vpe'); ?>.</em></label>
    </p>
   
    <div class="va-venue-general" style="display:none;"> 
        <h3><?php _e('Contact Information','vpe'); ?></h3>
        <p>
            <label for="va-address"><?php _e('Address','vpe'); ?></label><br/>
            <input type="text" name="va_address" id="va-address" value="<?php echo get_post_meta($post->ID, 'va_address', true); ?>" />
        </p>            
        <p>
            <label for="va-city"><?php _e('City','vpe'); ?></label><br/>
            <input type="text" name="va_city" id="va-city" value="<?php echo get_post_meta($post->ID, 'va_city', true); ?>" />
        </p>            
        <p>
            <label for="va-state"><?php _e('State or Province (abbreviation)','vpe'); ?></label><br/>
            <input type="text" name="va_state" id="va-state" value="<?php echo get_post_meta($post->ID, 'va_state', true); ?>" />
        </p>            
        <p>
            <label for="va-zipcode"><?php _e('Zipcode','vpe'); ?></label><br/>
            <input type="text" name="va_zipcode" id="va-zipcode" value="<?php echo get_post_meta($post->ID, 'va_zipcode', true); ?>" />
        </p>            
        <p>
            <label for="va-country"><?php _e('Country','vpe'); ?></label><br/>
            <input type="text" name="va_country" id="va-country" value="<?php echo get_post_meta($post->ID, 'va_country', true); ?>" />
        </p>    
        <p>
            <label for="va-contact-email"><?php _e('Contact Email','vpe'); ?></label><br/>
            <input type="text" name="va_contact_email" id="va-contact-email" value="<?php echo get_post_meta($post->ID, 'va_contact_email', true); ?>" />
        </p> 
        <p>
            <label for="va-phone"><?php _e('Phone','vpe'); ?></label><br/>
            <input type="text" name="va_phone" id="va-phone" value="<?php echo get_post_meta($post->ID, 'va_phone', true); ?>" />
        </p>   
        <p>
            <label for="va-website"><?php _e('Website','vpe'); ?></label><br/>
            <input type="text" name="va_website" id="va-website" value="<?php echo get_post_meta($post->ID, 'va_website', true); ?>" />
        </p> 
    </div>
   
    <div class="va-availability" style="display:none;">  
        <h3><?php printf(__('%1$s availability','vpe'),$this->va_settings['reservation_single']); ?>. <em>(<?php printf(__('Leave times blank to make this %1$s unavailable on those days','vpe'),$this->va_settings['venue_single']); ?>)</em></h3>
        <?php $days = array(__('Monday','vpe'), __('Tuesday','vpe'), __('Wednesday','vpe'), __('Thursday','vpe'), __('Friday','vpe'), __('Saturday','vpe'), __('Sunday','vpe')); ?>
        <?php foreach($days as $day) : ?>
        <div>
            <h4><?php echo $day; ?></h4>
            <label><?php _e('Start','vpe'); ?></label><br/>
            <?php echo $this->va_get_time_select('va_venue_'.strtolower($day).'_start', get_post_meta($post->ID, 'va_venue_'. strtolower($day). '_start', true)); ?><br/>
            <label><?php _e('End','vpe'); ?></label><br/>
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

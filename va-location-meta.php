<?php global $post; ?>
<?php $location = $post; ?>
<?php wp_nonce_field('save_location_meta', 'location_meta_nonce'); ?>
<div>
    <div id="va-fields">
        <?php $venues = $this->va_get_venues(); ?>
        <?php if($venues->have_posts()) : ?>
            <label><?php printf(__('Which %1$s does this %2$s belong to','vpe'),$this->va_settings['venue_single'],$this->va_settings['location_single']); ?>?</label>
            <select id="va-venue-id" name="va_venue_id">
            <?php while($venues->have_posts()) : $venues->the_post(); ?>
                <option value="<?php the_ID();?>" 
                    <?php 
                        if($venue_id = get_post_meta($location->ID, 'va_venue_id', true)){
                            if($venue_id == get_the_ID()){echo 'selected';}
                        }else if(get_option('va_default_venue') == get_the_ID()){echo 'selected';} 
                    ?>
                ><?php the_title(); ?></option>

            <?php endwhile; ?>
            </select>
        <?php endif; ?>
        <p id="va-venue-availability">
            <input type="radio" id="va-venue-availability-1" name="va_venue_availability" value="venue" <?php if((get_post_meta($location->ID, 'va_venue_availability', true) == "venue") || (get_post_meta($location->ID, 'va_venue_availability', true) == "")){echo 'checked';} ?> />
            <label for="va-venue-availability-1"><?php printf(__('Use %1$s availability','vpe'),$this->va_settings['venue_plural']); ?></label><br/>
            <input type="radio" id="va-venue-availability-2" name="va_venue_availability" value="custom" <?php if(get_post_meta($location->ID, 'va_venue_availability', true) == "custom"){echo 'checked';} ?> />
            <label for="va-venue-availability-2"><?php printf(__('Use custom availability for this %1$s','vpe'),$this->va_settings['location_single']); ?>. <em><?php printf(__('Leave times blank to make this %1$s unavailable on those days','vpe'),$this->va_settings['location_single']); ?>)</em></label>
        </p>
        <div class="va-availability" style="display:none;"> 
            <?php $days = array(__('Monday','vpe'), __('Tuesday','vpe'), __('Wednesday','vpe'), __('Thursday','vpe'), __('Friday','vpe'), __('Saturday','vpe'), __('Sunday','vpe')); ?>
            <?php foreach($days as $day) : ?>
            <div>
                <h4><?php echo $day; ?></h4>
                <label><?php _e('Start','vpe'); ?></label><br/>
                <?php echo $this->va_get_time_select('va_location_'.strtolower($day).'_start', get_post_meta($location->ID, 'va_location_'. strtolower($day). '_start', true)); ?><br/>
                <label><?php _e('End','vpe'); ?></label><br/>
                <?php echo $this->va_get_time_select('va_location_'.strtolower($day).'_end', get_post_meta($location->ID, 'va_location_'. strtolower($day). '_end', true)); ?>
            </div>  
            <?php endforeach; ?> 
            <span class="va-clearer"></span> 
            <br/>
        </div>
    </div>
</div>
<script type="text/javascript">
    jQuery(document).ready(function($){
        $('#va-venue-id').chosen({
            placeholder_text_single: "<?php printf(__('Select a %1$s','vpe'),$this->va_settings['venue_single']); ?>"
        });  
        if($('#va-venue-availability-2').prop('checked')){
            $('.va-availability').show();
        }
        $('#va-venue-availability').on('change', function(){
            $('.va-availability').slideToggle('fast');
        });
    });
</script>
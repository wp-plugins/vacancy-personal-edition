<div class="va-main-wrap">
	<h1 class="va-page-title"><i class="icon-calendar"></i> <?php printf(__('%1$s calendar','vpe'),$this->va_settings['reservation_single']); ?></h1>
	<hr/>
	<?php 
		$venue_id = get_option('va_default_venue');
		if(!empty($_POST['va_venue_id'])){$venue_id = sanitize_text_field($_POST['va_venue_id']);}

		// set date default
		$date = date('m/d/Y', strtotime('now'));
		
		// set prev/next/current/chosen
		if(isset($_POST['va_next'])){
			$date = $_POST['va_next'];
		}else if(isset($_POST['va_prev'])){
			$date = $_POST['va_prev'];
		}else if(isset($_POST['va_current'])){
			$date = $_POST['va_current'];
		}else if(isset($_POST['va_date_chosen'])){
			$date = $_POST['va_date_chosen'];
		}

		// check for view
		if(isset($_POST['va_back'])){
			echo $this->va_draw_calendar($date, $venue_id);
		}else{
			if(isset($_POST['va_view']) && $_POST['va_view'] == 'day'){
				echo $this->va_draw_day($date, $venue_id);
			}else{
				echo $this->va_draw_calendar($date, $venue_id);
			}	
		}
	?>
</div>
<script type="text/javascript">
    jQuery(document).ready(function($){
        $('#va-venue-id').chosen({
            placeholder_text_single: "<?php printf(__('Select a %1$s','vpe'),$this->va_settings['venue_single']); ?>"
        });

		$("table").stickyTableHeaders({fixedOffset: 32});
    });
</script>
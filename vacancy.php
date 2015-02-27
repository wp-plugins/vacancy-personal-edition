<?php
    /*
    Plugin Name: Vacancy Personal Edition
    Plugin URI: http://kraftpress.it
    Description: A full featured appointment and reservation booking solution
    Version: 1.1.0
    Author: kraftpress
    Author URI: http://kraftpress.it
    Contributors: kraftpress, buildcreate, a2rocklobster
    License: GPL
    */
    // If this file is called directly, abort.
    if(!defined('WPINC')){die;}
    class Vacancy {
        public $va_settings;
        
        function __construct(){
            // helpers
            add_filter('va_get_path', array($this, 'va_get_path'), 1, 1);
            add_filter('va_get_dir', array($this, 'va_get_dir'), 1, 1);
             // vars
            $this->va_settings = array(
                'version' => '1.1.0',
                'path' => apply_filters('va_get_path', __FILE__),
                'dir' => apply_filters('va_get_dir', __FILE__),
                'hook' => basename( dirname( __FILE__ ) ) . '/' . basename( __FILE__ ),
                'venue_single' => 'Venue',
                'venue_plural' => 'Venues',
                'location_single' => 'Location',
                'location_plural' => 'Locations',
                'reservation_single' => 'Reservation',
                'reservation_plural' => 'Reservations',
                'default_venue' => '',
                'day_start_time' => '08:00',
                'day_end_time' => '22:00',
                'end_time_length_hr' => '0',
                'end_time_length_min' => '0',
                'require_login' => 'yes',
                'admin_new_notification' => 'yes',
                'admin_email_label_one' => 'Rentals',
                'admin_email_one' => get_option('admin_email'),
                'admin_email_label_two' => '',
                'admin_email_two' => '',
                'user_new_notification' => 'yes',
                'user_approved_notification' => 'yes',
                'from_email_name' => '',
                'from_email_address' => '',
                'hide_admin_bar' => 'no',
                'show_admin_bar_for' => 'administrator',
                'user_subject_line_new' => '',
                'user_subject_line_approved' => '',
                'reservation_success_message' => '',
                'setup_cleanup' => 'yes',
                'saving_reservation_meta' => false,
                'show_reservation_details' => 'yes',
                'show_form_fields' => array('end_time','setup_time','cleanup_time','title','venue','location','phone','type','description','setup_needs','av_needs')
            );
            // filters 
            add_filter('manage_edit-va_venue_columns', array($this, 'va_venue_columns_filter'), 999, 1);
            add_filter('manage_edit-va_location_columns', array($this, 'va_location_columns_filter'), 999, 1);
            add_filter('manage_edit-va_reservation_columns', array($this, 'va_reservation_columns_filter'), 999, 1);
            add_filter('manage_edit-va_reservation_sortable_columns', array($this, 'va_reservation_sortable_columns_filter'), 999, 1);
            
            // actions
            add_action('admin_menu', array($this, 'admin_menu'), 999);
            add_action('init', array($this, 'init'), 1);
            add_action('admin_notices', array($this, 'va_setup_message'), 1);
            add_action('admin_enqueue_scripts', array($this, 'va_styles'));
            add_action('wp_enqueue_scripts', array($this, 'va_styles'));
            add_action('add_meta_boxes', array($this, 'va_venue_meta_box'));
            add_action('save_post', array($this, 'va_save_venue_meta'),1);
            add_action('add_meta_boxes', array($this, 'va_location_meta_box'));
            add_action('save_post', array($this, 'va_save_location_meta'),1);
            add_action('add_meta_boxes', array($this, 'va_reservation_meta_box'));
            add_action('save_post', array($this, 'va_save_reservation_meta'),1);
            add_action('manage_va_venue_posts_custom_column', array($this, 'va_manage_venue_columns'), 10, 2);
            add_action('manage_va_location_posts_custom_column', array($this, 'va_manage_location_columns'), 10, 2);
            add_action('manage_va_reservation_posts_custom_column', array($this, 'va_manage_reservation_columns'), 10, 2);
            add_action('wp_ajax_va_get_venue_locations', array($this, 'va_get_venue_locations'));
            add_action('wp_ajax_va_draw_shortcode_day', array($this, 'va_draw_shortcode_day'));
            add_action('wp_ajax_nopriv_va_draw_shortcode_day', array($this, 'va_draw_shortcode_day'));
            add_action('wp_ajax_va_make_reservation', array($this, 'va_make_reservation'));
            add_action('wp_ajax_nopriv_va_make_reservation', array($this, 'va_make_reservation'));
            add_action('pre_get_posts', array($this, 'va_column_orderby'));
            add_action('after_setup_theme', array($this, 'va_extensions'));
            add_action('load-post-new.php', array($this, 'va_disable_new_post'));
            
            // shortcode
            add_shortcode('vacancy', array($this, 'va_display_form'));
        }
        function init(){
			// force browser to clear cache
			header("Cache-Control: no-cache, must-revalidate");
			header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
			header("Content-Type: application/xml; charset=utf-8");
			
            // set options
            if(isset($_POST['va_update_settings'])){
                if(!empty($_POST['va_venue_single'])){update_option('va_venue_single', sanitize_text_field($_POST['va_venue_single']));}
                if(!empty($_POST['va_venue_plural'])){update_option('va_venue_plural', sanitize_text_field($_POST['va_venue_plural']));}
                if(!empty($_POST['va_location_single'])){update_option('va_location_single', sanitize_text_field($_POST['va_location_single']));}
                if(!empty($_POST['va_location_plural'])){update_option('va_location_plural', sanitize_text_field($_POST['va_location_plural']));}
                if(!empty($_POST['va_reservation_single'])){update_option('va_reservation_single', sanitize_text_field($_POST['va_reservation_single']));}
                if(!empty($_POST['va_reservation_plural'])){update_option('va_reservation_plural', sanitize_text_field($_POST['va_reservation_plural']));}
                if(!empty($_POST['va_default_venue'])){update_option('va_default_venue', sanitize_text_field($_POST['va_default_venue']));}
                if(!empty($_POST['va_day_start_time'])){update_option('va_day_start_time', sanitize_text_field($_POST['va_day_start_time']));}
                if(!empty($_POST['va_day_end_time'])){update_option('va_day_end_time', sanitize_text_field($_POST['va_day_end_time']));}
                
                if(!empty($_POST['va_end_time_length_hr'])){update_option('va_end_time_length_hr', sanitize_text_field($_POST['va_end_time_length_hr']));}
                if(!empty($_POST['va_end_time_length_min'])){update_option('va_end_time_length_min', sanitize_text_field($_POST['va_end_time_length_min']));}

                if(!empty($_POST['va_require_login'])){update_option('va_require_login', sanitize_text_field($_POST['va_require_login']));}
                if(!empty($_POST['va_admin_new_notification'])){update_option('va_admin_new_notification', sanitize_text_field($_POST['va_admin_new_notification']));}
                if(!empty($_POST['va_admin_email_one'])){update_option('va_admin_email_one', sanitize_text_field($_POST['va_admin_email_one']));}
                if(!empty($_POST['va_admin_email_label_one'])){update_option('va_admin_email_label_one', sanitize_text_field($_POST['va_admin_email_label_one']));}
                if(isset($_POST['va_admin_email_two'])){update_option('va_admin_email_two', sanitize_text_field($_POST['va_admin_email_two']));}
                if(isset($_POST['va_admin_email_label_two'])){update_option('va_admin_email_label_two', sanitize_text_field($_POST['va_admin_email_label_two']));}
                if(!empty($_POST['va_user_new_notification'])){update_option('va_user_new_notification', sanitize_text_field($_POST['va_user_new_notification']));}
                if(!empty($_POST['va_user_approved_notification'])){update_option('va_user_approved_notification', sanitize_text_field($_POST['va_user_approved_notification']));}
                if(!empty($_POST['va_from_email_name'])){update_option('va_from_email_name', sanitize_text_field($_POST['va_from_email_name']));}
                if(!empty($_POST['va_from_email_address'])){update_option('va_from_email_address', sanitize_text_field($_POST['va_from_email_address']));}
                if(isset($_POST['va_notification_header'])){update_option('va_notification_header', esc_textarea($_POST['va_notification_header']));}
                if(isset($_POST['va_notification_footer'])){update_option('va_notification_footer', esc_textarea($_POST['va_notification_footer']));}
                if(!empty($_POST['va_hide_admin_bar'])){update_option('va_hide_admin_bar', sanitize_text_field($_POST['va_hide_admin_bar']));}
                if(isset($_POST['va_hide_admin_bar'])){ // check other field since this one can be not set
                    if(!empty($_POST['va_show_admin_bar_for'])){
                        $vals = $_POST['va_show_admin_bar_for'];
                        if(is_array($vals)){
                            foreach($vals as $k => $v){$vals[$k] = sanitize_text_field($v);}
                            update_option('va_show_admin_bar_for', $vals);
                        }
                    }else{
                        update_option('va_show_admin_bar_for', array(''));
                    }
                }
                if(!empty($_POST['va_setup_cleanup'])){update_option('va_setup_cleanup', sanitize_text_field($_POST['va_setup_cleanup']));}
                if(isset($_POST['va_user_subject_line_new'])){update_option('va_user_subject_line_new', sanitize_text_field($_POST['va_user_subject_line_new']));}
                if(isset($_POST['va_user_subject_line_approved'])){update_option('va_user_subject_line_approved', sanitize_text_field($_POST['va_user_subject_line_approved']));}
                if(isset($_POST['va_reservation_success_message'])){update_option('va_reservation_success_message', sanitize_text_field($_POST['va_reservation_success_message']));}
                if(isset($_POST['va_show_reservation_details'])){update_option('va_show_reservation_details', sanitize_text_field($_POST['va_show_reservation_details']));}
               
                $show_form_fields = get_option('va_show_form_fields');
                if(!is_array($show_form_fields)){
                    update_option('va_show_form_fields', $this->va_settings['show_form_fields']);
                }
                if(isset($_POST['va_save_form_settings'])){
                    if(isset($_POST['va_show_form_fields'])){
                        $vals = $_POST['va_show_form_fields'];
                    }else{
                        $vals = array();
                    }
                    update_option('va_show_form_fields', $vals);
                }
            }
            
            // get options
            $venue_single = get_option('va_venue_single');
            if(!empty($venue_single)){$this->va_settings['venue_single'] = $venue_single;}
            $venue_plural = get_option('va_venue_plural');
            if(!empty($venue_plural)){$this->va_settings['venue_plural'] = $venue_plural;}
            $location_single = get_option('va_location_single');
            if(!empty($location_single)){$this->va_settings['location_single'] = $location_single;}
            $location_plural = get_option('va_location_plural');
            if(!empty($location_plural)){$this->va_settings['location_plural'] = $location_plural;}
            $reservation_single = get_option('va_reservation_single');
            if(!empty($reservation_single)){$this->va_settings['reservation_single'] = $reservation_single;}
            $reservation_plural = get_option('va_reservation_plural');
            if(!empty($reservation_plural)){$this->va_settings['reservation_plural'] = $reservation_plural;}
            $default_venue = get_option('va_default_venue');
            if(!empty($default_venue)){$this->va_settings['default_venue'] = $default_venue;} 
            $day_start_time = get_option('va_day_start_time');
            if(!empty($day_start_time)){$this->va_settings['day_start_time'] = $day_start_time;} 
            $day_end_time = get_option('va_day_end_time');
            if(!empty($day_end_time)){$this->va_settings['day_end_time'] = $day_end_time;} 

            $end_time_length_hr = get_option('va_end_time_length_hr');
            if(!empty($end_time_length_hr)){$this->va_settings['end_time_length_hr'] = $end_time_length_hr;} 
            $end_time_length_min = get_option('va_end_time_length_min');
            if(!empty($end_time_length_min)){$this->va_settings['end_time_length_min'] = $end_time_length_min;} 

            $require_login = get_option('va_require_login');
            if(!empty($require_login)){$this->va_settings['require_login'] = $require_login;}
            $admin_new_notification = get_option('va_admin_new_notification');
            if(!empty($admin_new_notification)){$this->va_settings['admin_new_notification'] = $admin_new_notification;}
            
            $admin_email_one = get_option('va_admin_email_one');
            if(!empty($admin_email_one)){$this->va_settings['admin_email_one'] = $admin_email_one;}
            $admin_email_label_one = get_option('va_admin_email_label_one');
            if(!empty($admin_email_label_one)){$this->va_settings['admin_email_label_one'] = $admin_email_label_one;} 
            $admin_email_two = get_option('va_admin_email_two');
            if(isset($admin_email_two)){$this->va_settings['admin_email_two'] = $admin_email_two;}
            $admin_email_label_two = get_option('va_admin_email_label_two');
            if(isset($admin_email_label_two)){$this->va_settings['admin_email_label_two'] = $admin_email_label_two;}
            
            $user_new_notification = get_option('va_user_new_notification');
            if(!empty($user_new_notification)){$this->va_settings['user_new_notification'] = $user_new_notification;}
            $user_approved_notification = get_option('va_user_approved_notification');
            if(!empty($user_approved_notification)){$this->va_settings['user_approved_notification'] = $user_approved_notification;}
            $from_email_name = get_option('va_from_email_name');
            if(!empty($from_email_name)){$this->va_settings['from_email_name'] = $from_email_name;}
            $from_email_address = get_option('va_from_email_address');
            if(!empty($from_email_address)){$this->va_settings['from_email_address'] = $from_email_address;}
            $hide_admin_bar = get_option('va_hide_admin_bar');
            if(!empty($hide_admin_bar)){$this->va_settings['hide_admin_bar'] = $hide_admin_bar;}
            $this->va_settings['show_admin_bar_for'] = get_option('va_show_admin_bar_for');
            $setup_cleanup = get_option('va_setup_cleanup');
            if(!empty($setup_cleanup)){$this->va_settings['setup_cleanup'] = $setup_cleanup;}
            $this->va_settings['user_subject_line_new'] = get_option('va_user_subject_line_new');
            $this->va_settings['user_subject_line_approved'] = get_option('va_user_subject_line_approved');
            $this->va_settings['reservation_success_message'] = get_option('va_reservation_success_message');
            $show_reservation_details = get_option('va_show_reservation_details');
            if(!empty($show_reservation_details)){$this->va_settings['show_reservation_details'] = $show_reservation_details;}
            $this->va_settings['show_form_fields'] = get_option('va_show_form_fields');

            // Create Venue post type
            $labels = array(
                'venue' => array(
                    'name' => $this->va_settings['venue_plural'],
                    'singular_name' => $this->va_settings['venue_single'],
                    'add_new' => 'Add New',
                    'add_new_item' => 'Add New ' . $this->va_settings['venue_single'],
                    'edit_item' =>  'Edit ' . $this->va_settings['venue_single'],
                    'new_item' => 'New ' . $this->va_settings['venue_single'],
                    'view_item' => 'View ' . $this->va_settings['venue_single'],
                    'search_items' => 'Search ' .$this->va_settings['venue_plural'],
                    'not_found' => 'No ' . $this->va_settings['venue_plural'] . ' Found',
                    'not_found_in_trash' => 'No ' . $this->va_settings['venue_plural'] . ' found in Trash'
                ),    
                'location' => array(
                    'name' => $this->va_settings['location_plural'],
                    'singular_name' => $this->va_settings['location_single'],
                    'add_new' => 'Add New',
                    'add_new_item' => 'Add New ' . $this->va_settings['location_single'],
                    'edit_item' =>  'Edit ' . $this->va_settings['location_single'],
                    'new_item' => 'New ' . $this->va_settings['location_single'],
                    'view_item' => 'View ' . $this->va_settings['location_single'],
                    'search_items' => 'Search ' .$this->va_settings['location_plural'],
                    'not_found' => 'No ' . $this->va_settings['location_plural'] . ' Found',
                    'not_found_in_trash' => 'No ' . $this->va_settings['location_plural'] . ' found in Trash'
                ),    
                'reservation' => array(
                    'name' => $this->va_settings['reservation_plural'],
                    'singular_name' => $this->va_settings['reservation_single'],
                    'add_new' => 'Add New',
                    'add_new_item' => 'Add New ' . $this->va_settings['reservation_single'],
                    'edit_item' =>  'Edit ' . $this->va_settings['reservation_single'],
                    'new_item' => 'New ' . $this->va_settings['reservation_single'],
                    'view_item' => 'View ' . $this->va_settings['reservation_single'],
                    'search_items' => 'Search ' .$this->va_settings['reservation_plural'],
                    'not_found' => 'No ' . $this->va_settings['reservation_plural'] . ' Found',
                    'not_found_in_trash' => 'No ' . $this->va_settings['reservation_plural'] . ' found in Trash'
                )
            );
            
            foreach($labels as $k => $label){
                register_post_type('va_'.$k, array(
                    'labels' => $label,
                    'public' => true,
                    'show_ui' => true,
                    'capability_type' => 'post',
                    'hierarchical' => true,
                    'rewrite' => array('slug' => $k),
                    'query_var' => 'va_'.$k,
                    'supports' => array('title','editor','thumbnail','revisions', 'page-attributes'),
                    'show_in_menu'  => false,
                ));     
            }
            // check for WP admin bar
            if($this->va_settings['hide_admin_bar'] == 'yes'){
                global $current_user;
                $user_roles = $current_user->roles;
                $user_role = array_shift($user_roles);
                if(is_array($this->va_settings['show_admin_bar_for'])){
                    if(!in_array($user_role, $this->va_settings['show_admin_bar_for'])){
                        show_admin_bar(false);
                    }
                }else if($user_role != $this->va_settings['show_admin_bar_for']){
                   show_admin_bar(false);
                }
            }

            if(isset($_POST['va_reservation_submitted'])){
                // set cookies for later use
                if(isset($_POST['va_reservation_title'])){
                    setcookie('va_reservation_title', sanitize_text_field($_POST['va_reservation_title']), time() + (86400), "/");
                }
                if(isset($_POST['va_reservation_content'])){
                    setcookie('va_reservation_content', sanitize_text_field($_POST['va_reservation_content']), time() + (86400), "/");
                }
                if(isset($_POST['va_reservation_name'])){
                    setcookie('va_reservation_name', sanitize_text_field($_POST['va_reservation_name']), time() + (86400), "/");
                }
                if(isset($_POST['va_reservation_phone'])){
                    setcookie('va_reservation_phone', sanitize_text_field($_POST['va_reservation_phone']), time() + (86400), "/");
                }
                if(isset($_POST['va_reservation_email'])){
                    setcookie('va_reservation_email', sanitize_text_field($_POST['va_reservation_email']), time() + (86400), "/");
                }
                if(isset($_POST['va_reservation_setup'])){
                    setcookie('va_reservation_setup', sanitize_text_field($_POST['va_reservation_setup']), time() + (86400), "/");
                }
                if(isset($_POST['va_reservation_av'])){
                    setcookie('va_reservation_av', sanitize_text_field($_POST['va_reservation_av']), time() + (86400), "/");
                }
            }
            
            
            // remove automatic ordering from CPTO plugin
            $cpto_options = get_option('cpto_options');
            if($cpto_options){
                // find and remove vacancy post types
                if($cpto_options['allow_post_types']){
                    $new_options = array();
                    foreach($cpto_options['allow_post_types'] as $post_type){
                        if($post_type != 'va_reservation'){
                            $new_options[] = $post_type;
                        }
                    }
                    $cpto_options['allow_post_types'] = $new_options;
                    update_option('cpto_options', $cpto_options);
                }
            }
        }

        function va_setup_message(){
            // check if clicked 
            $clicked = get_option('va_setup_usage');
            if($clicked != 1){
            ?>
            <div class="updated">
                <p><strong>Welcome to Vacancy!<strong> Please take a moment to learn about how to setup and use your new reservation system! <a href="/wp-admin/admin.php?page=va-settings&tab=va-setup-usage&notice=1">Click here for more</a></p>
            </div>
            <?php
            }
        }

        function admin_menu(){
            global $submenu;
            add_menu_page("Vacancy", "Vacancy", 'manage_options', 'va-settings', array($this, 'va_settings_callback'), '', '6.66');
            add_submenu_page('va-settings', $this->va_settings['location_plural'], $this->va_settings['location_plural'], 'manage_options', 'edit.php?post_type=va_location');
            add_submenu_page('va-settings', $this->va_settings['venue_plural'], $this->va_settings['venue_plural'], 'manage_options', 'edit.php?post_type=va_venue');
            add_submenu_page('va-settings', "Calendar View", "Calendar View", 'manage_options', 'va-admin-calendar', array($this, 'va_calendar_callback'));
            add_submenu_page('va-settings', 'Settings', 'Settings', 'manage_options', 'va-settings', array($this, 'va_settings_callback'));
            $submenu['va-settings'][0][0] = $this->va_settings['reservation_plural'];
            $submenu['va-settings'][0][2] = 'edit.php?post_type=va_reservation';
        }
        function va_settings_callback(){
            $path = $this->va_settings['path'];
            include_once($path . 'va-settings.php');
        }    
        function va_calendar_callback(){
            $path = $this->va_settings['path'];
            include_once($path . 'va-admin-calendar.php');
        }

        function va_styles() { 
            wp_enqueue_style('va-font-awesome', $this->va_settings['dir'] . 'css/fonts.css', false, $this->va_settings['version']); 
            wp_enqueue_style('va-chosen', $this->va_settings['dir'] . 'js/chosen_v1.2.0/chosen.css', false, $this->va_settings['version']); 
            wp_enqueue_style('jquery-ui', "//code.jquery.com/ui/1.11.1/themes/smoothness/jquery-ui.css", false, $this->va_settings['version']); 
            wp_enqueue_style('va-datepicker', $this->va_settings['dir'] . 'css/va-datepicker.css', false, $this->va_settings['version']);
            wp_enqueue_script('jquery-ui', "//code.jquery.com/ui/1.11.1/jquery-ui.js", array('jquery-ui-core', 'jquery-ui-mouse'), '1.11.1');
            wp_enqueue_script('va-chosen', $this->va_settings['dir'] . 'js/chosen_v1.2.0/chosen.jquery.js', array('jquery'), $this->va_settings['version']); 
            wp_enqueue_script('va-float-thead', $this->va_settings['dir'] . 'js/floatThead/dist/jquery.floatThead.js', array('jquery'), $this->va_settings['version']);
           
            if(is_admin()){
                wp_enqueue_style('va-admin-css', $this->va_settings['dir'] . 'css/va-admin.css', false, $this->va_settings['version']); 
            }else{
                wp_enqueue_script('thickbox',null,array('jquery'));
                wp_enqueue_script('touch-punch', $this->va_settings['dir'] . 'js/jquery.ui.touch-punch.min.js', array('jquery'), $this->va_settings['version']); 
                wp_enqueue_style('thickbox.css', '/'.WPINC.'/js/thickbox/thickbox.css', null, '1.0');
                wp_enqueue_style('va-css', $this->va_settings['dir'] . 'css/va.css', false, $this->va_settings['version']); 
            }
        }

        function va_extensions(){
            do_action('va_extensions');
        }

        function va_disable_new_post(){
            if(get_current_screen()->post_type == 'va_location'){
                $locations = new WP_Query('post_type=va_location&post_status=publish');
                if($locations->found_posts > 1){
                    wp_die("<div style='text-align:center;'><h1>Vacancy Personal Edition is limited to using only 1 " . $this->va_settings['location_single'] . ".</h1><p>Upgrade to <a target='_blank' href='//kraftpress.it/vacancy'>Vacancy Pro Edition</a> to create an Unlimited amount of " . $this->va_settings['location_plural'] . ".</p><a style='display:block; width:150px; margin:30px auto 0; padding:10px 20px; background:#21759B; color:#ffffff; border-radius:5px;' target='_blank' href='//kraftpress.it/vacancy'>UPGRADE NOW&nbsp;&nbsp;></i></a></div>");
                }
            }
            if(get_current_screen()->post_type == 'va_venue'){
                $venues = new WP_Query('post_type=va_venue&post_status=publish');
                if($venues->found_posts > 1){
                    wp_die("<div style='text-align:center;'><h1>Vacancy Personal Edition is limited to using only 1 " . $this->va_settings['venue_single'] . ".</h1><p>Upgrade to <a target='_blank' href='//kraftpress.it/vacancy'>Vacancy Pro Edition</a> to create an Unlimited amount of " . $this->va_settings['venue_plural'] . ".</p><a style='display:block; width:150px; margin:30px auto 0; padding:10px 20px; background:#21759B; color:#ffffff; border-radius:5px;' target='_blank' href='//kraftpress.it/vacancy'>UPGRADE NOW&nbsp;&nbsp;></i></a></div>");
                } 
            }
        }

        function va_venue_columns_filter($columns){
            $columns = array(
                'cb' => '<input type="checkbox" />',
                'title' => 'Title',
                'address' => 'Address',
                'offsite' => 'Offsite',
                'date' => 'Date'
            );
            return $columns;
        }
        function va_manage_venue_columns($column, $post_id){
            switch($column){
                case 'offsite' :
                    echo get_post_meta($post_id, 'va_venue_offsite', true);
                    break;
                case 'address' :
                    $full = null;
                    if($address = get_post_meta($post_id, 'va_address', true)){
                        $full .= $address . ', ';
                    }
                    if($city = get_post_meta($post_id, 'va_city', true)){
                        $full .= $city . ' ';
                    }
                    if($state = get_post_meta($post_id, 'va_state', true)){
                        $full .= $state . ' ';
                    }
                    if($zipcode = get_post_meta($post_id, 'va_zipcode', true)){
                        $full .= $zipcode . ' ';
                    }
                    if($country = get_post_meta($post_id, 'va_country', true)){
                        $full .= $country . ' ';
                    }
                    if(!$full){
                        $full = '-';
                    }
                    echo $full;
                    break;
            }
        }
        function va_location_columns_filter($columns){
            $columns = array(
                'cb' => '<input type="checkbox" />',
                'title' => 'Title',
                'venue' => $this->va_settings['venue_single'],
                'date' => 'Date'
            );
            return $columns;
        }
        function va_manage_location_columns($column, $post_id){
            echo '<a href="/wp-admin/post.php?post='.get_post_meta($post_id,'va_venue_id',true).'&action=edit">'.get_the_title(get_post_meta($post_id,'va_venue_id',true)).'</a>';
        }
        function va_reservation_columns_filter($columns){
            $columns = array(
                'cb' => '<input type="checkbox" />',
                'title' => 'Title',
                'status' => 'Status',
                'venue' => $this->va_settings['venue_single'],
                'location' => $this->va_settings['location_plural'],
                'reservation-date' => $this->va_settings['reservation_single'].' Date',
                'setup' => 'Setup Start',
                'start' => 'Start Time',
                'end' => 'End Time',
                'cleanup' => 'Cleanup End',
                'date' => 'Publish Date'
            );
            $columns = apply_filters('va_reservation_columns', $columns);
            return $columns;
        }
        function va_manage_reservation_columns($column, $post_id){
            switch($column){
                case 'status' :
                    $status =  get_post_meta($post_id, 'va_reservation_status', true);
                    if($status == 'approved'){echo '<i class="icon-ok-sign"></i> Approved';}
                    if($status == 'pending'){echo '<i class="icon-minus-sign"></i> Pending';}
                    if($status == 'denied'){echo '<i class="icon-remove-sign"></i> Denied';}
                    break;
                case 'link' :
                    $event_id = get_post_meta($post_id, 'va_tribe_event_id', true);
                    $event = null;
                    if($event_id){$event = get_post($event_id);}
                    $post_status = '';
                    if($event){
                        $event_link = "/wp-admin/post.php?post=$event_id&action=edit";
                        if($event->post_status == 'publish'){$post_status = 'published';}
                        if($event->post_status == 'draft'){$post_status = 'draft';}
                        if($event->post_status == 'trash'){
                            $event_link = "/wp-admin/edit.php?post_status=trash&post_type=tribe_events";
                            $post_status = 'in trash';
                        }
                        echo '<a href="'.$event_link.'">view ('.$post_status.')</a>';
                    }else{
                        echo '-';
                    }
                    break;
                case 'venue' :
                    echo '<a href="/wp-admin/post.php?post='.get_post_meta($post_id,'va_venue_id',true).'&action=edit">'.get_the_title(get_post_meta($post_id,'va_venue_id',true)).'</a>';
                    break;
                case 'location':
                    $location_ids = get_post_meta($post_id, 'va_location_id', true);
                    $first = true;
                    if($location_ids){
                        foreach ($location_ids as $location_id){
                            if($first){
                                $first = false;
                                echo '<a href="/wp-admin/post.php?post='.$location_id.'&action=edit">'.get_the_title($location_id).'</a>';
                            }else{
                                echo ', <a href="/wp-admin/post.php?post='.$location_id.'&action=edit">'.get_the_title($location_id).'</a>';
                            }
                        }
                    }else{
                        echo '-';
                    }
                    break;
                case 'reservation-date':
                    echo date('m/d/y', strtotime(get_post_meta($post_id, 'va_reservation_date', true)));
                    break;
                case 'setup':
                    echo date('g:i a', strtotime(get_post_meta($post_id, 'va_start_setup_time', true)));
                    break;  
                case 'start':
                    echo date('g:i a', strtotime(get_post_meta($post_id, 'va_start_time', true)));
                    break;    
                case 'end':
                    echo date('g:i a', strtotime(get_post_meta($post_id, 'va_end_time', true)));
                    break;
                case 'cleanup':
                    echo date('g:i a', strtotime(get_post_meta($post_id, 'va_end_cleanup_time', true)));
                    break;
            }
        }
        function va_reservation_sortable_columns_filter($columns){
            $columns['status'] = 'status';
            $columns['reservation-date'] = 'reservation-date';
            $columns['setup'] = 'setup';
            $columns['start'] = 'start';
            $columns['end'] = 'end';
            $columns['cleanup'] = 'cleanup';
            return $columns;
        }
        function va_column_orderby($query){
            if(!is_admin()){return;}
            $orderby = $query->get('orderby');
        
            switch($orderby){
                case 'status':
                    $query->set('meta_key','va_reservation_status');
                    $query->set('orderby','meta_value');
                    break;
                case 'reservation_date':
                    $query->set('meta_key','va_reservation_date');
                    $query->set('orderby','meta_value');
                    break;
                case 'setup':
                    $query->set('meta_key','va_start_setup_time');
                    $query->set('orderby','meta_value');
                    break;   
                case 'start':
                    $query->set('meta_key','va_start_time');
                    $query->set('orderby','meta_value');
                    break;
                case 'end':
                    $query->set('meta_key','va_end_time');
                    $query->set('orderby','meta_value');
                    break;       
                case 'cleanup':
                    $query->set('meta_key','va_end_cleanup_time');
                    $query->set('orderby','meta_value');
                    break;       
                default : 
                    $query->set('orderby', 'date');
                    $query->set('order', 'DESC');
                    break;
            }
        }
        
        function va_get_venues(){
            $venues = new WP_Query('post_type=va_venue&posts_per_page=-1');
            return $venues;
        }   

        // ajax request
        function va_get_venue_locations(){
            $venue_id = sanitize_text_field($_POST['venue_id']);
            $args = array(
                'post_type' => 'va_location',
                'posts_per_page' => -1,
                'meta_key' => 'va_venue_id',
                'meta_value' => $venue_id
            );
            $locations = new WP_Query($args);
            if($locations->have_posts()){
                $html = '<p><label>This ' . $this->va_settings['reservation_single'] . ' is for which ' . $this->va_settings['location_plural'] . '? </label>';
                $html .= '<select id="va-location-id" name="va_location_id" multiple>';
                $html .=  '<option></option>';
                while($locations->have_posts()) : $locations->the_post();
                    $html .= '<option value="'.get_the_ID().'" '; 
                    if(get_post_meta($reservation->ID, 'va_location_id', true) == get_the_ID()){$html.= '"selected"';}
                    $html.= '>'.get_the_title().'</option>';
                endwhile;
                $html .='</select></p>';
            }
            echo $html;
            die();
        }    
        function va_get_locations(){
            $locations = new WP_Query('post_type=va_location&posts_per_page=-1');
            return $locations;
        }        
        function va_get_reservations(){
            $reservations = new WP_Query('post_type=va_reservation&posts_per_page=-1');
            return $reservations;
        }
        function va_venue_meta_box(){
            add_meta_box('va-venue-meta', $this->va_settings['venue_single'] . ' Details', array($this, 'va_venue_meta_box_html'), 'va_venue', 'normal', 'high');
        }
        function va_venue_meta_box_html(){
            include_once('va-venue-meta.php');
        }        
        function va_location_meta_box(){
            add_meta_box('va-location-meta', $this->va_settings['location_single'] . ' Details', array($this, 'va_location_meta_box_html'), 'va_location', 'normal', 'high');
        }
        function va_location_meta_box_html(){
            include_once('va-location-meta.php');
        }        
        function va_reservation_meta_box(){
            add_meta_box('va-reservation-meta', $this->va_settings['reservation_single'] . ' Details', array($this, 'va_reservation_meta_box_html'), 'va_reservation', 'normal', 'high');
        }
        function va_reservation_meta_box_html(){
            include_once('va-reservation-meta.php');
        }
        function va_save_venue_meta($post_id){
            // Check if our nonce is set.
            if(!isset($_POST['venue_meta_nonce'])){return;}
            // Verify that the nonce is valid.
            if(!wp_verify_nonce($_POST['venue_meta_nonce'], 'save_venue_meta')){return;}
            // If this is an autosave, our form has not been submitted, so we don't want to do anything.
            if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE){return;}
            // Check the user's permissions.
            if(isset($_POST['post_type']) && 'va_venue' == $_POST['post_type']){
                if(!current_user_can('edit_page', $post_id)){return;}
                if(!current_user_can('edit_post', $post_id)){return;}
            }
            // sanitize data
            $address = sanitize_text_field($_POST['va_address']);
            $city = sanitize_text_field($_POST['va_city']);
            $state = sanitize_text_field($_POST['va_state']);
            $zipcode = sanitize_text_field($_POST['va_zipcode']);
            $country = sanitize_text_field($_POST['va_country']);
            $email = sanitize_text_field($_POST['va_contact_email']);
            $phone = sanitize_text_field($_POST['va_phone']);
            $website = sanitize_text_field($_POST['va_website']);
            $mon_start = sanitize_text_field($_POST['va_venue_monday_start']);
            $mon_end = sanitize_text_field($_POST['va_venue_monday_end']); 
            $tues_start = sanitize_text_field($_POST['va_venue_tuesday_start']);
            $tues_end = sanitize_text_field($_POST['va_venue_tuesday_end']);            
            $wed_start = sanitize_text_field($_POST['va_venue_wednesday_start']);
            $wed_end = sanitize_text_field($_POST['va_venue_wednesday_end']);            
            $thurs_start = sanitize_text_field($_POST['va_venue_thursday_start']);
            $thurs_end = sanitize_text_field($_POST['va_venue_thursday_end']);            
            $fri_start = sanitize_text_field($_POST['va_venue_friday_start']);
            $fri_end = sanitize_text_field($_POST['va_venue_friday_end']);            
            $sat_start = sanitize_text_field($_POST['va_venue_saturday_start']);
            $sat_end = sanitize_text_field($_POST['va_venue_saturday_end']);            
            $sun_start = sanitize_text_field($_POST['va_venue_sunday_start']);
            $sun_end = sanitize_text_field($_POST['va_venue_sunday_end']);
            $offsite = sanitize_text_field($_POST['va_venue_offsite']);
            // Update the meta field in the database.
            update_post_meta($post_id, 'va_address', $address);
            update_post_meta($post_id, 'va_city', $city);
            update_post_meta($post_id, 'va_state', $state);
            update_post_meta($post_id, 'va_zipcode', $zipcode);
            update_post_meta($post_id, 'va_country', $country);
            update_post_meta($post_id, 'va_contact_email', $email);
            update_post_meta($post_id, 'va_phone', $phone);
            update_post_meta($post_id, 'va_website', $website);
            update_post_meta($post_id, 'va_venue_monday_start', $mon_start);
            update_post_meta($post_id, 'va_venue_monday_end', $mon_end);
            update_post_meta($post_id, 'va_venue_tuesday_start', $tues_start);
            update_post_meta($post_id, 'va_venue_tuesday_end', $tues_end);
            update_post_meta($post_id, 'va_venue_wednesday_start', $wed_start);
            update_post_meta($post_id, 'va_venue_wednesday_end', $wed_end);
            update_post_meta($post_id, 'va_venue_thursday_start', $thurs_start);
            update_post_meta($post_id, 'va_venue_thursday_end', $thurs_end);
            update_post_meta($post_id, 'va_venue_friday_start', $fri_start);
            update_post_meta($post_id, 'va_venue_friday_end', $fri_end);
            update_post_meta($post_id, 'va_venue_saturday_start', $sat_start);
            update_post_meta($post_id, 'va_venue_saturday_end', $sat_end);
            update_post_meta($post_id, 'va_venue_sunday_start', $sun_start);
            update_post_meta($post_id, 'va_venue_sunday_end', $sun_end);
            update_post_meta($post_id, 'va_venue_offsite', $offsite);

            // check if default venue has been set and update if not
            $default_venue = get_option('va_default_venue');
            if(!$default_venue){
                update_option('va_default_venue', $post_id);
            }
        }
        function va_save_location_meta($post_id){
            // Check if our nonce is set.
            if(!isset($_POST['location_meta_nonce'])){return;}
            // Verify that the nonce is valid.
            if(!wp_verify_nonce($_POST['location_meta_nonce'], 'save_location_meta')){return;}
            // If this is an autosave, our form has not been submitted, so we don't want to do anything.
            if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE){return;}
            // Check the user's permissions.
            if(isset($_POST['post_type']) && 'va_location' == $_POST['post_type']){
                if(!current_user_can('edit_page', $post_id)){return;}
                if(!current_user_can('edit_post', $post_id)){return;}
            }
            // sanitize data
            $venue_id = sanitize_text_field($_POST['va_venue_id']);
            $avail = sanitize_text_field($_POST['va_venue_availability']);
            $mon_start = sanitize_text_field($_POST['va_location_monday_start']);
            $mon_end = sanitize_text_field($_POST['va_location_monday_end']); 
            $tues_start = sanitize_text_field($_POST['va_location_tuesday_start']);
            $tues_end = sanitize_text_field($_POST['va_location_tuesday_end']);            
            $wed_start = sanitize_text_field($_POST['va_location_wednesday_start']);
            $wed_end = sanitize_text_field($_POST['va_location_wednesday_end']);            
            $thurs_start = sanitize_text_field($_POST['va_location_thursday_start']);
            $thurs_end = sanitize_text_field($_POST['va_location_thursday_end']);            
            $fri_start = sanitize_text_field($_POST['va_location_friday_start']);
            $fri_end = sanitize_text_field($_POST['va_location_friday_end']);            
            $sat_start = sanitize_text_field($_POST['va_location_saturday_start']);
            $sat_end = sanitize_text_field($_POST['va_location_saturday_end']);            
            $sun_start = sanitize_text_field($_POST['va_location_sunday_start']);
            $sun_end = sanitize_text_field($_POST['va_location_sunday_end']);
            // Update the meta field in the database.
            update_post_meta($post_id, 'va_venue_id', $venue_id);
            update_post_meta($post_id, 'va_venue_availability', $avail);
            update_post_meta($post_id, 'va_location_monday_start', $mon_start);
            update_post_meta($post_id, 'va_location_monday_end', $mon_end);
            update_post_meta($post_id, 'va_location_tuesday_start', $tues_start);
            update_post_meta($post_id, 'va_location_tuesday_end', $tues_end);
            update_post_meta($post_id, 'va_location_wednesday_start', $wed_start);
            update_post_meta($post_id, 'va_location_wednesday_end', $wed_end);
            update_post_meta($post_id, 'va_location_thursday_start', $thurs_start);
            update_post_meta($post_id, 'va_location_thursday_end', $thurs_end);
            update_post_meta($post_id, 'va_location_friday_start', $fri_start);
            update_post_meta($post_id, 'va_location_friday_end', $fri_end);
            update_post_meta($post_id, 'va_location_saturday_start', $sat_start);
            update_post_meta($post_id, 'va_location_saturday_end', $sat_end);
            update_post_meta($post_id, 'va_location_sunday_start', $sun_start);
            update_post_meta($post_id, 'va_location_sunday_end', $sun_end);
        }

        function va_save_reservation_meta($post_id){
            if(!$this->va_settings['saving_reservation_meta']){
                // set value to avoid infinite save loop
                $this->va_settings['saving_reservation_meta'] = true;
      
                // Check if our nonce is set.
                if(!isset($_POST['reservation_meta_nonce'])){return;}
                // Verify that the nonce is valid.
                if(!wp_verify_nonce($_POST['reservation_meta_nonce'], 'save_reservation_meta')){return;}
                // If this is an autosave, our form has not been submitted, so we don't want to do anything.
                if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE){return;}
                // Check the user's permissions.
                if(isset($_POST['post_type']) && 'va_reservation' == $_POST['post_type']){
                    if(!current_user_can('edit_page', $post_id)){return;}
                    if(!current_user_can('edit_post', $post_id)){return;}
                }
                // get current status
                $old_status = get_post_meta($post_id, 'va_reservation_status', true);

                // sanitize data
                $status = sanitize_text_field($_POST['va_reservation_status']);
                $venue_id = sanitize_text_field($_POST['va_venue_id']);
                $location_id = $_POST['va_location_id']; // array, santized later in foreach
                $date = sanitize_text_field($_POST['va_reservation_date']);
                $start_setup_time = sanitize_text_field($_POST['va_start_setup_time']);
                $end_cleanup_time = sanitize_text_field($_POST['va_end_cleanup_time']);
                $start_time = sanitize_text_field($_POST['va_start_time']);
                $end_time = sanitize_text_field($_POST['va_end_time']);
                $name = sanitize_text_field($_POST['va_reservation_name']);
                $phone = sanitize_text_field($_POST['va_reservation_phone']);
                $email = sanitize_text_field($_POST['va_reservation_email']);
                $comments = sanitize_text_field($_POST['va_reservation_comments']);
                $setup_needs = sanitize_text_field($_POST['va_reservation_setup']);
                $av_needs = sanitize_text_field($_POST['va_reservation_av']);


                // Update the meta field in the database.
                update_post_meta($post_id, 'va_reservation_status', $status);
                update_post_meta($post_id, 'va_venue_id', $venue_id);
                update_post_meta($post_id, 'va_location_id', $location_id);
                update_post_meta($post_id, 'va_reservation_date', $date);
                update_post_meta($post_id, 'va_start_time', $start_time);
                update_post_meta($post_id, 'va_end_time', $end_time);
                update_post_meta($post_id, 'va_reservation_name', $name);
                update_post_meta($post_id, 'va_reservation_phone', $phone);
                update_post_meta($post_id, 'va_reservation_email', $email);
                update_post_meta($post_id, 'va_reservation_comments', $comments);
                update_post_meta($post_id, 'va_reservation_setup', $setup_needs);
                update_post_meta($post_id, 'va_reservation_av', $av_needs);

                $push = null;
                if(isset($_POST['va_push_to_ecp'])){
                    $push = sanitize_text_field($_POST['va_push_to_ecp']);
                }
                update_post_meta($post_id, 'va_push_to_ecp', $push);

                if($start_setup_time){
                    update_post_meta($post_id, 'va_start_setup_time', $start_setup_time);
                }else{
                    update_post_meta($post_id, 'va_start_setup_time', $start_time);
                }
              
                if($end_cleanup_time){
                    update_post_meta($post_id, 'va_end_cleanup_time', $end_cleanup_time);
                }else{
                    update_post_meta($post_id, 'va_end_cleanup_time', $end_time);
                }

                // check for status change to send notification
                if($this->va_settings['user_approved_notification'] == 'yes'){
                    if(($old_status != $status) && ($status != 'pending')){
                        if(empty($this->va_settings['user_subject_line_approved'])){
                            $subject = 'Your '.$this->va_settings['reservation_single'].' has been '.ucfirst($status);
                        }else{
                            $subject = $this->va_settings['user_subject_line_approved'];
                        }
                        $content = '<p>The following '.$this->va_settings['reservation_single'].' has been '.ucfirst($status).'.</p>';
                        $content .= '<ul><li><strong>Title: </strong> '.get_the_title($post_id).'</li>';
                        $content .= '<li><strong>'.$this->va_settings['venue_single'].': </strong> '.get_the_title($venue_id).'</li>';
                        $first = true;
                        foreach($location_id as $location){
                            if($first){
                                $first = false;
                                $location_names = get_the_title($location);
                            }else{
                                $location_names .= ', '.get_the_title($location);
                            }
                        }
                        $content .= '<li><strong>'.$this->va_settings['location_plural'].': </strong> '.$location_names.'</li>';
                        $content .= '<li><strong>Date: </strong> '.date('m/d/Y', strtotime($date)).'</li>';
                        if($start_setup_time){
                            $content .= '<li><strong>Start Setup Time: </strong> '.date('g:i a', strtotime($start_setup_time)).'</li>';
                        }
                        $content .= '<li><strong>Start Time: </strong> '.date('g:i a', strtotime($start_time)).'</li>';
                        $content .= '<li><strong>End Time: </strong> '.date('g:i a', strtotime($end_time)).'</li>';
                        if($end_cleanup_time){
                            $content .= '<li><strong>End Cleanup Time: </strong> '.date('g:i a', strtotime($end_cleanup_time)).'</li></ul>';
                        }
                        $content .= '<br/><p><strong>Comments:</strong><br/>';
                        $content .= $comments .'</p><br/>';
                        $content .= '<p>'.nl2br(get_option('va_notification_footer')).'</p>';
                        $this->va_send_notification($email, $subject, $content);
                    }
                }
            }
        }

        function va_save_submitted_reservation(){
            // Check if our nonce is set.
            if(!isset($_POST['va_save_submitted_reservation_nonce'])){return;}
            // Verify that the nonce is valid.
            if(!wp_verify_nonce($_POST['va_save_submitted_reservation_nonce'], 'va_save_submitted_reservation')){return;}
            
            // sanitize data
            if(in_array('title',$this->va_settings['show_form_fields'])){
                $title = sanitize_text_field($_POST['va_reservation_title']);
            }else{
                $title = sanitize_text_field($_POST['va_reservation_name']);
            }
            if(in_array('description',$this->va_settings['show_form_fields'])){
                $post_content = sanitize_text_field($_POST['va_reservation_content']);
            }else{
                $post_content = '';
            }
            $venue_id = sanitize_text_field($_POST['va_venue_id']);
            $location_ids = $_POST['va_location_id'];
            $date = sanitize_text_field($_POST['va_reservation_dates']);
            if(in_array('setup_time',$this->va_settings['show_form_fields'])){
                $start_setup_time = sanitize_text_field($_POST['va_start_setup_time']);
            }else{
                $start_setup_time = false;
            }
            if(in_array('cleanup_time',$this->va_settings['show_form_fields'])){
                $end_cleanup_time = sanitize_text_field($_POST['va_end_cleanup_time']);
            }else{
                $end_cleanup_time = false;
            }
            $start_time = sanitize_text_field($_POST['va_start_time']);
            if(in_array('end_time',$this->va_settings['show_form_fields'])){
                $end_time = sanitize_text_field($_POST['va_end_time']);
            }else{
                $start_time_temp = strtotime($start_time);
                $end_time_temp = strtotime("+".$this->va_settings['end_time_length_hr']." hours", $start_time_temp);
                $end_time = date('h:i',strtotime("+".$this->va_settings['end_time_length_min']." minutes", $end_time_temp));
            }
            $name = sanitize_text_field($_POST['va_reservation_name']);
            if(in_array('phone',$this->va_settings['show_form_fields'])){
                $phone = sanitize_text_field($_POST['va_reservation_phone']);
            }else{
                $phone = '';
            }
            $email = sanitize_text_field($_POST['va_reservation_email']);
            $admin_notification_label = sanitize_text_field($_POST['va_reservation_send_to']);
            $admin_notification_email = $this->va_settings['admin_email_'.$admin_notification_label];
			if(in_array('setup_needs',$this->va_settings['show_form_fields'])){
                $setup_needs = sanitize_text_field($_POST['va_reservation_setup']);
            }else {
                $setup_needs = '';
            }
            if(in_array('av_needs',$this->va_settings['show_form_fields'])){
			 $av_needs = sanitize_text_field($_POST['va_reservation_av']);
            }else {
                $av_needs = '';
            }
            
            if($start_setup_time){
                $reservation_start = $start_setup_time;
            }else{
                $reservation_start = $start_time;
            }            
            if($end_cleanup_time){
                $reservation_end = $end_cleanup_time;
            }else{
                $reservation_end = $end_time;
            }

            $message = '';

            // check if start time is before end time
            if($reservation_start < $reservation_end){

                // check for multiple dates
                $dates = explode(', ', $date);

                // start with empty conflict array
                $conflicts = array();
                $successes = array();

                foreach($dates as $date){
                    // conflict flag for this date
                    $conflict = false;

                    // fix date format
                    $date = date('Y-m-d',strtotime($date));

                    // check if location is unavailable for time selected
                    foreach($location_ids as $location_id){
                        $location_id = sanitize_text_field($location_id);

                        // get availability
                        $location_availability = get_post_meta($location_id, 'va_venue_availability', true);
                        if($location_availability == 'custom'){
                            $location_start = get_post_meta($location_id, 'va_location_'.strtolower(date('l', strtotime($date))).'_start', true);
                            $location_end = get_post_meta($venue_id, 'va_location_'.strtolower(date('l', strtotime($date))).'_start', true);
                        }
                        else{
                            $venue_id = get_post_meta($location_id, 'va_venue_id', true);
                            $location_start = get_post_meta($venue_id, 'va_venue_'.strtolower(date('l', strtotime($date))).'_start', true);
                            $location_end = get_post_meta($venue_id, 'va_venue_'.strtolower(date('l', strtotime($date))).'_end', true);
                        }

                        // first check availability conflict
                        if(!$location_start && !$location_end){
                            $conflicts[$date] = get_the_title($location_id) . ' ' . $this->va_settings['reservation_plural'] . ' are unavailable <strong>all day</strong> on ' . date('l', strtotime($date)) . 's';
                            $conflict = true;
                        }else if($reservation_start < $location_start){
                            $conflicts[$date] = get_the_title($location_id) . ' ' . $this->va_settings['reservation_plural'] . ' are not available <strong>before</strong> ' . date('g:i a', strtotime($location_start)) . ' on ' . date('l', strtotime($date)) . 's';
                            $conflict = true;
                        }else if($reservation_end > $location_end){
                            $conflicts[$date] = get_the_title($location_id) . ' ' . $this->va_settings['reservation_plural'] . ' are not available <strong>after</strong> ' . date('g:i a', strtotime($location_start)) . ' on ' . date('l', strtotime($date)) . 's';;
                            $conflict = true;
                        }else{
                            // then check for existing reservation conflicts
                            $args = array(
                                'post_type' => 'va_reservation',
                                'post_status' => 'publish',
                                'meta_query' => array(
                                    'relation' => 'AND',
                                    array(
                                        'key' => 'va_reservation_date',
                                        'value' => $date,
                                        'compare' => '='
                                    ),  
                                    array(
                                        'key' => 'va_start_setup_time',
                                        'value' => $reservation_end,
                                        'compare' => '<'
                                    ),
                                    array(
                                        'key' => 'va_end_cleanup_time',
                                        'value' => $reservation_start,
                                        'compare' => '>'
                                    )
                                )
                            );

                            $results = new WP_Query($args);
                            if($results->have_posts()) {
                                while($results->have_posts()) {
                                    $results->the_post();
                                    $locations = get_post_meta(get_the_ID(),'va_location_id',true);
                                    if($locations){
                                        // check for existing reservations in chosen location(s)
                                        $compare = array_intersect($locations, $location_ids);
                                        if(!empty($compare)){
                                            $conflicts[$date] = 'Existing ' . $this->va_settings['reservation_single'];
                                            $conflict = true;
                                        }
                                    }
                                }
                            }
                        }
                    }

                    if(!$conflict){
                        $successes[] = $date;

                        //if(false){ // testing without creating posts
                        $post = array(
                            'post_type' => 'va_reservation',
                            'post_title' => $title,
                            'post_content' => $post_content,
                            'post_status' => 'publish'
                        );
                        $post_id = wp_insert_post($post);
                        update_post_meta($post_id, 'va_reservation_status', 'pending');
                        update_post_meta($post_id, 'va_venue_id', $venue_id);
                        update_post_meta($post_id, 'va_location_id', $location_ids);
                        update_post_meta($post_id, 'va_reservation_date', $date);
                        update_post_meta($post_id, 'va_start_setup_time', $reservation_start);
                        update_post_meta($post_id, 'va_start_time', $start_time);
                        update_post_meta($post_id, 'va_end_time', $end_time);
                        update_post_meta($post_id, 'va_end_cleanup_time', $reservation_end);
                        update_post_meta($post_id, 'va_reservation_name', $name);
                        update_post_meta($post_id, 'va_reservation_phone', $phone);
                        update_post_meta($post_id, 'va_reservation_email', $email);
                        update_post_meta($post_id, 'va_reservation_setup', $setup_needs);
                        update_post_meta($post_id, 'va_reservation_av', $av_needs);
						
						// set ECP to on by default
						update_post_meta($post_id, 'va_push_to_ecp', 'on');

                        // send notifications
                        if($this->va_settings['admin_new_notification'] == 'yes'){
                            $subject = 'A new '.$this->va_settings['reservation_single'].' has been submitted for review';
                            $content = '<p>The following '.$this->va_settings['reservation_single'].' information has been submitted. <a href="'.admin_url().'post.php?post='.$post_id.'&action=edit">Edit this '.$this->va_settings['reservation_single'].' here</a>.</p>';
                            $content .= '<ul><li><strong>Title: </strong> '.get_the_title($post_id).'</li>';
                            $content .= '<li><strong>'.$this->va_settings['venue_single'].': </strong> '.get_the_title($venue_id).'</li>';
                            $first = true;
                            $location_names = '';
                            if($location_ids){
                                foreach($location_ids as $location){
                                    if($first){
                                        $first = false;
                                        $location_names = get_the_title($location);
                                    }else{
                                        $location_names .= ', '.get_the_title($location);
                                    }
                                }
                            }
                            $content .= '<li><strong>'.$this->va_settings['location_plural'].': </strong> '.$location_names.'</li>';
                            $content .= '<li><strong>Date: </strong> '.date('m/d/Y', strtotime($date)).'</li>';
                            if($start_setup_time){
                                $content .= '<li><strong>Setup Start Time: </strong> '.date('g:i a', strtotime($start_setup_time)).'</li>';
                            }
                            $content .= '<li><strong>'.$this->va_settings['reservation_single'].' Start Time: </strong> '.date('g:i a', strtotime($start_time)).'</li>';
                            $content .= '<li><strong>'.$this->va_settings['reservation_single'].' End Time: </strong> '.date('g:i a', strtotime($end_time)).'</li>';
                            if($end_cleanup_time){
                                $content .= '<li><strong>Cleanup End Time: </strong> '.date('g:i a', strtotime($end_cleanup_time)).'</li></ul>';
                            }
                            $content .= '<br/><p>Contact Information:<p>';
                            $content .= '<ul><li><strong>Name: </strong> '.$name.'</li>';
                            $content .= '<li><strong>Phone: </strong> '.$phone.'</li>';
                            $content .= '<li><strong>Email: </strong> '.$email.'</li>';
                            $content .= '<li><strong>Set Up Need: </strong> '.$setup_needs.'</li>';
                            $content .= '<li><strong>A/V Needs: </strong> '.$av_needs.'</li></ul>';
                            $content .= '<br/><p><strong>'.$this->va_settings['reservation_single'].' Description:</strong><br/>';
                            $content .= $post_content.'</p><br/>';
                            $content .= '<p>'.nl2br(get_option('va_notification_footer')).'</p>';
                            $this->va_send_notification($admin_notification_email, $subject, $content); 
                            $content = '';
                        }

                        if($this->va_settings['user_new_notification'] == 'yes'){
                            if(empty($this->va_settings['user_subject_line_new'])){
                                 $subject = 'Thank you for submitting your '.$this->va_settings['reservation_single'].' request!';
                            }else{
                                $subject = $this->va_settings['user_subject_line_new'];
                            }

                            $email_header = get_option('va_notification_header');
                            if(empty($email_header)){
                                $content = '<p>The following '.$this->va_settings['reservation_single'].' information has been submitted.</p>';
                            }else{
                                $content .= '<p>'.nl2br(get_option('va_notification_header')).'</p>';
                            }
                                   
                            $content .= '<ul><li><strong>Title: </strong> '.get_the_title($post_id).'</li>';
                            $content .= '<li><strong>'.$this->va_settings['venue_single'].': </strong> '.get_the_title($venue_id).'</li>';
                            $first = true;
                            $location_names = '';
                            if($location_ids){
                                foreach($location_ids as $location){
                                    if($first){
                                        $first = false;
                                        $location_names = get_the_title($location);
                                    }else{
                                        $location_names .= ', '.get_the_title($location);
                                    }
                                }
                            }
                            $content .= '<li><strong>'.$this->va_settings['location_plural'].': </strong> '.$location_names.'</li>';
                            $content .= '<li><strong>Date: </strong> '.date('m/d/Y', strtotime($date)).'</li>';
                            if($start_setup_time){
                                $content .= '<li><strong>Setup Start Time: </strong> '.date('g:i a', strtotime($start_setup_time)).'</li>';
                            }
                            $content .= '<li><strong>'.$this->va_settings['reservation_single'].' Start Time: </strong> '.date('g:i a', strtotime($start_time)).'</li>';
                            $content .= '<li><strong>'.$this->va_settings['reservation_single'].' End Time: </strong> '.date('g:i a', strtotime($end_time)).'</li>';
                            if($end_cleanup_time){
                                $content .= '<li><strong>Cleanup End Time: </strong> '.date('g:i a', strtotime($end_cleanup_time)).'</li>';
                            }                            
							if($setup_needs){
                                $content .= '<li><strong>Set Up Needs: </strong> '.$setup_needs.'</li>';
                            }                            
							if($av_needs){
                                $content .= '<li><strong>A/V Needs: </strong> '.$av_needs.'</li>';
                            }
							$content .= '</ul>';
                            $content .= '<br/><p><strong>'.$this->va_settings['reservation_single'].' Description:</strong><br/>';
                            $content .= $post_content.'</p><br/>';
                            $content .= '<p>'.nl2br(get_option('va_notification_footer')).'</p>';
                            $this->va_send_notification(sanitize_text_field($_POST['va_reservation_email']), $subject, $content); 
                            $content = '';
                        }

                        //} // testing without creating posts
                    }
                }

            }else{
                // notify about start/end time error
                $message .= '<strong>ERROR:</strong> The selected end time (' . date("g:i a", strtotime($reservation_end)) . ') is before the selected start time (' .  date("g:i a", strtotime($reservation_start)) . ').';
                return $message;
            } 

            // set message to user
            if($this->va_settings['reservation_success_message'] != ''){
                // notify about successes
                if(!empty($successes)){
                    $message .= $this->va_settings['reservation_success_message'] . '<br/>';
                    $message .= '<br/><strong>SUCCESS:</strong> These dates had no conflicts.';
                    $message .= '<ul>';
                    foreach($successes as $success){
                        $message .= '<li>' . date('F jS, Y',strtotime($success)) . '</li>';
                    }
                    $message .= '</ul>';
                }
            }else{
                // notify about successes
                if(!empty($successes)){
                    $message .= 'Your ' . $this->va_settings['reservation_single'] . ' request(s) have been submitted for review.<br/>';
                    $message .= '<br/><strong>SUCCESS:</strong> These dates had no conflicts.';
                    $message .= '<ul>';
                    foreach($successes as $success){
                        $message .= '<li>' . date('F jS, Y',strtotime($success)) . '</li>';
                    }
                    $message .= '</ul>';
                }
            }

            // notify about conflicts
            if(!empty($conflicts)){
                $message .= '<strong>ERROR:</strong> These dates were not submitted due to conflicts.';
                $message .= '<ul>';
                foreach($conflicts as $conflict => $reason){
                    $message .= '<li>' . date('F jS, Y',strtotime($conflict)) . ' - ' . $reason .' </li>';
                }
                $message .= '</ul>';
            }

            return $message;
        }

        // [vacancy] shortcode
        function va_display_form($atts){
            global $post;
            $can_submit = false;
            if($this->va_settings['require_login'] == "yes"){
                if(is_user_logged_in()){
                    $can_submit = true;
                }else{
                    return 'You must be logged in to make a '. $this->va_settings['reservation_single'] . '. Please <a href="/wp-login.php?redirect_to='. get_permalink($post->ID).'">login here</a>.';
                }
            }else{
                $can_submit = true;
            }
            if($can_submit){
                ob_start();
                ?>
                <form id="va-day-form" method="post" action="">
                    <?php if(isset($_POST['va_venue_id'])) : ?>
                        <?php $venue_id = sanitize_text_field($_POST['va_venue_id']); ?>
                    <?php endif; ?>
                    <?php $venues = $this->va_get_venues(); ?>
                    <?php if($venues->have_posts()): ?>
                        <p>
                            <label>Select a <?php echo $this->va_settings['venue_single']; ?></label>
                            <select id="va-venue-id" name="va_venue_id">
                            <?php while($venues->have_posts()): $venues->the_post(); ?>
                                <option value="<?php the_ID();?>" 
                                    <?php 
                                        if(!empty($venue_id)){
                                            if($venue_id == get_the_ID()){echo 'selected';}
                                        }else if(get_option('va_default_venue') == get_the_ID()){echo 'selected';} 
                                    ?>
                                ><?php the_title(); ?></option>
                            <?php endwhile; ?>
                            </select>
                        </p>
                    <?php endif;?>
                    <p>
                        <label>Select a Date</label>
                        <input id="va-datepicker" type="text" value="<?php if(isset($_POST['va_date'])){echo date('m/d/Y', strtotime($_POST['va_date']));} ?>"/>
                        <input id="va-date" type="hidden" name="va_date" value="<?php if(isset($_POST['va_date'])){echo sanitize_text_field($_POST['va_date']);} ?>"/>
                    </p>
                    <?php if(isset($_POST['va_reservation_submitted'])) : ?>
                        <?php $message = $this->va_save_submitted_reservation(); ?>
                        <div class="va-reservation-success-message">
                            <?php echo $message; ?>
                        </div>
                    <?php endif; ?>
                    <input id="va-prev-next" type="hidden" name="va_prev_next"/>
                    <span class="va-clearer"></span>
                </form> 
                <span class="clearer"></span>
                <div id="va-shortcode-day"> 
                    <?php if(false) : // no more full calendar view ?>
                    <?php //if(empty($_POST)) : ?>
                        <h3><?php echo date('F Y', strtotime('now')); ?></h3>
                        <?php // draw table ?>
                        <table cellpadding="0" cellspacing="0" id="va-calendar-month" class="va-calendar">
                            <thead>
                                <?php //table headings ?>
                                <?php $headings = array('Sun','Mon','Tue','Wed','Thu','Fri','Sat'); ?>
                                <tr class="calendar-row">
                                    <th class="calendar-day-head">
                                        <?php echo implode('</th><th class="calendar-day-head">',$headings); ?>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                    // days and weeks vars now ...
                                    $month = date('m', strtotime('now'));
                                    $year = date('Y', strtotime('now'));
                                    $running_day = date('w',mktime(0,0,0,$month,1,$year));
                                    $days_in_month = date('t',mktime(0,0,0,$month,1,$year));
                                    $days_in_this_week = 1;
                                    $day_counter = 0;
                                    $today = strtotime('today');
                                    $dates_array = array();
                                ?>
                                <?php // row for week one ?>
                                <tr class="calendar-row">
                                <?php // print "blank" days until the first of the current week ?>
                                <?php for($x = 0; $x < $running_day; $x++): ?>
                                    <td class="calendar-day-np"></td>
                                    <?php $days_in_this_week++; ?>
                                <?php endfor; ?>
                                    <?php // keep going with days.... ?>
                                    <?php for($list_day = 1; $list_day <= $days_in_month; $list_day++): ?>
                                        <td class="calendar-day<?php if($today == mktime(0,0,0,$month,$list_day,$year)){echo ' today';}?>" data-date="<?php echo date('Y-m-d', strtotime("$year-$month-$list_day")); ?>">
                                            <?php // add in the day number ?>
                                            <div class="day-number"><?php echo $list_day; ?></div>
                                            <?php // get reservations ?>
                                            <?php $query_date = date('Y-m-d', strtotime("$year-$month-$list_day")); ?>
                                            <?php 
                                                $args = array(
                                                    'post_type' => 'va_reservation',
                                                    'posts_per_page' => -1,
                                                    'meta_query' => array(
                                                        'relation' => 'AND',
                                                        array(
                                                            'key' => 'va_reservation_date',
                                                            'value' => $query_date
                                                        ),
                                                        array(
                                                            'key' => 'va_venue_id',
                                                            'value' => $this->va_settings['default_venue']
                                                        )
                                                    ),
                                                );
                                            ?>
                                            <?php $reservations = new WP_Query($args); ?>
                                            <?php if($reservations->have_posts()) : ?>
                                                <?php while($reservations->have_posts()) : $reservations->the_post(); ?>
                                                    <?php $status = get_post_meta(get_the_ID(), 'va_reservation_status', true); ?>
                                                    <div class="reservation <?php echo $status; ?>"><?php the_title(); ?></div>
                                                <?php endwhile; ?>
                                            <?php endif; ?>
                                        </td>
                                        <?php if($running_day == 6) : ?>
                                        </tr>
                                        <?php if(($day_counter+1) != $days_in_month) : ?>
                                            <tr class="calendar-row">
                                        <?php endif; ?>
                                        <?php $running_day = -1; ?>
                                        <?php $days_in_this_week = 0; ?>
                                    <?php endif; ?>
                                    <?php $days_in_this_week++; $running_day++; $day_counter++; ?>
                                <?php endfor; ?>
                                <?php // finish the rest of the days in the week ?>
                                <?php if($days_in_this_week < 8) : ?>
                                    <?php for($x = 1; $x <= (8 - $days_in_this_week); $x++) : ?>
                                        <td class="calendar-day-np"> </td>
                                    <?php endfor; ?>
                                <?php endif; ?>
                                </tr>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
                <script type="text/javascript">
                    jQuery(document).ready(function($){

                        // jquery datepicker
                        $('#va-datepicker').datepicker({
                            dateFormat: "mm/dd/yy",
                            altField: "#va-date",
                            altFormat: "yy-mm-dd",
                            inline: true,
                            showOtherMonths: true,
                            showOn: "both", 
                            buttonText: "<i class='icon-calendar'></i>",
                            onSelect: function(selectedDate){
                                $('#va-date').trigger('change');
                            }
                        });

                        // set today for default if blank
                        if(!($('#va-date').val().length > 0)){
                            $('#va-datepicker').datepicker('setDate', new Date());
                        }

                        // ajax call to draw day
                        $(document).on('change', 'input[name="va_date"]', function(){
                            $('#va-shortcode-day').html('<img src="<?php echo $this->va_settings['dir']; ?>images/loading.gif" alt="loading"/>');
                            var venue_id = $('#va-venue-id').val();
                            var date = $(this).val();
                            var data = {
                                'action': 'va_draw_shortcode_day',
                                'venue_id': venue_id,
                                'date': date
                            };

                            $.post("<?php echo admin_url('admin-ajax.php'); ?>", data, function(response){
                                $('#va-shortcode-day').html(response);
                                var width = $('#va-day-view').width() - 100;
                                var num_cols = $('#va-day-view th').length / 2; // account for floating header
                                var col_width = width / num_cols;
                                $('#va-day-view th').attr('width', col_width+'px');
                                $('#va-times').attr('width', '100px');
                            }); 
                        });    

                        $('select[name="va_venue_id"]').on('change',function(){
                            var venue_id = $(this).val();
                            var date = $('input[name="va_date"]').val();
                            var data = {
                                'action': 'va_draw_shortcode_day',
                                'venue_id': venue_id,
                                'date': date
                            };
        
                            $.post("<?php echo admin_url('admin-ajax.php'); ?>", data, function(response){
                                $('#va-shortcode-day').html(response);
                                var width = $('#va-day-view').width() - 100;
                                var num_cols = $('#va-day-view th').length / 2; // account for floating header
                                var col_width = width / num_cols;
                                $('#va-day-view th').attr('width', col_width+'px');
                                $('#va-times').attr('width', '100px');
                            }); 
                        }); 
                        // check for posted date
                        if($('#va-date').val().length){
                            $('#va-date').trigger('change');
                        }
                        // calendar click
                        $('.calendar-day').on('click', function(){
                            $('input[name="va_date"]').val($(this).attr('data-date'));
                            $('#va-day-form').submit();
                        });
                    });
                </script>
               <?php return ob_get_clean();
            }
        }

        // frontend display - ajax request
        function va_draw_shortcode_day(){
            ob_start();
            ?>
            <div id="va-table-wrap">
            <?php if(!empty($_POST['date']) && isset($_POST['venue_id'])) : ?>
                <?php $date = sanitize_text_field($_POST['date']); ?>
                <?php $venue_id = sanitize_text_field($_POST['venue_id']); ?>
                <?php $offsite_venue = get_post_meta($venue_id, 'va_venue_offsite', true); ?>
                <?php if($offsite_venue == 'yes') : ?>
                    <h3><?php echo get_the_title($venue_id); ?> <?php echo $this->va_settings['reservation_single']; ?></h3>
                    <?php echo $this->va_make_reservation($venue_id, true); ?>
                <?php else : ?>
                    <?php $venue_start = get_post_meta($venue_id, 'va_venue_'.strtolower(date('l',strtotime($date))).'_start', true); ?>
                    <?php $venue_end = get_post_meta($venue_id, 'va_venue_'.strtolower(date('l',strtotime($date))).'_end', true); ?>
                      
                    <h3>
                        <?php echo date('l - F jS, Y', strtotime($date)); ?>
                        <button class="va-prev-day" data-date="<?php echo date('Y-m-d', strtotime($date . ' -1 day')); ?>"><i class="icon-double-angle-left"></i> prev</button>
                        <button class="va-next-day" data-date="<?php echo date('Y-m-d', strtotime($date . ' +1 day')); ?>">next <i class="icon-double-angle-right"></i></button>
                        <span class="va-clearer"></span>
                    </h3>
                    <?php $locations = new WP_Query('post_type=va_location&posts_per_page=-1&meta_key=va_venue_id&meta_value='.$venue_id); ?>
                    <?php if($locations) : ?>
                        <table cellpadding="0" cellspacing="0" id="va-day-view">
                            <thead>
                                <tr class="va-header-row" <?php echo apply_filters('va_table_header_background', ''); ?>>
                                    <th id="va-times"></th>
                                <?php foreach($locations->posts as $location) : ?>
                                    <th><?php echo $location->post_title; ?></th>
                                <?php endforeach; ?>
                                </tr>
                            </thead>
                            <tbody>
                            <?php 
                                $args = array(
                                    'post_type' => 'va_reservation',
                                    'posts_per_page' => -1,
                                    'meta_query' => array(
                                        'relation' => 'AND',
                                        array(
                                            'key' => 'va_venue_id',
                                            'value' => $venue_id
                                        ),
                                        array(
                                            'key' => 'va_reservation_date',
                                            'value' => $date
                                        ),
                                    ),
                                ); 
                            ?>
                            <?php $reservations = new WP_Query($args); ?>
                            <?php $times = $this->va_get_times($this->va_settings['day_start_time'],$this->va_settings['day_end_time'],0.25); ?>
                            <?php if($times) : ?>
                                <?php foreach($times as $time) : ?>
                                    <tr>
                                        <td class="time"><?php echo date('g:i a',strtotime($time)); ?></td>
                                    <?php foreach($locations->posts as $location) : ?>
                                        <td id="<?php echo $venue_id . '-' . $location->ID . '-' . $date . '-' . str_replace(':',':',$time); ?>"
                                            <?php 
                                                $status = 'not-available';
												
												// check for custom availability 
                                                if(get_post_meta($location->ID, 'va_venue_availability', true) == 'custom'){ 
                                                    $location_start = get_post_meta($location->ID, 'va_location_'.strtolower(date('l',strtotime($date))).'_start', true);
                                                    $location_end = get_post_meta($location->ID, 'va_location_'.strtolower(date('l',strtotime($date))).'_end', true);
													
                                                    if(($location_start <= date('H:i', strtotime($time))) && (date('H:i', strtotime($time)) <= $location_end)){
														$status = 'available';
													}
												}
												else{
													//check location availability
													if(($venue_start <= date('H:i', strtotime($time))) && (date('H:i', strtotime($time)) <= $venue_end)){
														$status = 'available';
													}
                                                } 
												
											?>
											class="<?php echo $status; ?>" <?php echo apply_filters('va_unavailable_background', '', $status); ?>>
                                      
                                            <?php if($reservations) : ?>
                                                <?php foreach($reservations->posts as $reservation) : ?>
                                                    <?php $found = false; ?>
                                                    <?php if(is_array(get_post_meta($reservation->ID, 'va_location_id', true))) : ?>
                                                        <?php if(in_array($location->ID, get_post_meta($reservation->ID, 'va_location_id', true)) && $time >= get_post_meta($reservation->ID, 'va_start_setup_time',true) && $time < get_post_meta($reservation->ID, 'va_end_cleanup_time',true)) : ?>
                                                            <?php $found = true; ?>
                                                        <?php endif; ?>
                                                    <?php elseif(get_post_meta($reservation->ID, 'va_location_id', true) == $location->ID && $time >= get_post_meta($reservation->ID, 'va_start_setup_time',true) && $time < get_post_meta($reservation->ID, 'va_end_cleanup_time',true)) : ?>
                                                            <?php $found = true; ?>
                                                        <?php endif; ?>
                                                    <?php if($found) : ?>
                                                        <?php $start = get_post_meta($reservation->ID, 'va_start_setup_time', true); ?>
                                                        <?php $end = get_post_meta($reservation->ID, 'va_end_cleanup_time', true); ?>
                                                        <?php $diff = (strtotime($end) - strtotime($start))/60; ?>
                                                        <?php $height = ($diff/15) * 30; ?>
                                                        <?php $status = get_post_meta($reservation->ID, 'va_reservation_status', true); ?>
                                                        <?php if($status != 'denied' && $status != 'private') : ?>
                                                            <?php if($start == $time) : ?>
                                                                <div class="reservation <?php echo $status; ?>" style="height:<?php echo $height; ?>px;<?php echo apply_filters('va_reservation_background', '', $status); ?>">
                                                                    <?php if($this->va_settings['show_reservation_details'] == 'yes') : ?>
                                                                        <?php echo $reservation->post_title . ' (' . date('g:i a', strtotime($start)) . ' - ' . date('g:i a', strtotime($end)) . ')'; ?>
                                                                    <?php else : ?>
                                                                        <?php echo date('g:i a', strtotime($start)) . ' - ' . date('g:i a', strtotime($end)); ?>
                                                                    <?php endif; ?>
                                                                </div>
                                                            <?php else : ?>
                                                                <div></div>
                                                            <?php endif; ?>
                                                        <?php endif; ?>
                                                    <?php endif; ?>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </td>
                                    <?php endforeach; ?>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    <?php else : ?>
                        <p>Sorry, no <?php echo $this->va_settings['location_plural']; ?> were found.</p>
                    <?php endif; ?>
                    <a style="display:none;" id="va-thickbox-link" class="thickbox" title="<?php echo $this->va_settings['reservation_single']; ?> details" href="#TB_inline?width=600&height=550&inlineId=va-reservation-form">click</a>
                    <div id="va-reservation-form-wrap" style="display:none;"></div>
                    <script type="text/javascript">
                        jQuery(document).ready(function($){
                            if($('#wpadminbar').length > 0){ 
                                $("#va-day-view").floatThead({
                                    useAbsolutePositioning: false,
                                    scrollingTop: 32
                                });
                               
                            }else{
                               $("#va-day-view").floatThead({
                                    useAbsolutePositioning: false
                                });
                            }
                            
                            $("#va-day-view td").each(function(){
                                if($(this).children('div').length > 0){$(this).toggleClass('available not-available');}
                            });
                            // prev next buttons
                            $('.va-prev-day, .va-next-day').on('click', function(){
                                $('#va-date').val($(this).attr('data-date'));
                                $('#va-prev-next').val('1');
                                $('#va-day-form').submit();
                            });

                            // drag select dates
                            // $("#va-day-view").selectable({
                            //     filter:'td.available',
                            //     stop: function(event, ui){
                             
                            //         // get selected cells
                            //         var selected = new Array();
                            //         $('.ui-selected').each(function(){
                            //             selected.push($(this).attr('id'));
                            //         });
                            //         // check if anything was selected
                            //         if(selected.length > 0){
                            //             // make ajax call to check for availability and/or book reservation
                            //             var data = {
                            //                 'action' : 'va_make_reservation',
                            //                 'selected' : selected,
                            //                 'date' : "<?php echo $date; ?>"
                            //             };
                            //             $.post("<?php echo admin_url('admin-ajax.php'); ?>", data, function(response){
                            //                 $('#va-reservation-form-wrap').html(response);
                            //                 $('#va-thickbox-link').trigger('click');
                            //             }); 
                            //         }
                            //     }
                            // });

                            var clicked = false;
                            $('#va-day-view .available').on('click', function(){ 
                                $('.va-editing').removeClass('va-editing').html('');
                                $(this).addClass('va-editing').html('Selected <i class="icon-ok"></i>');
                                var $clicked = $(this);
                                var data = {
                                    'action' : 'va_make_reservation',
                                    'selected' : $(this).attr('id'),
                                    'date' : "<?php echo $date; ?>"
                                };
                                if(!clicked){
                                    $.post("<?php echo admin_url('admin-ajax.php'); ?>", data, function(response){
                                        //$('#va-reservation-form-wrap').html(response);

                                        $('#va-added-tr').remove();

                                        // count table columns
                                        var cols = 0;
                                        $('#va-day-view tr:nth-child(1) td').each(function () {
                                            cols++;
                                        });
                                        var res_var = "<?php echo $this->va_settings['reservation_single']; ?>";
                                        $clicked.parents('tr:first').after('<tr id="va-added-tr"><td colspan="'+cols+'" style="padding:0;"><div id="va-clicked-form" style="display:none;"><h6>Submit '+ res_var +' requests using the form below <span>close <i class="icon-remove-sign"></i></h6>'+response+'</div></td></tr>');
                                        $('#va-clicked-form').slideToggle('fast');
                                        //$('#va-thickbox-link').trigger('click');
                                        clicked = false;
                                    });
                                }
                                clicked = true; 
                            });

                            // remove form when closed is clicked
                            $(document).on('click', '#va-day-view #va-clicked-form h6 span', function(){
                                $('#va-day-view #va-added-tr').remove();
                                $('.va-editing').removeClass('va-editing').html('');
                            });

                            // make thickbox responsive
                            var maxwidth = 600;
                            var maxheight = 800;
                            $(window).on('resize',function(){
                                var width = window.innerWidth - 80;
                                if(width > maxwidth){width = maxwidth;}
                                var height = window.innerHeight - 80;
                                if(height > maxheight){height = maxheight;}
                                var link = "#TB_inline?width="+width+"&height="+height+"&inlineId=va-reservation-form-wrap";
                                $("#va-thickbox-link").attr("href",link);
                            }).resize();
                        });
                    </script>
                <?php endif; ?>
            <?php else : ?>
                <p>Please Select a date.</p>
            <?php endif; ?>
            </div>
            <?php echo ob_get_clean();
            die();
        }
        // ajax call from lightbox
        function va_make_reservation($venue_id = '', $offsite = false){
            ?>
            <form id="va-reservation-form" method="post" action="">
               <?php 
                    // venue_id - location_id - year - month - day - time
                    //array(4) { 
                    //     [0]=> string(23) "260-262-2014-10-31-1515" 
                    //     [1]=> string(23) "260-262-2014-10-31-1530" 
                    //     [2]=> string(23) "260-262-2014-10-31-1545" 
                    //     [3]=> string(23) "260-262-2014-10-31-1600" 
                    // }
        
                    $location_ids = array();
                    $date = sanitize_text_field($_POST['date']);
                    $start_time = 9999;
                    $end_time = 0;
                    $selected_time = '';
                    if(isset($_POST['selected'])){
                        $selected_time = sanitize_text_field($_POST['selected']);
                        $data = explode('-', $selected_time);
                        $venue_id = $data[0];
                        $location_id = $data[1];
                        $year = $data[2];
                        $month = $data[3];
                        $day = $data[4];
                        $start_time = $data[5];
                    }
                ?>    

                <div class="va-lightbox-form">
                    <label><strong><?php echo $this->va_settings['reservation_single']; ?> date(s):</strong> <span id="va-reservation-dates-display"><?php echo date('m/d/Y', strtotime($date)); ?></span>&nbsp;&nbsp;<span id="va-edit-dates">[edit]</span></label>
                    <div id="va-lightbox-datepicker" style="display:none;"></div>
                    <input type="hidden" id="va-reservation-dates" name="va_reservation_dates" value="<?php echo date('m/d/Y', strtotime($date)); ?>" />
                    <span class="va-clearer"></span>
                    
                    <?php $setup_cleanup = $this->va_settings['setup_cleanup']; ?>
                    <div class="va-start">
                        <label><strong>Start <?php echo $this->va_settings['reservation_single']; ?> at:</strong> <?php echo $this->va_get_time_select('va_start_time', $start_time, null, true); ?></label>
                        <?php if(in_array('setup_time', $this->va_settings['show_form_fields'])) : ?>
                            <div class="va-setup-time" <?php if($setup_cleanup == 'no'){echo 'style="display:none;"';}?>>
                                <label>Do you need setup time before your <?php echo $this->va_settings['reservation_single']; ?> begins?
                                    <select name="va_need_setup_time">
                                        <option value="no">No</option>
                                        <option value="yes">Yes</option>
                                    </select>
                                </label> 
                                <div id="va-setup-time" style="display:none;">
                                    <label>Start Setup before <?php echo $this->va_settings['reservation_single']; ?> at:
                                        <?php echo $this->va_get_time_select('va_start_setup_time',null,null,false,false,$start_time); ?>
                                    </label> 
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="va-end">
                        <?php if(in_array('end_time', $this->va_settings['show_form_fields'])) : ?>
                            <label><strong>End <?php echo $this->va_settings['reservation_single']; ?> at:</strong> <?php echo $this->va_get_time_select('va_end_time', null, null, true, $start_time); ?></label>
                        <?php else : ?>
                            <br/>
                            <label><?php echo $this->va_settings['reservation_plural']; ?> last for: <?php echo $this->va_settings['end_time_length_hr']; ?> hr <?php echo $this->va_settings['end_time_length_min']; ?> min</label>
                        <?php endif; ?>
                        <?php if(in_array('cleanup_time', $this->va_settings['show_form_fields'])) : ?>
                            <div class="va-cleanup-time" <?php if($setup_cleanup == 'no'){echo 'style="display:none;"';}?>>
                                <label>Do you need cleanup time after your <?php echo $this->va_settings['reservation_single']; ?> ends?
                                    <select name="va_need_cleanup_time">
                                        <option value="no">No</option>
                                        <option value="yes">Yes</option>
                                    </select>
                                </label>
                                <div id="va-cleanup-time" style="display:none;">
                                    <label>End Cleanup after <?php echo $this->va_settings['reservation_single']; ?> at:
                                        <?php echo $this->va_get_time_select('va_end_cleanup_time',null,null,false,$start_time); ?>
                                    </label> 
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                    <span class="va-clearer"></span>
                    
                    <?php if(in_array('title', $this->va_settings['show_form_fields'])) : ?>
                        <label><?php echo $this->va_settings['reservation_single']; ?> Title </label>
                        <input type="text" name="va_reservation_title" value="<?php if(isset($_COOKIE['va_reservation_title'])){echo stripslashes(sanitize_text_field($_COOKIE['va_reservation_title']));} ?>" required />
                    <?php endif; ?>
                    <span class="va-clearer"></span>

                    <?php if(in_array('venue', $this->va_settings['show_form_fields'])) : ?>
                        <div class="va-venue">
                            <label><?php echo $this->va_settings['venue_single']; ?>:</label>
                            <?php if($offsite) : ?>
                                <p><?php echo get_the_title($venue_id); ?></p>
                                <input type="hidden" name="va_venue_id" value="<?php echo $venue_id; ?>" />
                            <?php else : ?>
                                <?php $venues = $this->va_get_venues(); ?>
                                <?php if($venues->have_posts()) : ?>
                                    <select id="va-venue-id" name="va_venue_id" required>
                                    <?php while($venues->have_posts()) : $venues->the_post(); ?>
                                        <option value="<?php the_ID();?>" 
                                            <?php if($venue_id == get_the_ID()){echo 'selected';} ?>
                                        ><?php the_title(); ?></option>
                                    <?php endwhile; ?>
                                    </select>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    <?php else : ?>
                        <input type="hidden" name="va_venue_id" value="<?php echo $venue_id; ?>" />
                    <?php endif; ?>

                    <?php if(in_array('location', $this->va_settings['show_form_fields'])) : ?>
                        <div class="va-locations">
                            <label><?php echo $this->va_settings['location_plural']?>:</label> 
                            <?php if($offsite) : ?>
                                <p>*<?php echo $this->va_settings['location_plural']; ?> not available for offsite <?php echo $this->va_settings['venue_plural']; ?></p>
                            <?php else : ?>
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
                                    <select id="va-location-id" name="va_location_id[]" multiple required>
                                    <option></option>
                                    <?php while($locations->have_posts()) : $locations->the_post(); ?>
                                        <option value="<?php the_ID();?>" 
                                            <?php if($location_id == get_the_ID()){echo 'selected';} ?>
                                        ><?php the_title(); ?></option>
                                    <?php endwhile; ?>
                                    </select>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    <?php else : ?>
                        <input type="hidden" name="va_location_id[]" value="<?php echo $location_id; ?>" />
                    <?php endif; ?>
                    <span class="va-clearer"></span>
                    <div class="va-your-name">
                        <label>Your Name</label>
                        <input type="text" name="va_reservation_name" value="<?php if(isset($_COOKIE['va_reservation_name'])){echo stripslashes(sanitize_text_field($_COOKIE['va_reservation_name']));} ?>" required />
                    </div>
                    <?php if(in_array('phone', $this->va_settings['show_form_fields'])) : ?>
                        <div class="va-phone">
                            <label>Phone</label>
                            <input type="tel" name="va_reservation_phone" value="<?php if(isset($_COOKIE['va_reservation_phone'])){echo stripslashes(sanitize_text_field($_COOKIE['va_reservation_phone']));} ?>" required />
                        </div>
                    <?php endif; ?>
                    <span class="va-clearer"></span>
                    <div class="va-email">
                        <label>Email</label>
                        <input type="email" name="va_reservation_email" value="<?php if(isset($_COOKIE['va_reservation_email'])){echo stripslashes(sanitize_text_field($_COOKIE['va_reservation_email']));} ?>" required />
                        <span class="va-clearer"></span>
                    </div>
                    <?php if(in_array('type', $this->va_settings['show_form_fields'])) : ?>
                        <div class="va-send-to">
                            <label><?php echo $this->va_settings['reservation_single']; ?> Type:</label>
                            <select name="va_reservation_send_to" required>
                                <?php if($this->va_settings['admin_email_two'] == '') : ?>
                                    <option value="one"><?php echo $this->va_settings['admin_email_label_one']; ?></option>
                                <?php else : ?>
                                    <option value="">-- Choose One --</option>
                                    <option value="one"><?php echo $this->va_settings['admin_email_label_one']; ?></option>
                                    <option value="two"><?php echo $this->va_settings['admin_email_label_two']; ?></option>
                                <?php endif; ?>
                            </select>
                        </div>
                    <?php else : ?>
                        <input type="hidden" name="va_reservation_send_to" value="one" />
                    <?php endif; ?>
                    <span class="va-clearer"></span>
                    <?php if(in_array('description', $this->va_settings['show_form_fields'])) : ?>
                        <label><strong><?php echo $this->va_settings['reservation_single']; ?> Description:</strong></label>
                        <textarea name="va_reservation_content" style="width:100%;" rows="6"><?php if(isset($_COOKIE['va_reservation_content'])){echo stripslashes(sanitize_text_field($_COOKIE['va_reservation_content']));} ?></textarea>
                    <?php endif; ?>
				    <span class="va-clearer"></span>
                    <?php if(in_array('setup_needs', $this->va_settings['show_form_fields'])) : ?>
                        <label><strong>Set Up Needs:</strong></label>
                        <textarea name="va_reservation_setup" style="width:100%;" rows="6"><?php if(isset($_COOKIE['va_reservation_setup'])){echo stripslashes(sanitize_text_field($_COOKIE['va_reservation_setup']));} ?></textarea>
                    <?php endif; ?>
                    <span class="va-clearer"></span> 
                    <?php if(in_array('av_needs', $this->va_settings['show_form_fields'])) : ?>
                        <label><strong>A/V Tech Needs: (ie. Screen, Projector, Speakers, Microphone, etc.)</label>
                        <textarea name="va_reservation_av" style="width:100%;" rows="6"><?php if(isset($_COOKIE['va_reservation_av'])){echo stripslashes(sanitize_text_field($_COOKIE['va_reservation_av']));} ?></textarea>
                    <?php endif; ?>
				</div>
                <br/>
                <div>
                    <?php wp_nonce_field('va_save_submitted_reservation', 'va_save_submitted_reservation_nonce'); ?>
                    <input type="hidden" name="va_date" value="<?php echo $date; ?>" />
                    <input type="submit" id="va-reservation-submitted" name="va_reservation_submitted" value="Submit <?php echo $this->va_settings['reservation_single']; ?> Request" />
                </div>
            </form>

            <?php 
                // datepicker
                $datepicker = "
                    $('#va-lightbox-datepicker').datepicker({
                        dateFormat: 'mm/dd/yy',
                        altField: '#va-reservation-dates',
                        showOtherMonths: true,
                        minDate: 0,
                        addDates: ['" . date('m/d/Y', strtotime($date)) . "'],
                        defaultDate: '" . date('m/d/Y', strtotime($date)) . "',
                        onSelect: function(selectedDate){
                            $('#va-reservation-dateprols').trigger('change');
                            $('#va-reservation-dates-display').html($('#va-reservation-dates').val());
                        }
                    });
                ";
                $datepicker = apply_filters('va_datepicker', $datepicker); 
            ?>

            <script type="text/javascript">
                jQuery(document).ready(function($){

                    <?php echo $datepicker; ?>

                    // locations
                    $('#va-location-id').chosen({
                        placeholder_text_multiple: "Select some <?php echo $this->va_settings['location_plural']; ?>",
                        width:"100%"
                    });

                    $('select[name="va_need_setup_time"]').on('change',function(){
                        $('#va-setup-time').slideToggle('fast');
                    });
                    $('select[name="va_need_cleanup_time"]').on('change',function(){
                        $('#va-cleanup-time').slideToggle('fast');
                    });

                    $('#va-edit-dates').on('click',function(){
                        $('#va-lightbox-datepicker').slideToggle('fast');
                    });
                });
            </script>
            <?php
            die();
        }

        function va_draw_calendar($date = null, $venue_id){
            
            // check for date
            if(!$date){$date = date('m/d/Y', strtotime('now'));}

            // set month and year
            $month = date('n', strtotime($date));
            $year = date('Y', strtotime($date));
        ?>
            <form id="va-calendar-form" method="post" action="">
                <div id="va-calendar-venue">
                <?php $venues = $this->va_get_venues(); ?>
                <?php if($venues->have_posts()): ?>
                    <select id="va-venue-id" name="va_venue_id" onchange="this.form.submit();">
                    <?php while($venues->have_posts()): $venues->the_post(); ?>
                        <option value="<?php the_ID();?>" 
                            <?php 
                                if(!empty($venue_id)){
                                    if($venue_id == get_the_ID()){echo 'selected';}
                                }else if(get_option('va_default_venue') == get_the_ID()){echo 'selected';} 
                            ?>
                        ><?php the_title(); ?></option>
                    <?php endwhile; ?>
                    </select>
                    <?php endif;?>
                </div>
                <div class="va-calendar-nav">
                    Date <input id="va-datepicker" type="text" name="va_date_chosen" value="<?php echo $date; ?>" onchange="this.form.submit();"/>
                    <button class="button" name="va_prev" value="<?php echo date('m/d/Y', strtotime($date.'-1 months')); ?>"><i class="icon-double-angle-left"></i> prev</button>
                    <button class="button" name="va_current" value="<?php echo date('m/d/Y', strtotime('now')); ?>">current</button>
                    <button class="button" name="va_next" value="<?php echo date('m/d/Y', strtotime($date.'+1 months')); ?>">next <i class="icon-double-angle-right"></i></button>
                    <input type="hidden" name="va_view" value="month" />
                </div>
                <span class="va-clearer"></span>
            </form>
            
            <h1><?php echo date('F Y', mktime(0,0,0,$month,1,$year)); ?></h1>
            <?php // draw table ?>
            <table cellpadding="0" cellspacing="0" id="va-calendar-month" class="va-calendar">
            <thead>
            <?php //table headings ?>
            <?php $headings = array('Sun','Mon','Tue','Wed','Thu','Fri','Sat'); ?>
            <tr class="calendar-row">
                <th class="calendar-day-head">
                    <?php echo implode('</th><th class="calendar-day-head">',$headings); ?>
                </th>
            </tr>
            </thead>
            <tbody>
            <?php 
                // days and weeks vars now ...
                $running_day = date('w',mktime(0,0,0,$month,1,$year));
                $days_in_month = date('t',mktime(0,0,0,$month,1,$year));
                $days_in_this_week = 1;
                $day_counter = 0;
                $today = strtotime('today');
                $dates_array = array();
            ?>
            <?php // row for week one ?>
            <tr class="calendar-row">
            <?php // print "blank" days until the first of the current week ?>
            <?php for($x = 0; $x < $running_day; $x++): ?>
                <td class="calendar-day-np"></td>
                <?php $days_in_this_week++; ?>
            <?php endfor; ?>
                <?php // keep going with days.... ?>
                <?php for($list_day = 1; $list_day <= $days_in_month; $list_day++): ?>
                    <td class="calendar-day<?php if($today == mktime(0,0,0,$month,$list_day,$year)){echo ' today';}?>" data-date="<?php echo date('m/d/Y', strtotime("$year-$month-$list_day")); ?>">
                        <?php // add in the day number ?>
                        <i class="icon-edit"></i><div class="day-number"><?php echo $list_day; ?></div>
                        <?php // get reservations ?>
                        <?php $query_date = date('Y-m-d', strtotime("$year-$month-$list_day")); ?>
                        <?php 
                            $args = array(
                                'post_type' => 'va_reservation',
                                'posts_per_page' => -1,
                                'meta_query' => array(
                                    'relation' => 'AND',
                                    array(
                                        'key' => 'va_reservation_date',
                                        'value' => $query_date
                                    ),
                                    array(
                                        'key' => 'va_venue_id',
                                        'value' => $venue_id
                                    )
                                ),
                            ); 
                        ?>
                        <?php $reservations = new WP_Query($args); ?>
                        <?php if($reservations->have_posts()) : ?>
                            <?php while($reservations->have_posts()) : $reservations->the_post(); ?>
                                <?php $status = get_post_meta(get_the_ID(), 'va_reservation_status', true); ?>
                                <a class="<?php echo $status; ?>" href="/wp-admin/post.php?post=<?php the_ID(); ?>&action=edit" style="<?php echo apply_filters('va_reservation_background', '', $status); ?>"><?php the_title(); ?></a>
                            <?php endwhile; ?>
                        <?php endif; ?>
                    </td>
                    <?php if($running_day == 6) : ?>
                        </tr>
                        <?php if(($day_counter+1) != $days_in_month) : ?>
                            <tr class="calendar-row">
                        <?php endif; ?>
                        <?php $running_day = -1; ?>
                        <?php $days_in_this_week = 0; ?>
                    <?php endif; ?>
                    <?php $days_in_this_week++; $running_day++; $day_counter++; ?>
                <?php endfor; ?>
                <?php // finish the rest of the days in the week ?>
                <?php if($days_in_this_week < 8) : ?>
                    <?php for($x = 1; $x <= (8 - $days_in_this_week); $x++) : ?>
                        <td class="calendar-day-np"> </td>
                    <?php endfor; ?>
                <?php endif; ?>
                </tr>
                </tbody>
            </table>
            <script type="text/javascript">
                jQuery(document).ready(function($){
                    $('#va-datepicker').datepicker({
                        dateFormat: "mm/dd/yy",
                        inline: true,
                        showOtherMonths: true,
                        showOn: "both", 
                        buttonText: "<i class='icon-calendar'></i>",
                        onSelect: function(selectedDate){
                            $('input[name="va_view"]').val('day');
                            $('input[name="va_date_chosen"]').trigger('change');
                        }
                    });
                    $('.calendar-day').on('click', function(){
                        $('input[name="va_view"]').val('day');
                        $('input[name="va_date_chosen"]').val($(this).attr('data-date'));
                        $('#va-calendar-form').submit();
                    });
                });
            </script>
            
            <?php 
            return ob_get_clean();
        }
		
		// admin calendar view
        function va_draw_day($date, $venue_id){
            $date = date('Y-m-d', strtotime($date));
            ob_start();
            ?>
            <form id="va-day-form" method="post" action="">
                <div id="va-calendar-venue">
                <?php $venue_start = get_post_meta($venue_id, 'va_venue_'.strtolower(date('l',strtotime($date))).'_start', true); ?>
                <?php $venue_end = get_post_meta($venue_id, 'va_venue_'.strtolower(date('l',strtotime($date))).'_end', true); ?>
                <?php $venues = $this->va_get_venues(); ?>
                <?php if($venues->have_posts()): ?>
                    <select id="va-venue-id" name="va_venue_id" onchange="this.form.submit();">
                    <?php while($venues->have_posts()): $venues->the_post(); ?>
                        <option value="<?php the_ID();?>" 
                            <?php 
                                if(!empty($venue_id)){
                                    if($venue_id == get_the_ID()){echo 'selected';}
                                }else if(get_option('va_default_venue') == get_the_ID()){echo 'selected';} 
                            ?>
                        ><?php the_title(); ?></option>
                    <?php endwhile; ?>
                    </select>
                    <?php endif;?>
                </div>
                <div class="va-calendar-nav">
                    Date <input id="va-datepicker" type="text" name="va_date_chosen" value="<?php echo date('m/d/Y', strtotime($date)); ?>" onchange="this.form.submit();"/>
                    <button class="button" name="va_prev" value="<?php echo date('m/d/Y', strtotime($date.'-1 days')); ?>"><i class="icon-double-angle-left"></i> prev</button>
                    <button class="button" name="va_current" value="<?php echo date('m/d/Y', strtotime('now')); ?>">current</button>
                    <button class="button" name="va_next" value="<?php echo date('m/d/Y', strtotime($date.'+1 days')); ?>">next <i class="icon-double-angle-right"></i></button>
                    <button class="button" name="va_back" value="back"><i class="icon-calendar"></i> calendar</button>
                    <input type="hidden" name="va_view" value="day" />
                </div>
                <span class="va-clearer"></span>
            </form>   
           
            <h2><?php echo date('l - F jS, Y', strtotime($date)); ?></h2>
            <?php $locations = new WP_Query('post_type=va_location&posts_per_page=-1&meta_key=va_venue_id&meta_value='.$venue_id); ?>
            <table cellpadding="0" cellspacing="0" id="va-day-view">
                <thead>
                <tr class="va-header-row" <?php echo apply_filters('va_table_header_background', ''); ?>>
                    <th style="width:100px;"></th>
                <?php foreach($locations->posts as $location) : ?>
                    <th>
                        <a class="va-edit" href="/wp-admin/post.php?post=<?php echo $location->ID; ?>&action=edit"><?php echo $location->post_title; ?></a>
                    </th>
                <?php endforeach; ?>
                </tr>
                </thead>
                <tbody>
                <?php 
                    $args = array(
                        'post_type' => 'va_reservation',
                        'posts_per_page' => -1,
                        'meta_query' => array(
                            'relation' => 'AND',
                            array(
                                'key' => 'va_venue_id',
                                'value' => $venue_id
                            ),
                            array(
                                'key' => 'va_reservation_date',
                                'value' => $date
                            ),
                        ),
                    ); 
                ?>
                <?php $reservations = new WP_Query($args); ?>
                <?php $times = $this->va_get_times($this->va_settings['day_start_time'],$this->va_settings['day_end_time'],0.25); ?>
                <?php foreach($times as $time) : ?>
                    <tr>
                        <td class="time"><?php echo date('g:i a',strtotime($time)); ?></td>
                    <?php foreach($locations->posts as $location) : ?>
                        <td id="<?php echo $location->ID . '-' . str_replace(':','',$time); ?>"
							<?php 
								$status = 'not-available';
								
								// check for custom availability 
								if(get_post_meta($location->ID, 'va_venue_availability', true) == 'custom'){ 
									$location_start = get_post_meta($location->ID, 'va_location_'.strtolower(date('l',strtotime($date))).'_start', true);
									$location_end = get_post_meta($location->ID, 'va_location_'.strtolower(date('l',strtotime($date))).'_end', true);
									
									if(($location_start <= date('H:i', strtotime($time))) && (date('H:i', strtotime($time)) <= $location_end)){
										$status = 'available';
									}
								}
								else{
									//check location availability
									if(($venue_start <= date('H:i', strtotime($time))) && (date('H:i', strtotime($time)) <= $venue_end)){
										$status = 'available';
									}
								} 
								
							?>
							class="<?php echo $status; ?>" <?php echo apply_filters('va_unavailable_background', '', $status); ?>>
                        
                            <?php foreach($reservations->posts as $reservation) : ?>
                            <?php //echo get_post_meta($reservation->ID, 'va_location_id', true); echo $location->ID;?>
                                <?php $found = false; ?>
                                <?php if(is_array(get_post_meta($reservation->ID, 'va_location_id', true))) : ?>
                                    <?php if(in_array($location->ID, get_post_meta($reservation->ID, 'va_location_id', true)) && get_post_meta($reservation->ID, 'va_start_setup_time',true) == $time) : ?>
                                        <?php $found = true; ?>
                                    <?php endif; ?>
                                <?php elseif(get_post_meta($reservation->ID, 'va_location_id', true) == $location->ID && get_post_meta($reservation->ID, 'va_start_setup_time',true) == $time) : ?>
                                        <?php $found = true; ?>
                                    <?php endif; ?>
                                <?php if($found) : ?>
                                    <?php $start = get_post_meta($reservation->ID, 'va_start_setup_time', true); ?>
                                    <?php $end = get_post_meta($reservation->ID, 'va_end_cleanup_time', true); ?>
                                    <?php $diff = (strtotime($end) - strtotime($start))/60; ?>
                                    <?php $height = ($diff/15) * 30; ?>
                                    <?php $status = get_post_meta($reservation->ID, 'va_reservation_status', true); ?>
                                    <a href="/wp-admin/post.php?post=<?php echo $reservation->ID; ?>&action=edit" class="reservation <?php echo $status; ?>" style="height:<?php echo $height; ?>px; <?php echo apply_filters('va_reservation_background', '', $status); ?>">
                                        <i class="icon-edit"></i> <?php echo $reservation->post_title . ' (' . date('g:i a', strtotime($start)) . ' - ' . date('g:i a', strtotime($end)) . ')'; ?>
                                        <p><?php echo $reservation->post_content; ?></p>
                                    </a>
                                <?php endif; ?>
                            <?php endforeach; ?>
                            <a class="va-edit" href="/wp-admin/post-new.php?post_type=va_reservation&va_venue_id=<?php echo $venue_id; ?>&va_location_id=<?php echo $location->ID; ?>&va_start_time=<?php echo $time; ?>&va_reservation_date=<?php echo date('Y-m-d', strtotime($date)); ?>"><i class="icon-edit"></i></a>
                        </td>
                    <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <script type="text/javascript">
                jQuery(document).ready(function($){
                    $('#va-datepicker').datepicker({
                        dateFormat: "mm/dd/yy",
                        inline: true,
                        showOtherMonths: true,
                        showOn: "both", 
                        buttonText: "<i class='icon-calendar'></i>",
                        onSelect: function(selectedDate){
                            $('input[name="va_view"]').val('day');
                            $('input[name="va_date_chosen"]').trigger('change');
                        }
                    });
                    $('button[name="va_today"]').on('click', function(){
                        $('input[name="va_view"]').val('day');
                        $('input[name="va_date_chosen"]').val($(this).attr('data-date'));
                        $('#va-calendar-form').submit();
                    });
                });
            </script>
            <?php
            return ob_get_clean();
        }
        function va_send_notification($to, $subject, $content){
            add_filter('wp_mail_content_type', create_function('', 'return "text/html"; '));
            $to = str_replace(' ', '', $to);
            $headers = 'From: "'.$this->va_settings['from_email_name'].'" <'.$this->va_settings['from_email_address'].'>' . "\r\n";
            $status = wp_mail($to, $subject, $content, $headers);
        }
 
        function va_get_times($lower = 0, $upper = 23.75, $step = 1, $format = NULL){
            
			// set default format
			if($format === NULL){
                $format = 'H:i';
            }
			
			// convert time to decimal
			if(strpos($lower,':') !== false) {
				$lower = $this->va_time_to_decimal($lower);
			}
			if(strpos($upper,':') !== false) {
				$upper = $this->va_time_to_decimal($upper);
			}

			// build array
            $times = array();
            foreach(range($lower, $upper, $step) as $increment){
                $increment = number_format($increment, 2);
                list($hour, $minutes) = explode('.', $increment);
                $date = new DateTime($hour . ':' . $minutes * .6);
                $times[(string) $increment] = $date->format($format);
            }
            return $times;
        }
		
		function va_time_to_decimal($time){
			$time_arr = explode(':', $time);
			$hour = intval($time_arr[0]);
			$minute = intval($time_arr[1])/60;
			$decimal = number_format($hour + $minute, 2);
			return $decimal;
		}
		
        function va_get_time_select($name, $value = null, $id = null, $required = false, $start = false, $end = false){
            ob_start();
            ?>
            <select id="<?php echo $id; ?>" name="<?php echo $name; ?>" <?php if($required){echo 'required';} ?>>
                <?php if($start) : ?>
                    <?php $time_arr = explode(':',$start); ?>
                    <?php $start_at = $time_arr[0] + ($time_arr[1]/60); ?>
                    <?php $times = $this->va_get_times($start_at,23.75,0.25); ?>
                <?php elseif($end) : ?>
                    <?php $time_arr = explode(':',$end); ?>
                    <?php $end_at = $time_arr[0] + ($time_arr[1]/60); ?>
                    <?php $times = $this->va_get_times(0,$end_at,0.25); ?>
                    <?php arsort($times); ?>
                <?php else : ?>
                    <?php $times = $this->va_get_times(0,23.75,0.25); ?>
                <?php endif; ?>
                <option></option>
                <?php foreach ($times as $time) : ?>
                    <option value="<?php echo $time; ?>" <?php if($time == $value){echo 'selected';} ?>>
                        <?php echo date('g:i a', strtotime($time)); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <?php
            return ob_get_clean();
        }

        function va_get_path($file){
            return trailingslashit(dirname($file));
        }
        
        function va_get_dir($file){
            $dir = trailingslashit(dirname($file));
            $count = 0;
            
            // sanitize for Win32 installs
            $dir = str_replace('\\' ,'/', $dir); 
            
            // if file is in plugins folder
            $wp_plugin_dir = str_replace('\\' ,'/', WP_PLUGIN_DIR); 
            $dir = str_replace($wp_plugin_dir, plugins_url(), $dir, $count);
            
            if($count < 1){
                // if file is in wp-content folder
                $wp_content_dir = str_replace('\\' ,'/', WP_CONTENT_DIR); 
                $dir = str_replace($wp_content_dir, content_url(), $dir, $count);
            }
           
            if($count < 1){
                // if file is in ??? folder
                $wp_dir = str_replace('\\' ,'/', ABSPATH); 
                $dir = str_replace($wp_dir, site_url('/'), $dir);
            }
            
            return $dir;
        }
    }
    $vacancy = new Vacancy();
?>
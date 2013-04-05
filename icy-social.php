<?php
/*
Plugin Name: Icy Social
Plugin Script: icy-social.php
Plugin URI: http://www.icypixels.com/icy-social/
Description: A simple and handy social icons set which can be used by shortcode. Brought to you by <a href="http://www.icypixels.com" title="Icy Pixels WordPress Themes">Icy Pixels</a> (<a href="http://twitter.com/theicypixels/">Twitter</a> | <a href="https://www.facebook.com/pages/Icy-Pixels/170508899756996">Facebook</a>). 
Version: 1.0
License: GPL 3.0
Author: Icy Pixels
Author URI: http://www.icypixels.com
*/

class IcySocial {

	var $settings;
	var $services;
	
    function __construct() 
    {	
    	$this->services = array(
			'amazon' => '',
			'android' => '',
			'aol' => '',
			'appstore' => '',
			'bitcoin' => '',
			'blogger' => '',
			'call' => '',
			'cal' => '',
			'chrome' => '',
			'cloudapp' => '',
			'creativecommons' => '',
			'disqus' => '',
			'dribbble' => '',
			'dropbox' => '',
			'email' => '',
			'eventasaurus' => '',
			'eventbrite' => '',
			'eventful' => '',
			'evernote' => '',
			'facebook' => '',
			'fivehundredpx' => '',
			'flattr' => '',
			'forrst' => '',
			'foursquare' => '',
			'github' => '',
			'gmail' => '',
			'google' => '',
			'googleplus' => '',
			'gowalla' => '',
			'grooveshark' => '',
			'guest' => '',
			'html5' => '',
			'ie' => '',
			'instapaper' => '',
			'intensedebate' => '',
			'klout' => '',
			'itunes' => '',
			'lanyrd' => '',
			'lastfm' => '',
			'linkedin' => '',
			'macstore' => '',
			'meetup' => '',
			'myspace' => '',
			'ninetyninedesigns' => '',
			'openid' => '',
			'paypal' => '',
			'pinboard' => '',
			'pinterest' => '',
			'plancast' => '',
			'plurk' => '',
			'podcast' => '',
			'posterous' => '',
			'quora' => '',
			'rss' => '',
			'scribd' => '',
			'skype' => '',
			'smashing' => '',
			'songkick' => '',
			'soundcloud' => '',
			'spotify' => '',
			'stumbleupon' => '',
			'tumblr' => '',
			'twitter' => '',
			'viadeo' => '',
			'vimeo' => '',
			'weibo' => '',
			'wikipedia' => '',
			'windows' => '',
			'wordpress' => '',
			'xing' => '',
			'yahoo' => '',
			'yelp' => '',
			'youtube' => '',
    	);
    	
    	// Registering Admin Stuff
    	add_action('admin_init', array(&$this, 'admin_init'));
        add_action('admin_menu', array(&$this, 'admin_menu'), 99);

        // Addings shortcode and widget
        add_shortcode('icy_social', array(&$this, 'shortcode'));
        add_action('widgets_init', create_function('', 'register_widget("IcySocial_Widget");'));
        add_action('wp_enqueue_scripts', array(&$this, 'icy_register_styles'));
        add_action('admin_enqueue_scripts', array(&$this, 'icy_register_styles'));
	}

	function icy_register_styles() {
		// Registering plugin styling        
        wp_register_style( 'icy-social', plugins_url( '/icy-social.css', __FILE__ ));         
    	wp_enqueue_style( 'icy-social' );  
	}
	
	function section_intro() {

	}

	function admin_init()
	{
		// Registering Settings
		register_setting( 'icy-social', 'icy_social_settings', array(&$this, 'settings_validate') );
		add_settings_section( 'icy-social', '', array(&$this, 'section_intro'), 'icy-social' );
		add_settings_field( 'preview', __( 'Preview', 'framework' ), array(&$this, 'setting_preview'), 'icy-social', 'icy-social' );
		
		$this->settings = get_option( 'icy_social_settings' );
		// Output social profiles
		foreach($this->services as $service=>$help){
			$this->add_profile( $service, $service .' URL', $help );
		}
				
		add_settings_field( 'links', __( 'Open Links', 'framework' ), array(&$this, 'setting_links'), 'icy-social', 'icy-social' );
		add_settings_field( 'instructions', __( 'Shortcode and Template Tag', 'framework' ), array(&$this, 'setting_instructions'), 'icy-social', 'icy-social' );
	}
	
	function admin_menu() 
	{
		$icon_url = plugins_url( '/images/favicon.png', __FILE__ );
		$page_hook = add_menu_page( __( 'Icy Social Settings', 'framework' ), __( 'Icy Social', 'framework' ), 'update_core', 'icy-social', array(&$this, 'settings_page'), $icon_url );
		add_submenu_page( 'icy-social', __( 'Settings', 'framework' ), __( 'Icy Social Settings', 'framework' ), 'update_core', 'icy-social', array(&$this, 'settings_page') );		
	}
	
	function settings_page()
	{
		?>
		<div class="wrap">
			<div id="icon-themes" class="icon32"></div>
			<h2>Icy Social Settings</h2>
			<p><?php _e('Icy Social allows you to display beautiful retina-ready social icons on your website. Select the services which you want to use and your basic configuration.', 'framework'); ?></p>
			<p><?php _e('Check out our <a href="http://www.icypixels.com/themes/">WordPress themes</a>. Brought to you by <a href="http://www.icypixels.com" title="Icy Pixels WordPress Themes">Icy Pixels</a> (<a href="http://twitter.com/theicypixels/">Twitter</a> | <a href="https://www.facebook.com/pages/Icy-Pixels/170508899756996">Facebook</a>). ', 'framework'); ?></p>
			<?php if( isset($_GET['settings-updated']) && $_GET['settings-updated'] ){ ?>
			<div id="setting-error-settings_updated" class="updated settings-error"> 
				<p><strong><?php _e( 'Settings saved.', 'framework' ); ?></strong></p>
			</div>
			<?php } ?>
			<form action="options.php" method="post">
				<?php settings_fields( 'icy-social' ); ?>
				<?php do_settings_sections( 'icy-social' ); ?>
				<p class="submit"><input type="submit" class="button-primary" value="<?php _e( 'Save Changes', 'framework' ); ?>" /></p>
			</form>
		</div>
		<?php
	}
	
	function add_profile( $id, $title, $help = '' )
	{
		$args = array(
			'id' => $id,
			'help' => $help
		);
		
		add_settings_field( $id, __( $title, 'framework' ), array(&$this, 'setting_profile'), 'icy-social', 'icy-social', $args );
	}
	
	function setting_profile( $args )
	{
		if( !isset($this->settings[$args['id']]) ) $this->settings[$args['id']] = '';
		
		echo '<input type="text" name="icy_social_settings['. $args['id'] .']" class="regular-text" value="'. $this->settings[$args['id']] .'" /> ';		
	}	

	function setting_links()
	{
		if( !isset($this->settings['links']) ) $this->settings['links'] = 'same_window';
		
		echo '<select name="icy_social_settings[links]">
		<option value="same_window"'. (($this->settings['links'] == 'same_window') ? ' selected="selected"' : '') .'>In same window</option>
		<option value="new_window"'. (($this->settings['links'] == 'new_window') ? ' selected="selected"' : '') .'>In new window</option>
		</select>';
	}
	
	function setting_preview()
	{
		if($this->settings) echo $this->do_social();
	}
	
	function setting_instructions()
	{
		echo '<p>In order to use your social icons on posts and pages use the following shortcode:</p>
		<p><code>[icy_social]</code></p>		
		<p>You can optionally pass in a "sites" parameter to the above to override the default values.</p>
		<p>e.g. <code>[icy_social sites="twitter,facebook"]</code></p>';
	}
	
	function settings_validate($input)
	{
		foreach($this->services as $service=>$help){
			$input[$service] = strip_tags($input[$service]);
			if($service != 'Skype') $input[$service] = esc_url_raw($input[$service]);
		}
		return $input;
	}
	
	function shortcode( $atts )
	{
		global $icy_social;
		extract( shortcode_atts( array(			
			'services' => ''
		), $atts ) );

		$services_wl = array();
		if($services) $services_wl = explode(',', str_replace(' ', '', esc_attr($services)));
		return $this->do_social($services_wl);
	}
	
	function do_social($services_wl = array() )
	{
		$options = get_option( 'icy_social_settings' );
		
		if( !isset($options['links']) ) $options['links'] = 'same_window';
		
		$output = '<div class="icy-social-wrapper">';
				
		if(empty($services_wl)){
			foreach($this->services as $service=>$help){
				if(isset($options[$service]) && $options[$service]){
					$output .= '<a href="'. $options[$service] .'" class="icy-social icon '. $service .'"'. (($options['links'] == 'new_window') ? ' target="_blank"' : '') .'>'. $service .'</a> ';
				}
			}
		} else {
			foreach($services_wl as $service){
				if(isset($options[$service]) && $options[$service]){
					$output .= '<a href="'. $options[$service] .'" class="icy-social icon '. $service .'"'. (($options['links'] == 'new_window') ? ' target="_blank"' : '') .'>'. $service .'</a> ';
				}
			}
		}	
		
		$output .= '</div>';
		return $output;
	}
	
}

global $icy_social;
$icy_social = new IcySocial();

/**
 *
 * WordPress Widget
 *
 */

class IcySocial_Widget extends WP_Widget {

	function __construct() {
		parent::WP_Widget( 'icy_social_widget', 'Icy Social', array( 'description' => 'Displays your Icy Social icons' ) );
	}

	function widget( $args, $instance ) {
		extract( $args );
		$title = apply_filters( 'widget_title', $instance['title'] );
		$desc = $instance['description'];		
		
		echo $before_widget;
		if ( !empty( $title ) ) echo $before_title . $title . $after_title;
		
		if( $desc ) echo '<p>'. $desc .'</p>';
		
		global $icy_social;
		echo $icy_social->do_social();
		
		echo $after_widget;
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['description'] = strip_tags($new_instance['description'], '<a><b><strong><i><em>');		
		return $instance;
	}

	function form( $instance ) {
		if ( $instance && isset($instance['title']) ) $title = esc_attr( $instance['title'] );
		else $title = '';
		if ( $instance && isset($instance['description']) ) $desc = esc_attr( $instance['description'] );
		else $desc = '';		
		?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('description'); ?>"><?php _e('Description:'); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id('description'); ?>" name="<?php echo $this->get_field_name('description'); ?>" type="text" value="<?php echo $desc; ?>" />
		</p>		
		<?php 
	}
}


?>
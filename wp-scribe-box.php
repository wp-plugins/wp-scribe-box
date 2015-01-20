<?php
/*
Plugin Name: WP Scribe Box
Plugin URI: http://www.jimmyscode.com/wordpress/wp-scribe-box/
Description: Display the Scribe affiliate box on your WordPress website. Make money as a Scribe affiliate.
Version: 0.2.3
Author: Jimmy Pe&ntilde;a
Author URI: http://www.jimmyscode.com/
License: GPLv2 or later
*/

if (!defined('WPSB_PLUGIN_NAME')) {
	// plugin constants
	define('WPSB_PLUGIN_NAME', 'WP Scribe Box');
	define('WPSB_VERSION', '0.2.3');
	define('WPSB_SLUG', 'wp-scribe-box');
	define('WPSB_LOCAL', 'wp_scribe_box');
	define('WPSB_OPTION', 'wp_scribe_box');
	define('WPSB_OPTIONS_NAME', 'wp_scribe_box_options');
	define('WPSB_PERMISSIONS_LEVEL', 'manage_options');
	define('WPSB_PATH', plugin_basename(dirname(__FILE__)));
	/* defaults */ 
	define('WPSB_DEFAULT_ENABLED', true);
	define('WPSB_DEFAULT_URL', '');
	define('WPSB_ROUNDED', false);
	define('WPSB_NOFOLLOW', false);
	define('WPSB_AVAILABLE_IMAGES', 'scribe-125x125,scribe-235x247,scribe-250x250,scribe-250x250-2,scribe-260x125,scribe-300x250');
	define('WPSB_DEFAULT_IMAGE', '');
	define('WPSB_DEFAULT_AUTO_INSERT', false);
	define('WPSB_DEFAULT_SHOW', false);
	define('WPSB_DEFAULT_NEWWINDOW', false);
	define('WPSB_DEFAULT_NONLOGGEDUSERS', false);
	define('WPSB_DEFAULT_USEEXTENDED_TEXT', false);
	/* default option names */
	define('WPSB_DEFAULT_ENABLED_NAME', 'enabled');
	define('WPSB_DEFAULT_URL_NAME', 'affurl');
	define('WPSB_DEFAULT_ROUNDED_NAME', 'rounded');
	define('WPSB_DEFAULT_NOFOLLOW_NAME', 'nofollow');
	define('WPSB_DEFAULT_IMAGE_NAME', 'img');
	define('WPSB_DEFAULT_AUTO_INSERT_NAME', 'autoinsert');
	define('WPSB_DEFAULT_SHOW_NAME', 'show');
	define('WPSB_DEFAULT_NEWWINDOW_NAME', 'opennewwindow');
	define('WPSB_DEFAULT_NONLOGGEDUSERS_NAME', 'nonloggedonly');
	define('WPSB_DEFAULT_USEEXTENDED_TEXT_NAME', 'useextended');
}
	// oh no you don't
	if (!defined('ABSPATH')) {
		wp_die(__('Do not access this file directly.', wpsb_get_local()));
	}

	// localization to allow for translations
	add_action('init', 'wp_scribe_box_translation_file');
	function wp_scribe_box_translation_file() {
		$plugin_path = wpsb_get_path() . '/translations';
		load_plugin_textdomain(wpsb_get_local(), '', $plugin_path);
		register_wp_scribe_box_style();
	}
	// tell WP that we are going to use new options
	add_action('admin_init', 'wp_scribe_box_options_init');
	function wp_scribe_box_options_init() {
		register_setting(WPSB_OPTIONS_NAME, wpsb_get_option(), 'wpsb_validation');
		register_wpsb_admin_style();
		register_wpsb_admin_script();
	}
	// validation function
	function wpsb_validation($input) {
		if (!empty($input)) {
			// validate all form fields
			$input[WPSB_DEFAULT_URL_NAME] = esc_url($input[WPSB_DEFAULT_URL_NAME]);
			$input[WPSB_DEFAULT_ENABLED_NAME] = (bool)$input[WPSB_DEFAULT_ENABLED_NAME];
			$input[WPSB_DEFAULT_ROUNDED_NAME] = (bool)$input[WPSB_DEFAULT_ROUNDED_NAME];
			$input[WPSB_DEFAULT_NOFOLLOW_NAME] = (bool)$input[WPSB_DEFAULT_NOFOLLOW_NAME];
			$input[WPSB_DEFAULT_AUTO_INSERT_NAME] = (bool)$input[WPSB_DEFAULT_AUTO_INSERT_NAME];
			$input[WPSB_DEFAULT_NEWWINDOW_NAME] = (bool)$input[WPSB_DEFAULT_NEWWINDOW_NAME];
			$input[WPSB_DEFAULT_NONLOGGEDUSERS_NAME] = (bool)$input[WPSB_DEFAULT_NONLOGGEDUSERS_NAME];
			$input[WPSB_DEFAULT_USEEXTENDED_TEXT_NAME] = (bool)$input[WPSB_DEFAULT_USEEXTENDED_TEXT_NAME];
			$input[WPSB_DEFAULT_IMAGE_NAME] = sanitize_text_field($input[WPSB_DEFAULT_IMAGE_NAME]);
		}
		return $input;
	}
	// add Settings sub-menu
	add_action('admin_menu', 'wpsb_plugin_menu');
	function wpsb_plugin_menu() {
		add_options_page(WPSB_PLUGIN_NAME, WPSB_PLUGIN_NAME, WPSB_PERMISSIONS_LEVEL, wpsb_get_slug(), 'wp_scribe_box_page');
	}
	// plugin settings page
	// http://planetozh.com/blog/2009/05/handling-plugins-options-in-wordpress-28-with-register_setting/
	function wp_scribe_box_page() {
		// check perms
		if (!current_user_can(WPSB_PERMISSIONS_LEVEL)) {
			wp_die(__('You do not have sufficient permission to access this page', wpsb_get_local()));
		}
	?>
		<div class="wrap">
			<h2 id="plugintitle"><img src="<?php echo wpsb_getimagefilename('scribe.png'); ?>" title="" alt="" height="64" width="64" align="absmiddle" /> <?php echo WPSB_PLUGIN_NAME; ?> by <a href="http://www.jimmyscode.com/">Jimmy Pe&ntilde;a</a></h2>
			<div><?php _e('You are running plugin version', wpsb_get_local()); ?> <strong><?php echo WPSB_VERSION; ?></strong>.</div>
			
			<?php /* http://code.tutsplus.com/tutorials/the-complete-guide-to-the-wordpress-settings-api-part-5-tabbed-navigation-for-your-settings-page--wp-24971 */ ?>
			<?php $active_tab = (isset($_GET['tab']) ? $_GET['tab'] : 'settings'); ?>
			<h2 class="nav-tab-wrapper">
			  <a href="?page=<?php echo wpsb_get_slug(); ?>&tab=settings" class="nav-tab <?php echo $active_tab == 'settings' ? 'nav-tab-active' : ''; ?>"><?php _e('Settings', wpsb_get_local()); ?></a>
				<a href="?page=<?php echo wpsb_get_slug(); ?>&tab=parameters" class="nav-tab <?php echo $active_tab == 'parameters' ? 'nav-tab-active' : ''; ?>"><?php _e('Parameters', wpsb_get_local()); ?></a>
				<a href="?page=<?php echo wpsb_get_slug(); ?>&tab=support" class="nav-tab <?php echo $active_tab == 'support' ? 'nav-tab-active' : ''; ?>"><?php _e('Support', wpsb_get_local()); ?></a>
			</h2>
			
			<form method="post" action="options.php">
			<?php settings_fields(WPSB_OPTIONS_NAME); ?>
			<?php $options = wpsb_getpluginoptions(); ?>
			<?php update_option(wpsb_get_option(), $options); ?>
			<?php if ($active_tab == 'settings') { ?>
			<h3 id="settings"><img src="<?php echo wpsb_getimagefilename('settings.png'); ?>" title="" alt="" height="61" width="64" align="absmiddle" /> <?php _e('Plugin Settings', wpsb_get_local()); ?></h3>
				<table class="form-table" id="theme-options-wrap">
					<tr valign="top"><th scope="row"><strong><label title="<?php _e('Is plugin enabled? Uncheck this to turn it off temporarily.', wpsb_get_local()); ?>" for="<?php echo wpsb_get_option(); ?>[<?php echo WPSB_DEFAULT_ENABLED_NAME; ?>]"><?php _e('Plugin enabled?', wpsb_get_local()); ?></label></strong></th>
						<td><input type="checkbox" id="<?php echo wpsb_get_option(); ?>[<?php echo WPSB_DEFAULT_ENABLED_NAME; ?>]" name="<?php echo wpsb_get_option(); ?>[<?php echo WPSB_DEFAULT_ENABLED_NAME; ?>]" value="1" <?php checked('1', wpsb_checkifset(WPSB_DEFAULT_ENABLED_NAME, WPSB_DEFAULT_ENABLED, $options)); ?> /></td>
					</tr>
					<?php wpsb_explanationrow(__('Is plugin enabled? Uncheck this to turn it off temporarily.', wpsb_get_local())); ?>
					<?php wpsb_getlinebreak(); ?>
					<tr valign="top"><th scope="row"><strong><label title="<?php _e('Enter your affiliate URL here. This will be inserted wherever you use the shortcode.', wpsb_get_local()); ?>" for="<?php echo wpsb_get_option(); ?>[<?php echo WPSB_DEFAULT_URL_NAME; ?>]"><?php _e('Your Affiliate URL', wpsb_get_local()); ?></label></strong></th>
						<td><input type="url" id="<?php echo wpsb_get_option(); ?>[<?php echo WPSB_DEFAULT_URL_NAME; ?>]" name="<?php echo wpsb_get_option(); ?>[<?php echo WPSB_DEFAULT_URL_NAME; ?>]" value="<?php echo wpsb_checkifset(WPSB_DEFAULT_URL_NAME, WPSB_DEFAULT_URL, $options); ?>" /></td>
					</tr>
					<?php wpsb_explanationrow(__('Enter your affiliate URL here. This will be inserted wherever you use the shortcode.', wpsb_get_local())); ?>
					<?php wpsb_getlinebreak(); ?>
					<tr valign="top"><th scope="row"><strong><label title="<?php _e('Do you want to apply rounded corners CSS to the output?', wpsb_get_local()); ?>" for="<?php echo wpsb_get_option(); ?>[<?php echo WPSB_DEFAULT_ROUNDED_NAME; ?>]"><?php _e('Rounded corners CSS?', wpsb_get_local()); ?></label></strong></th>
						<td><input type="checkbox" id="<?php echo wpsb_get_option(); ?>[<?php echo WPSB_DEFAULT_ROUNDED_NAME; ?>]" name="<?php echo wpsb_get_option(); ?>[<?php echo WPSB_DEFAULT_ROUNDED_NAME; ?>]" value="1" <?php checked('1', wpsb_checkifset(WPSB_DEFAULT_ROUNDED_NAME, WPSB_ROUNDED, $options)); ?> /></td>
					</tr>
					<?php wpsb_explanationrow(__('Do you want to apply rounded corners CSS to the output?', wpsb_get_local())); ?>
					<?php wpsb_getlinebreak(); ?>
					<tr valign="top"><th scope="row"><strong><label title="<?php _e('Check this box to automatically insert the output at the end of blog posts. If you do not do this then you will need to manually insert shortcode or call the function in PHP.', wpsb_get_local()); ?>" for="<?php echo wpsb_get_option(); ?>[<?php echo WPSB_DEFAULT_AUTO_INSERT_NAME; ?>]"><?php _e('Auto insert Scribe box at the end of posts?', wpsb_get_local()); ?></label></strong></th>
						<td><input type="checkbox" id="<?php echo wpsb_get_option(); ?>[<?php echo WPSB_DEFAULT_AUTO_INSERT_NAME; ?>]" name="<?php echo wpsb_get_option(); ?>[<?php echo WPSB_DEFAULT_AUTO_INSERT_NAME; ?>]" value="1" <?php checked('1', wpsb_checkifset(WPSB_DEFAULT_AUTO_INSERT_NAME, WPSB_DEFAULT_AUTO_INSERT, $options)); ?> /></td>
					</tr>
					<?php wpsb_explanationrow(__('Check this box to automatically insert the output at the end of blog posts. If you don\'t do this then you will need to manually insert shortcode or call the function in PHP.', wpsb_get_local())); ?>
					<?php wpsb_getlinebreak(); ?>
					<tr valign="top"><th scope="row"><strong><label title="<?php _e('Do you want to add rel=nofollow to all links?', wpsb_get_local()); ?>" for="<?php echo wpsb_get_option(); ?>[<?php echo WPSB_DEFAULT_NOFOLLOW_NAME; ?>]"><?php _e('Nofollow links?', wpsb_get_local()); ?></label></strong></th>
						<td><input type="checkbox" id="<?php echo wpsb_get_option(); ?>[<?php echo WPSB_DEFAULT_NOFOLLOW_NAME; ?>]" name="<?php echo wpsb_get_option(); ?>[<?php echo WPSB_DEFAULT_NOFOLLOW_NAME; ?>]" value="1" <?php checked('1', wpsb_checkifset(WPSB_DEFAULT_NOFOLLOW_NAME, WPSB_NOFOLLOW, $options)); ?> /></td>
					</tr>
					<?php wpsb_explanationrow(__('Do you want to add rel="nofollow" to all links?', wpsb_get_local())); ?>
					<?php wpsb_getlinebreak(); ?>
					<tr valign="top"><th scope="row"><strong><label title="<?php _e('Check this box to open links in a new window.', wpsb_get_local()); ?>" for="<?php echo wpsb_get_option(); ?>[<?php echo WPSB_DEFAULT_NEWWINDOW_NAME; ?>]"><?php _e('Open links in new window?', wpsb_get_local()); ?></label></strong></th>
						<td><input type="checkbox" id="<?php echo wpsb_get_option(); ?>[<?php echo WPSB_DEFAULT_NEWWINDOW_NAME; ?>]" name="<?php echo wpsb_get_option(); ?>[<?php echo WPSB_DEFAULT_NEWWINDOW_NAME; ?>]" value="1" <?php checked('1', wpsb_checkifset(WPSB_DEFAULT_NEWWINDOW_NAME, WPSB_DEFAULT_NEWWINDOW, $options)); ?> /></td>
					</tr>
					<?php wpsb_explanationrow(__('Check this box to open links in a new window. Requires Javascript.', wpsb_get_local())); ?>
					<?php wpsb_getlinebreak(); ?>
					<tr valign="top"><th scope="row"><strong><label title="<?php _e('Show to non-logged-in users only?', wpsb_get_local()); ?>" for="<?php echo wpsb_get_option(); ?>[<?php echo WPSB_DEFAULT_NONLOGGEDUSERS_NAME; ?>]"><?php _e('Show to non-logged-in users only?', wpsb_get_local()); ?></label></strong></th>
						<td><input type="checkbox" id="<?php echo wpsb_get_option(); ?>[<?php echo WPSB_DEFAULT_NONLOGGEDUSERS_NAME; ?>]" name="<?php echo wpsb_get_option(); ?>[<?php echo WPSB_DEFAULT_NONLOGGEDUSERS_NAME; ?>]" value="1" <?php checked('1', wpsb_checkifset(WPSB_DEFAULT_NONLOGGEDUSERS_NAME, WPSB_DEFAULT_NONLOGGEDUSERS, $options)); ?> /></td>
					</tr>
					<?php wpsb_explanationrow(__('Check this box to display the Scribe box to non-logged-in users only.', wpsb_get_local())); ?>
					<?php wpsb_getlinebreak(); ?>
					<tr valign="top"><th scope="row"><strong><label title="<?php _e('Show full marketing text', wpsb_get_local()); ?>" for="<?php echo wpsb_get_option(); ?>[<?php echo WPSB_DEFAULT_USEEXTENDED_TEXT_NAME; ?>]"><?php _e('Show full marketing text', wpsb_get_local()); ?></label></strong></th>
						<td><input type="checkbox" id="<?php echo wpsb_get_option(); ?>[<?php echo WPSB_DEFAULT_USEEXTENDED_TEXT_NAME; ?>]" name="<?php echo wpsb_get_option(); ?>[<?php echo WPSB_DEFAULT_USEEXTENDED_TEXT_NAME; ?>]" value="1" <?php checked('1', wpsb_checkifset(WPSB_DEFAULT_USEEXTENDED_TEXT_NAME, WPSB_DEFAULT_USEEXTENDED_TEXT, $options)); ?> /></td>
					</tr>
					<?php wpsb_explanationrow(__('Check this box to display the full marketing text. If unchecked, only the first paragraph of text will be shown.', wpsb_get_local())); ?>
					<?php wpsb_getlinebreak(); ?>
					<tr valign="top"><th scope="row"><strong><label title="<?php _e('Select the default image.', wpsb_get_local()); ?>" for="<?php echo wpsb_get_option(); ?>[<?php echo WPSB_DEFAULT_IMAGE_NAME; ?>]"><?php _e('Default image', wpsb_get_local()); ?></label></strong></th>
						<td><select id="<?php echo wpsb_get_option(); ?>[<?php echo WPSB_DEFAULT_IMAGE_NAME; ?>]" name="<?php echo wpsb_get_option(); ?>[<?php echo WPSB_DEFAULT_IMAGE_NAME; ?>]" onChange="picture.src=this.options[this.selectedIndex].getAttribute('data-whichPicture');">
									<?php $images = explode(",", WPSB_AVAILABLE_IMAGES);
												for($i=0, $imagecount=count($images); $i < $imagecount; $i++) {
													$imageurl = wpsb_getimagefilename($images[$i] . '.png');
													if ($images[$i] === (wpsb_checkifset(WPSB_DEFAULT_IMAGE_NAME, WPSB_DEFAULT_IMAGE, $options))) { $selectedimage = $imageurl; }
													echo '<option data-whichPicture="' . $imageurl . '" value="' . $images[$i] . '"' . selected($images[$i], wpsb_checkifset(WPSB_DEFAULT_IMAGE_NAME, WPSB_DEFAULT_IMAGE, $options), false) . '>' . $images[$i] . '</option>';
												} ?>
							</select></td></tr>
					<tr><td colspan="2"><img src="<?php if (!$selectedimage) { echo wpsb_getimagefilename(WPSB_DEFAULT_IMAGE . '.png'); } else { echo $selectedimage; } ?>" id="picture" /></td></tr>
				</table>
				<?php submit_button(); ?>
			<?php } elseif ($active_tab == 'parameters') { ?>
			<h3 id="parameters"><img src="<?php echo wpsb_getimagefilename('parameters.png'); ?>" title="" alt="" height="64" width="64" align="absmiddle" /> <?php _e('Plugin Parameters and Default Values', wpsb_get_local()); ?></h3>
			These are the parameters for using the shortcode, or calling the plugin from your PHP code.

			<?php echo wpsb_parameters_table(wpsb_get_local(), wpsb_shortcode_defaults(), wpsb_required_parameters()); ?>			

			<h3 id="examples"><img src="<?php echo wpsb_getimagefilename('examples.png'); ?>" title="" alt="" height="64" width="64" align="absmiddle" /> <?php _e('Shortcode and PHP Examples', wpsb_get_local()); ?></h3>
			<h4><?php _e('Shortcode Format:', wpsb_get_local()); ?></h4>
			<?php echo wpsb_get_example_shortcode('wp-scribe-box', wpsb_shortcode_defaults(), wpsb_get_local()); ?>

			<h4><?php _e('PHP Format:', wpsb_get_local()); ?></h4>
			<?php echo wpsb_get_example_php_code('wp-scribe-box', 'scribe_aff_box', wpsb_shortcode_defaults()); ?>
			<?php _e('<small>Note: \'show\' is false by default; set it to <strong>true</strong> echo the output, or <strong>false</strong> to return the output to your PHP code.</small>', wpsb_get_local()); ?>
			<?php } else { ?>
			<h3 id="support"><img src="<?php echo wpsb_getimagefilename('support.png'); ?>" title="" alt="" height="64" width="64" align="absmiddle" /> <?php _e('Support', wpsb_get_local()); ?></h3>
				<div class="support">
				<?php echo wpsb_getsupportinfo(wpsb_get_slug(), wpsb_get_local()); ?>
				<small><?php _e('Disclaimer: This plugin is not affiliated with or endorsed by ShareASale or CopyBlogger Media.', wpsb_get_local()); ?></small>
				</div>
			<?php } ?>
			</form>
		</div>
		<?php 
	}
	// shortcode for posts and pages
	add_shortcode('wp-scribe-box', 'scribe_aff_box');
	// one function for shortcode and PHP
	function scribe_aff_box($atts, $content = null) {
		// get parameters
		extract(shortcode_atts(wpsb_shortcode_defaults(), $atts));
		// plugin is enabled/disabled from settings page only
		$options = wpsb_getpluginoptions();
		if (!empty($options)) {
			$enabled = (bool)$options[WPSB_DEFAULT_ENABLED_NAME];
		} else {
			$enabled = WPSB_DEFAULT_ENABLED;
		}

		$output = '';
		
		// ******************************
		// derive shortcode values from constants
		// ******************************
		if ($enabled) {
			$temp_url = constant('WPSB_DEFAULT_URL_NAME');
			$affiliate_url = $$temp_url;
			$temp_nofollow = constant('WPSB_DEFAULT_NOFOLLOW_NAME');
			$nofollow = $$temp_nofollow;
			$temp_window = constant('WPSB_DEFAULT_NEWWINDOW_NAME');
			$opennewwindow = $$temp_window;
			$temp_show = constant('WPSB_DEFAULT_SHOW_NAME');
			$show = $$temp_show;
			$temp_rounded = constant('WPSB_DEFAULT_ROUNDED_NAME');
			$rounded = $$temp_rounded;
			$temp_image = constant('WPSB_DEFAULT_IMAGE_NAME');
			$img = $$temp_image;
			$temp_nonloggedonly = constant('WPSB_DEFAULT_NONLOGGEDUSERS_NAME');
			$nonloggedonly = $$temp_nonloggedonly;
			$temp_showfulltext = constant('WPSB_DEFAULT_USEEXTENDED_TEXT_NAME');
			$showfulltext = $$temp_showfulltext;
		}
		// ******************************
		// sanitize user input
		// ******************************
		if ($enabled) {
			$affiliate_url = esc_url($affiliate_url);
			$rounded = (bool)$rounded;
			$nofollow = (bool)$nofollow;
			$opennewwindow = (bool)$opennewwindow;
			$show = (bool)$show;
			$img = sanitize_text_field($img);
			$nonloggedonly = (bool)$nonloggedonly;
			$showfulltext = (bool)$showfulltext;
			// allow alternate parameter names for affurl
			if (!empty($atts['url'])) {
				$affiliate_url = esc_url($atts['url']);
			} elseif (!empty($atts['link'])) {
				$affiliate_url = esc_url($atts['link']);
			} elseif (!empty($atts['href'])) {
				$affiliate_url = esc_url($atts['href']);
			}
		}
		// ******************************
		// check for parameters, then settings, then defaults
		// ******************************
		if ($enabled) {
			// check for overridden parameters, if nonexistent then get from DB
			if ($affiliate_url === WPSB_DEFAULT_URL) { // no url passed to function, try settings page
				$affiliate_url = $options[WPSB_DEFAULT_URL_NAME];
				if (($affiliate_url === WPSB_DEFAULT_URL) || ($affiliate_url === false)) { // no url on settings page either
					$enabled = false;
					$output = '<!-- ' . WPSB_PLUGIN_NAME . ': ' . __('plugin is disabled. Either you did not pass a necessary setting to the plugin, or did not configure a default. Check Settings page.', wpsb_get_local()) . ' -->';
				}
			}
			if ($enabled) { // same some cycles by skipping this code if plugin is disabled above
				$rounded = wpsb_setupvar($rounded, WPSB_ROUNDED, WPSB_DEFAULT_ROUNDED_NAME, $options);
				$nofollow = wpsb_setupvar($nofollow, WPSB_NOFOLLOW, WPSB_DEFAULT_NOFOLLOW_NAME, $options);
				$img = wpsb_setupvar($img, WPSB_DEFAULT_IMAGE, WPSB_DEFAULT_IMAGE_NAME, $options);
				$opennewwindow = wpsb_setupvar($opennewwindow, WPSB_DEFAULT_NEWWINDOW, WPSB_DEFAULT_NEWWINDOW_NAME, $options);
				$nonloggedonly = wpsb_setupvar($nonloggedonly, WPSB_DEFAULT_NONLOGGEDUSERS, WPSB_DEFAULT_NONLOGGEDUSERS_NAME, $options);				
				$showfulltext = wpsb_setupvar($showfulltext, WPSB_DEFAULT_USEEXTENDED_TEXT, WPSB_DEFAULT_USEEXTENDED_TEXT_NAME, $options);				
			}
		} // end enabled check
		// ******************************
		// do some actual work
		// ******************************
		if ($enabled) {
			if (is_user_logged_in() && $nonloggedonly) {
				// user is logged on but we don't want to show it to logged in users
				$output = '<!-- ' . WPSB_PLUGIN_NAME . ': ' . __('Set to show to non-logged-in users only, and current user is logged in.', wpsb_get_local()) . ' -->';
			} else {
				// enqueue CSS only on pages with shortcode
				wp_scribe_box_styles();

				if ($content) {
					$text = wp_kses_post(force_balance_tags($content));
				} else {
					$text = '<p><a' . ($opennewwindow ? ' onclick="window.open(this.href); return false;" onkeypress="window.open(this.href); return false;" ' : ' ') . ($nofollow ? ' rel="nofollow" ' : ' ') . 'href="' . $affiliate_url . '">Scribe</a> ';
					$text .= __('shows you the language the audience prefers when searching and discussing on social networks, before you begin to create content.', wpsb_get_local());
					$text .= __(' Once your content is created, Scribe reveals other profitable topics and keyword phrases. Scribe analyzes your content, and tells you exactly how to gently tweak it for better search engine rankings. ', wpsb_get_local()) . '<a' . ($opennewwindow ? ' onclick="window.open(this.href); return false;" onkeypress="window.open(this.href); return false;" ' : ' ') . ($nofollow ? ' rel="nofollow" ' : ' ') . 'href="' . $affiliate_url . '">Scribe</a>' . __(' also analyzes your overall site content to help you execute on your go-forward content strategy.', wpsb_get_local()) . '</p>';
					if ($showfulltext) {
						$text .= '<p><a' . ($opennewwindow ? ' onclick="window.open(this.href); return false;" onkeypress="window.open(this.href); return false;" ' : ' ') . ($nofollow ? ' rel="nofollow" ' : ' ') . 'href="' . $affiliate_url . '">Scribe</a> ';
						$text .= __('helps you crosslink your content to increase usability and time on site, identify websites for guest writing, strategic alliances, and link building, and locate social media users who\'ll want to share your content.', wpsb_get_local()) . '</p>';
						$text .= '<p>' . __('Scribe is the ultimate optimization tool for empowered online marketing. ', wpsb_get_local());
						$text .= '<a' . ($opennewwindow ? ' onclick="window.open(this.href); return false;" onkeypress="window.open(this.href); return false;" ' : ' ') . ($nofollow ? ' rel="nofollow" ' : ' ') . 'href="' . $affiliate_url . '">' . __('See what Scribe can do!', wpsb_get_local()) . '</a></p>';
					}
				}
				// calculate image url
				$images = explode(",", WPSB_AVAILABLE_IMAGES);
				if (!in_array($img, $images)) {
					$img = $images[$options[WPSB_DEFAULT_IMAGE_NAME]];
					if (!$img) { $img = WPSB_DEFAULT_IMAGE; }
				}
				$imageurl = wpsb_getimagefilename($img . '.png');
				$imagedata = getimagesize($imageurl);
				$output = '<div id="scribe-box"' . ($rounded ? ' class="wpsb-rounded-corners"' : '') . '>';
				$output .= '<h3>' . __('Get More Traffic and Leads With Less Time and Hassle', wpsb_get_local()) . '</h3>';
				$output .= '<a' . ($opennewwindow ? ' onclick="window.open(this.href); return false;" onkeypress="window.open(this.href); return false;" ' : ' ') . ($nofollow ? ' rel="nofollow" ' : ' ') . 'href="' . $affiliate_url . '">';
				$output .= '<img class="alignright" src="' . $imageurl . '" alt="' . __('Scribe', wpsb_get_local()) . '" title="' . __('Try It Now!', wpsb_get_local()) . '" width="' . $imagedata[0] . '" height="' . $imagedata[1] . '" /></a>';
				$output .= do_shortcode($text) . '</div>';
			}
		} else { // plugin disabled
			$output = '<!-- ' . WPSB_PLUGIN_NAME . ': ' . __('plugin is disabled. Either you did not pass a necessary setting to the plugin, or did not configure a default. Check Settings page.', wpsb_get_local()) . ' -->';
		}
		if ($show) {
			echo $output;
		} else {
			return $output;
		}
	} // end shortcode function
	// auto insert at end of posts?
	add_action('the_content', 'wpsb_insert_premise_box');
	function wpsb_insert_premise_box($content) {
		if (is_single()) {
			$options = wpsb_getpluginoptions();
			if (!empty($options)) {
				if ($options[WPSB_DEFAULT_AUTO_INSERT_NAME]) {
					$content .= scribe_aff_box($options);
				}
			}
		}
		return $content;
	}
	// show admin messages to plugin user
	add_action('admin_notices', 'wpsb_showAdminMessages');
	function wpsb_showAdminMessages() {
		// http://wptheming.com/2011/08/admin-notices-in-wordpress/
		global $pagenow;
		if (current_user_can(WPSB_PERMISSIONS_LEVEL)) { // user has privilege
			if ($pagenow == 'options-general.php') {
				if (isset($_GET['page'])) {
					if ($_GET['page'] == wpsb_get_slug()) { // we are on this plugin's settings page
						$options = wpsb_getpluginoptions();
						if (!empty($options)) {
							$enabled = (bool)$options[WPSB_DEFAULT_ENABLED_NAME];
							$affiliate_url = $options[WPSB_DEFAULT_URL_NAME];
							if (!$enabled) {
								echo '<div id="message" class="error">' . WPSB_PLUGIN_NAME . ' ' . __('is currently disabled.', wpsb_get_local()) . '</div>';
							}
							if (($affiliate_url === WPSB_DEFAULT_URL) || ($affiliate_url === false)) {
								echo '<div id="message" class="updated">' . __('WARNING: Affiliate URL missing. Please enter it below, or pass it to the shortcode or function, otherwise the plugin won\'t do anything.', wpsb_get_local()) . '</div>';
							}
						}
					}
				}
			} // end page check
		} // end privilege check
	} // end admin msgs function
	// add admin CSS if we are on the plugin options page
	add_action('admin_head', 'insert_wpsb_admin_css');
	function insert_wpsb_admin_css() {
		global $pagenow;
		if (current_user_can(WPSB_PERMISSIONS_LEVEL)) { // user has privilege
			if ($pagenow == 'options-general.php') {
				if (isset($_GET['page'])) {
					if ($_GET['page'] == wpsb_get_slug()) { // we are on this plugin's settings page
						wpsb_admin_styles();
					}
				}
			}
		}
	}
	// add helpful links to plugin page next to plugin name
	// http://bavotasan.com/2009/a-settings-link-for-your-wordpress-plugins/
	// http://wpengineer.com/1295/meta-links-for-wordpress-plugins/
	add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'wpsb_plugin_settings_link');
	add_filter('plugin_row_meta', 'wpsb_meta_links', 10, 2);
	
	function wpsb_plugin_settings_link($links) {
		return wpsb_settingslink($links, wpsb_get_slug(), wpsb_get_local());
	}
	function wpsb_meta_links($links, $file) {
		if ($file == plugin_basename(__FILE__)) {
			$links = array_merge($links,
			array(
				sprintf(__('<a href="http://wordpress.org/support/plugin/%s">Support</a>', wpsb_get_local()), wpsb_get_slug()),
				sprintf(__('<a href="http://wordpress.org/extend/plugins/%s/">Documentation</a>', wpsb_get_local()), wpsb_get_slug()),
				sprintf(__('<a href="http://wordpress.org/plugins/%s/faq/">FAQ</a>', wpsb_get_local()), wpsb_get_slug())
			));
		}
		return $links;	
	}
	// enqueue/register the admin CSS file
	function wpsb_admin_styles() {
		wp_enqueue_style('wpsb_admin_style');
	}
	function register_wpsb_admin_style() {
		wp_register_style( 'wpsb_admin_style',
			plugins_url(wpsb_get_path() . '/css/admin.css'),
			array(),
			WPSB_VERSION . "_" . date('njYHis', filemtime(dirname(__FILE__) . '/css/admin.css')),
			'all' );
	}
	// enqueue/register the plugin CSS file
	function wp_scribe_box_styles() {
		wp_enqueue_style('wp_scribe_box_style');
	}
	function register_wp_scribe_box_style() {
		wp_register_style('wp_scribe_box_style', 
			plugins_url(wpsb_get_path() . '/css/wp-scribe-box.css'), 
			array(), 
			WPSB_VERSION . "_" . date('njYHis', filemtime(dirname(__FILE__) . '/css/wp-scribe-box.css')),
			'all' );
	}
	// enqueue/register the admin JS file
	add_action('admin_enqueue_scripts', 'wpsb_ed_buttons');
	function wpsb_ed_buttons($hook) {
		if (($hook == 'post-new.php') || ($hook == 'post.php')) {
			wp_enqueue_script('wpsb_add_editor_button');
		}
	}
	function register_wpsb_admin_script() {
		wp_register_script('wpsb_add_editor_button',
			plugins_url(wpsb_get_path() . '/js/editor_button.js'), 
			array('quicktags'), 
			WPSB_VERSION . "_" . date('njYHis', filemtime(dirname(__FILE__) . '/js/editor_button.js')),
			true);
	}
	// when plugin is activated, create options array and populate with defaults
	register_activation_hook(__FILE__, 'wpsb_activate');
	function wpsb_activate() {
		$options = wpsb_getpluginoptions();
		update_option(wpsb_get_option(), $options);
		
		// delete option when plugin is uninstalled
		register_uninstall_hook(__FILE__, 'uninstall_wpsb_plugin');
	}
	function uninstall_wpsb_plugin() {
		delete_option(wpsb_get_option());
	}
	
	// generic function that returns plugin options from DB
	// if option does not exist, returns plugin defaults
	function wpsb_getpluginoptions() {
		return get_option(wpsb_get_option(), array(
			WPSB_DEFAULT_ENABLED_NAME => WPSB_DEFAULT_ENABLED, 
			WPSB_DEFAULT_URL_NAME => WPSB_DEFAULT_URL, 
			WPSB_DEFAULT_ROUNDED_NAME => WPSB_ROUNDED, 
			WPSB_DEFAULT_NOFOLLOW_NAME => WPSB_NOFOLLOW, 
			WPSB_DEFAULT_IMAGE_NAME => WPSB_DEFAULT_IMAGE, 
			WPSB_DEFAULT_AUTO_INSERT_NAME => WPSB_DEFAULT_AUTO_INSERT, 
			WPSB_DEFAULT_NEWWINDOW_NAME => WPSB_DEFAULT_NEWWINDOW,
			WPSB_DEFAULT_NONLOGGEDUSERS_NAME => WPSB_DEFAULT_NONLOGGEDUSERS,
			WPSB_DEFAULT_USEEXTENDED_TEXT_NAME => WPSB_DEFAULT_USEEXTENDED_TEXT
			));
	}
	// function to return shortcode defaults
	function wpsb_shortcode_defaults() {
		return array(
			WPSB_DEFAULT_URL_NAME => WPSB_DEFAULT_URL, 
			WPSB_DEFAULT_ROUNDED_NAME => WPSB_ROUNDED, 
			WPSB_DEFAULT_NOFOLLOW_NAME => WPSB_NOFOLLOW, 
			WPSB_DEFAULT_IMAGE_NAME => WPSB_DEFAULT_IMAGE, 
			WPSB_DEFAULT_NEWWINDOW_NAME => WPSB_DEFAULT_NEWWINDOW, 
			WPSB_DEFAULT_SHOW_NAME => WPSB_DEFAULT_SHOW,
			WPSB_DEFAULT_NONLOGGEDUSERS_NAME => WPSB_DEFAULT_NONLOGGEDUSERS,
			WPSB_DEFAULT_USEEXTENDED_TEXT_NAME => WPSB_DEFAULT_USEEXTENDED_TEXT
			);
	}
	// function to return parameter status (required or not)
	function wpsb_required_parameters() {
		return array(
			true,
			false,
			false,
			false,
			false,
			false,
			false,
			true
		);
	}
	
	// encapsulate these and call them throughout the plugin instead of hardcoding the constants everywhere
	function wpsb_get_slug() { return WPSB_SLUG; }
	function wpsb_get_local() { return WPSB_LOCAL; }
	function wpsb_get_option() { return WPSB_OPTION; }
	function wpsb_get_path() { return WPSB_PATH; }
	
	function wpsb_settingslink($linklist, $slugname = '', $localname = '') {
		$settings_link = sprintf( __('<a href="options-general.php?page=%s">Settings</a>', $localname), $slugname);
		array_unshift($linklist, $settings_link);
		return $linklist;
	}
	function wpsb_setupvar($var, $defaultvalue, $defaultvarname, $optionsarr) {
		if ($var == $defaultvalue) {
			$var = $optionsarr[$defaultvarname];
			if (!$var) {
				$var = $defaultvalue;
			}
		}
		return $var;
	}
	function wpsb_getsupportinfo($slugname = '', $localname = '') {
		$output = __('Do you need help with this plugin? Check out the following resources:', $localname);
		$output .= '<ol>';
		$output .= '<li>' . sprintf( __('<a href="http://wordpress.org/extend/plugins/%s/">Documentation</a>', $localname), $slugname) . '</li>';
		$output .= '<li>' . sprintf( __('<a href="http://wordpress.org/plugins/%s/faq/">FAQ</a><br />', $localname), $slugname) . '</li>';
		$output .= '<li>' . sprintf( __('<a href="http://wordpress.org/support/plugin/%s">Support Forum</a><br />', $localname), $slugname) . '</li>';
		$output .= '<li>' . sprintf( __('<a href="http://www.jimmyscode.com/wordpress/%s">Plugin Homepage / Demo</a><br />', $localname), $slugname) . '</li>';
		$output .= '<li>' . sprintf( __('<a href="http://wordpress.org/extend/plugins/%s/developers/">Development</a><br />', $localname), $slugname) . '</li>';
		$output .= '<li>' . sprintf( __('<a href="http://wordpress.org/plugins/%s/changelog/">Changelog</a><br />', $localname), $slugname) . '</li>';
		$output .= '</ol>';
		
		$output .= sprintf( __('If you like this plugin, please <a href="http://wordpress.org/support/view/plugin-reviews/%s/">rate it on WordPress.org</a>', $localname), $slugname);
		$output .= sprintf( __(' and click the <a href="http://wordpress.org/plugins/%s/#compatibility">Works</a> button. ', $localname), $slugname);
		$output .= '<br /><br /><br />';
		$output .= __('Your donations encourage further development and support. ', $localname);
		$output .= '<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=7EX9NB9TLFHVW"><img src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_LG.gif" alt="Donate with PayPal" title="Support this plugin" width="92" height="26" /></a>';
		$output .= '<br /><br />';
		return $output;
	}
	
	function wpsb_parameters_table($localname = '', $sc_defaults, $reqparms) {
	  $output = '<table class="widefat">';
		$output .= '<thead><tr>';
		$output .= '<th title="' . __('The name of the parameter', $localname) . '"><strong>' . __('Parameter Name', $localname) . '</strong></th>';
		$output .= '<th title="' . __('Is this parameter required?', $localname) . '"><strong>' . __('Is Required?', $localname) . '</strong></th>';
		$output .= '<th title="' . __('What data type this parameter accepts', $localname) . '"><strong>' . __('Data Type', $localname) . '</strong></th>';
		$output .= '<th title="' . __('What, if any, is the default if no value is specified', $localname) . '"><strong>' . __('Default Value', $localname) . '</strong></th>';
		$output .= '</tr></thead>';
		$output .= '<tbody>';
		
		$plugin_defaults_keys = array_keys($sc_defaults);
		$plugin_defaults_values = array_values($sc_defaults);
		$required = $reqparms;
		for($i = 0; $i < count($plugin_defaults_keys); $i++) {
			$output .= '<tr>';
			$output .= '<td><strong>' . $plugin_defaults_keys[$i] . '</strong></td>';
			$output .= '<td>';
			
			if ($required[$i] === true) {
				$output .= '<strong>';
				$output .= __('Yes', $localname);
				$output .= '</strong>';
			} else {
				$output .= __('No', $localname);
			}
			
			$output .= '</td>';
			$output .= '<td>' . gettype($plugin_defaults_values[$i]) . '</td>';
			$output .= '<td>';
			
			if ($plugin_defaults_values[$i] === true) {
				$output .= '<strong>';
				$output .= __('true', $localname);
				$output .= '</strong>';
			} elseif ($plugin_defaults_values[$i] === false) {
				$output .= __('false', $localname);
			} elseif ($plugin_defaults_values[$i] === '') {
				$output .= '<em>';
				$output .= __('this value is blank by default', $localname);
				$output .= '</em>';
			} elseif (is_numeric($plugin_defaults_values[$i])) {
				$output .= $plugin_defaults_values[$i];
			} else { 
				$output .= '"' . $plugin_defaults_values[$i] . '"';
			} 
			$output .= '</td>';
			$output .= '</tr>';
		}
		$output .= '</tbody>';
		$output .= '</table>';
		
		return $output;
	}
	function wpsb_get_example_shortcode($shortcodename = '', $sc_defaults, $localname = '') {
		$output = '<pre style="background:#FFF">[' . $shortcodename . ' ';
		
		$plugin_defaults_keys = array_keys($sc_defaults);
		$plugin_defaults_values = array_values($sc_defaults);
		
		for($i = 0; $i < count($plugin_defaults_keys); $i++) {
			if ($plugin_defaults_keys[$i] !== 'show') {
				if (gettype($plugin_defaults_values[$i]) === 'string') {
					$output .= '<strong>' . $plugin_defaults_keys[$i] . '</strong>=\'' . $plugin_defaults_values[$i] . '\'';
				} elseif (gettype($plugin_defaults_values[$i]) === 'boolean') {
					$output .= '<strong>' . $plugin_defaults_keys[$i] . '</strong>=' . ($plugin_defaults_values[$i] == false ? 'false' : 'true');
				} else {
					$output .= '<strong>' . $plugin_defaults_keys[$i] . '</strong>=' . $plugin_defaults_values[$i];
				}
				if ($i < count($plugin_defaults_keys) - 2) {
					$output .= ' ';
				}
			}
		}
		$output .= ']</pre>';
		
		return $output;
	}
	
	function wpsb_get_example_php_code($shortcodename = '', $internalfunctionname = '', $sc_defaults) {
		
		$plugin_defaults_keys = array_keys($sc_defaults);
		$plugin_defaults_values = array_values($sc_defaults);
		
		$output = '<pre style="background:#FFF">';
		$output .= 'if (shortcode_exists(\'' . $shortcodename . '\')) {<br />';
		$output .= '  $atts = array(<br />';
		for($i = 0; $i < count($plugin_defaults_keys); $i++) {
			$output .= '    \'' . $plugin_defaults_keys[$i] . '\' => ';
			if (gettype($plugin_defaults_values[$i]) === 'string') {
				$output .= '\'' . $plugin_defaults_values[$i] . '\'';
			} elseif (gettype($plugin_defaults_values[$i]) === 'boolean') {
				$output .= ($plugin_defaults_values[$i] == false ? 'false' : 'true');
			} else {
				$output .= $plugin_defaults_values[$i];
			}
			if ($i < count($plugin_defaults_keys) - 1) {
				$output .= ', <br />';
			}
		}
		$output .= '<br />  );<br />';
		$output .= '   echo ' . $internalfunctionname . '($atts);';
		$output .= '<br />}';
		$output .= '</pre>';
		return $output;	
	}
	function wpsb_checkifset($optionname, $optiondefault, $optionsarr) {
		return (isset($optionsarr[$optionname]) ? $optionsarr[$optionname] : $optiondefault);
	}
	function wpsb_getlinebreak() {
	  echo '<tr valign="top"><td colspan="2"></td></tr>';
	}
	function wpsb_explanationrow($msg = '') {
		echo '<tr valign="top"><td></td><td><em>' . $msg . '</em></td></tr>';
	}
	function wpsb_getimagefilename($fname = '') {
		return plugins_url(wpsb_get_path() . '/images/' . $fname);
	}
?>
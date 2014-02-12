<?php
/*
Plugin Name: WP Scribe Box
Plugin URI: http://www.jimmyscode.com/wordpress/wp-scribe-box/
Description: Display the Scribe affiliate box on your WordPress website. Make money as a Scribe affiliate.
Version: 0.0.8
Author: Jimmy Pe&ntilde;a
Author URI: http://www.jimmyscode.com/
License: GPLv2 or later
*/
// plugin constants
define('WPSB_VERSION', '0.0.8');
define('WPSB_PLUGIN_NAME', 'WP Scribe Box');
define('WPSB_SLUG', 'wp-scribe-box');
define('WPSB_OPTION', 'wp_scribe_box');
define('WPSB_LOCAL', 'wp_scribe_box');
/* defaults */
define('WPSB_DEFAULT_ENABLED', true);
define('WPSB_DEFAULT_URL', '');
define('WPSB_ROUNDED', false);
define('WPSB_NOFOLLOW', true);
define('WPSB_AVAILABLE_IMAGES', 'scribe-125x125,scribe-235x247,scribe-250x250,scribe-260x125,scribe-300x250');
define('WPSB_DEFAULT_IMAGE', '');
define('WPSB_DEFAULT_AUTO_INSERT', false);
define('WPSB_DEFAULT_SHOW', false);
define('WPSB_DEFAULT_NEWWINDOW', false);
/* default option names */
define('WPSB_DEFAULT_ENABLED_NAME', 'enabled');
define('WPSB_DEFAULT_URL_NAME', 'affurl');
define('WPSB_DEFAULT_ROUNDED_NAME', 'rounded');
define('WPSB_DEFAULT_NOFOLLOW_NAME', 'nofollow');
define('WPSB_DEFAULT_IMAGE_NAME', 'img');
define('WPSB_DEFAULT_AUTO_INSERT_NAME', 'autoinsert');
define('WPSB_DEFAULT_SHOW_NAME', 'show');
define('WPSB_DEFAULT_NEWWINDOW_NAME', 'opennewwindow');

// oh no you don't
if (!defined('ABSPATH')) {
  wp_die(__('Do not access this file directly.', WPSB_LOCAL));
}

// delete option when plugin is uninstalled
register_uninstall_hook(__FILE__, 'uninstall_wpsb_plugin');
function uninstall_wpsb_plugin() {
  delete_option(WPSB_OPTION);
}

// localization to allow for translations
add_action('init', 'wp_scribe_box_translation_file');
function wp_scribe_box_translation_file() {
  $plugin_path = plugin_basename(dirname(__FILE__)) . '/translations';
  load_plugin_textdomain(WPSB_LOCAL, '', $plugin_path);
  register_wp_scribe_box_style();
}
// tell WP that we are going to use new options
add_action('admin_init', 'wp_scribe_box_options_init');
function wp_scribe_box_options_init() {
  register_setting('wp_scribe_box_options', WPSB_OPTION, 'wpsb_validation');
  register_wpsb_admin_style();
	register_wpsb_admin_script();
}
// validation function
function wpsb_validation($input) {
  // sanitize url
  $input[WPSB_DEFAULT_URL_NAME] = esc_url($input[WPSB_DEFAULT_URL_NAME]);
  // sanitize image
  $input[WPSB_DEFAULT_IMAGE_NAME] = sanitize_text_field($input[WPSB_DEFAULT_IMAGE_NAME]);
  if (!$input[WPSB_DEFAULT_IMAGE_NAME]) { // set to default
    $input[WPSB_DEFAULT_IMAGE_NAME] = WPSB_DEFAULT_IMAGE;
  }
  return $input;
}
// add Settings sub-menu
add_action('admin_menu', 'wpsb_plugin_menu');
function wpsb_plugin_menu() {
  add_options_page(WPSB_PLUGIN_NAME, WPSB_PLUGIN_NAME, 'manage_options', WPSB_SLUG, 'wp_scribe_box_page');
}
// plugin settings page
// http://planetozh.com/blog/2009/05/handling-plugins-options-in-wordpress-28-with-register_setting/
function wp_scribe_box_page() {
  // check perms
  if (!current_user_can('manage_options')) {
    wp_die(__('You do not have sufficient permission to access this page', WPSB_LOCAL));
  }
?>
  <div class="wrap">
    <h2><?php echo WPSB_PLUGIN_NAME; ?></h2>
    <form method="post" action="options.php">
      <div>You are running plugin version <strong><?php echo WPSB_VERSION; ?></strong>.</div>
      <?php settings_fields('wp_scribe_box_options'); ?>
      <?php $options = wpsb_getpluginoptions(); ?>
	<?php /* update_option(WPSB_OPTION, $options); */ ?>
      <table class="form-table" id="theme-options-wrap">
        <tr valign="top"><th scope="row"><strong><label title="<?php _e('Is plugin enabled? Uncheck this to turn it off temporarily.', WPSB_LOCAL); ?>" for="wp_scribe_box[<?php echo WPSB_DEFAULT_ENABLED_NAME; ?>]"><?php _e('Plugin enabled?', WPSB_LOCAL); ?></label></strong></th>
		<td><input type="checkbox" id="wp_scribe_box[<?php echo WPSB_DEFAULT_ENABLED_NAME; ?>]" name="wp_scribe_box[<?php echo WPSB_DEFAULT_ENABLED_NAME; ?>]" value="1" <?php checked('1', $options[WPSB_DEFAULT_ENABLED_NAME]); ?> /></td>
        </tr>
	  <tr valign="top"><td colspan="2"><?php _e('Is plugin enabled? Uncheck this to turn it off temporarily.', WPSB_LOCAL); ?></td></tr>
        <tr valign="top"><th scope="row"><strong><label title="<?php _e('Enter your affiliate URL here. This will be inserted wherever you use the shortcode.', WPSB_LOCAL); ?>" for="wp_scribe_box[<?php echo WPSB_DEFAULT_URL_NAME; ?>]"><?php _e('Your Affiliate URL', WPSB_LOCAL); ?></label></strong></th>
          <td><input type="url" id="wp_scribe_box[<?php echo WPSB_DEFAULT_URL_NAME; ?>]" name="wp_scribe_box[<?php echo WPSB_DEFAULT_URL_NAME; ?>]" value="<?php echo $options[WPSB_DEFAULT_URL_NAME]; ?>" /></td>
        </tr>
        <tr valign="top"><td colspan="2"><?php _e('Enter your affiliate URL here. This will be inserted wherever you use the shortcode.', WPSB_LOCAL); ?></td></tr>
        <tr valign="top"><th scope="row"><strong><label title="<?php _e('Do you want to apply rounded corners CSS to the output?', WPSB_LOCAL); ?>" for="wp_scribe_box[<?php echo WPSB_DEFAULT_ROUNDED_NAME; ?>]"><?php _e('Rounded corners CSS?', WPSB_LOCAL); ?></label></strong></th>
		<td><input type="checkbox" id="wp_scribe_box[<?php echo WPSB_DEFAULT_ROUNDED_NAME; ?>]" name="wp_scribe_box[<?php echo WPSB_DEFAULT_ROUNDED_NAME; ?>]" value="1" <?php checked('1', $options[WPSB_DEFAULT_ROUNDED_NAME]); ?> /></td>
        </tr>
	  <tr valign="top"><td colspan="2"><?php _e('Do you want to apply rounded corners CSS to the output?', WPSB_LOCAL); ?></td></tr>
        <tr valign="top"><th scope="row"><strong><label title="<?php _e('Check this box to automatically insert the output at the end of blog posts. If you do not do this then you will need to manually insert shortcode or call the function in PHP.', WPSB_LOCAL); ?>" for="wp_scribe_box[<?php echo WPSB_DEFAULT_AUTO_INSERT_NAME; ?>]"><?php _e('Auto insert Scribe box at the end of posts?', WPSB_LOCAL); ?></label></strong></th>
		<td><input type="checkbox" id="wp_scribe_box[<?php echo WPSB_DEFAULT_AUTO_INSERT_NAME; ?>]" name="wp_scribe_box[<?php echo WPSB_DEFAULT_AUTO_INSERT_NAME; ?>]" value="1" <?php checked('1', $options[WPSB_DEFAULT_AUTO_INSERT_NAME]); ?> /></td>
        </tr>
	  <tr valign="top"><td colspan="2"><?php _e('Check this box to automatically insert the output at the end of blog posts. If you don\'t do this then you will need to manually insert shortcode or call the function in PHP.', WPSB_LOCAL); ?></td></tr>
        <tr valign="top"><th scope="row"><strong><label title="<?php _e('Do you want to add rel=nofollow to all links?', WPSB_LOCAL); ?>" for="wp_scribe_box[<?php echo WPSB_DEFAULT_NOFOLLOW_NAME; ?>]"><?php _e('Nofollow links?', WPSB_LOCAL); ?></label></strong></th>
		<td><input type="checkbox" id="wp_scribe_box[<?php echo WPSB_DEFAULT_NOFOLLOW_NAME; ?>]" name="wp_scribe_box[<?php echo WPSB_DEFAULT_NOFOLLOW_NAME; ?>]" value="1" <?php checked('1', $options[WPSB_DEFAULT_NOFOLLOW_NAME]); ?> /></td>
        </tr>
	  <tr valign="top"><td colspan="2"><?php _e('Do you want to add rel="nofollow" to all links?', WPSB_LOCAL); ?></td></tr>
        <tr valign="top"><th scope="row"><strong><label title="<?php _e('Check this box to open links in a new window.', WPSB_LOCAL); ?>" for="wp_scribe_box[<?php echo WPSB_DEFAULT_NEWWINDOW_NAME; ?>]"><?php _e('Open links in new window?', WPSB_LOCAL); ?></label></strong></th>
		<td><input type="checkbox" id="wp_scribe_box[<?php echo WPSB_DEFAULT_NEWWINDOW_NAME; ?>]" name="wp_scribe_box[<?php echo WPSB_DEFAULT_NEWWINDOW_NAME; ?>]" value="1" <?php checked('1', $options[WPSB_DEFAULT_NEWWINDOW_NAME]); ?> /></td>
        </tr>
	  <tr valign="top"><td colspan="2"><?php _e('Check this box to open links in a new window.', WPSB_LOCAL); ?></td></tr>
        <tr valign="top"><th scope="row"><strong><label title="<?php _e('Select the default image.', WPSB_LOCAL); ?>" for="wp_scribe_box[<?php echo WPSB_DEFAULT_IMAGE_NAME; ?>]"><?php _e('Default image', WPSB_LOCAL); ?></label></strong></th>
		<td><select id="wp_scribe_box[<?php echo WPSB_DEFAULT_IMAGE_NAME; ?>]" name="wp_scribe_box[<?php echo WPSB_DEFAULT_IMAGE_NAME; ?>]" onChange="picture.src=this.options[this.selectedIndex].getAttribute('data-whichPicture');">
                <?php $images = explode(",", WPSB_AVAILABLE_IMAGES);
                      for($i=0, $imagecount=count($images); $i < $imagecount; $i++) {
                        $imageurl = plugins_url(plugin_basename(dirname(__FILE__) . '/images/' . $images[$i] . '.png'));
                        if ($images[$i] === $options[WPSB_DEFAULT_IMAGE_NAME]) { $selectedimage = $imageurl; }
                        echo '<option data-whichPicture="' . $imageurl . '" value="' . $images[$i] . '" ' . selected($images[$i], $options[WPSB_DEFAULT_IMAGE_NAME]) . '>' . $images[$i] . '</option>';
                      } ?>
            </select></td></tr>
        <tr><td colspan="2"><img src="<?php if (!$selectedimage) { echo plugins_url(plugin_basename(dirname(__FILE__) . '/images/' . WPSB_DEFAULT_IMAGE . '.png')); } else { echo $selectedimage; } ?>" id="picture" /></td></tr>
	  <tr valign="top"><td colspan="2"><?php _e('Select the default image.', WPSB_LOCAL); ?></td></tr>
      </table>
      <?php submit_button(); ?>
    </form>
    <h3>Plugin Arguments and Defaults</h3>
    <table class="widefat">
      <thead>
        <tr>
          <th title="<?php _e('The name of the parameter', WPSB_LOCAL); ?>"><?php _e('Argument', WPSB_LOCAL); ?></th>
	  <th title="<?php _e('Is this parameter required?', WPSB_LOCAL); ?>"><?php _e('Required?', WPSB_LOCAL); ?></th>
          <th title="<?php _e('What data type this parameter accepts', WPSB_LOCAL); ?>"><?php _e('Type', WPSB_LOCAL); ?></th>
          <th title="<?php _e('What, if any, is the default if no value is specified', WPSB_LOCAL); ?>"><?php _e('Default Value', WPSB_LOCAL); ?></th>
        </tr>
      </thead>
      <tbody>
    <?php $plugin_defaults_keys = array_keys(wpsb_shortcode_defaults());
					$plugin_defaults_values = array_values(wpsb_shortcode_defaults());
					$wpsb_required = wpsb_required_parameters();
					for($i=0; $i<count($plugin_defaults_keys);$i++) { ?>
        <tr>
          <td><?php echo $plugin_defaults_keys[$i]; ?></td>
					<td><?php echo $wpsb_required[$i]; ?></td>
          <td><?php echo gettype($plugin_defaults_values[$i]); ?></td>
          <td><?php 
						if ($plugin_defaults_values[$i] === true) {
							echo 'true';
						} elseif ($plugin_defaults_values[$i] === false) {
							echo 'false';
						} elseif ($plugin_defaults_values[$i] === '') {
							echo '<em>(this value is blank by default)</em>';
						} else {
							echo $plugin_defaults_values[$i];
						} ?></td>
        </tr>
    <?php } ?>
    </tbody>
    </table>
    <h3>Support</h3>
	<div class="support">
		<?php echo '<a href="http://wordpress.org/extend/plugins/' . WPSB_SLUG . '/">' . __('Documentation', WPSB_LOCAL) . '</a> | ';
        echo '<a href="http://wordpress.org/plugins/' . WPSB_SLUG . '/faq/">' . __('FAQ', WPSB_LOCAL) . '</a><br />';
			?>
      If you like this plugin, please <a href="http://wordpress.org/support/view/plugin-reviews/<?php echo WPSB_SLUG; ?>/">rate it on WordPress.org</a> and click the "Works" button so others know it will work for your WordPress version. For support please visit the <a href="http://wordpress.org/support/plugin/<?php echo WPSB_SLUG; ?>">forums</a>. <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=7EX9NB9TLFHVW"><img src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_LG.gif" alt="Donate with PayPal" title="Donate with PayPal" width="92" height="26" /></a>
    </div>
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
  $enabled = $options[WPSB_DEFAULT_ENABLED_NAME];

  // ******************************
  // derive shortcode values from constants
  // ******************************
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

  // ******************************
  // sanitize user input
  // ******************************
  $affiliate_url = esc_url($affiliate_url);
  $rounded = (bool)$rounded;
  $nofollow = (bool)$nofollow;
  $opennewwindow = (bool)$opennewwindow;
  $show = (bool)$show;
  $img = sanitize_text_field($img);

  // ******************************
  // check for parameters, then settings, then defaults
  // ******************************
  if ($enabled) {
    // check for overridden parameters, if nonexistent then get from DB
    if ($affiliate_url === WPSB_DEFAULT_URL) { // no url passed to function, try settings page
      $affiliate_url = $options[WPSB_DEFAULT_URL_NAME];
      if (($affiliate_url === WPSB_DEFAULT_URL) || ($affiliate_url === false)) { // no url on settings page either
        $enabled = false;
      }
    }
    if ($rounded == WPSB_ROUNDED) {
      $rounded = $options[WPSB_DEFAULT_ROUNDED_NAME];
      if ($rounded === false) {
        $rounded = WPSB_ROUNDED;
      }
    }
    if ($nofollow == WPSB_NOFOLLOW) {
	$nofollow = $options[WPSB_DEFAULT_NOFOLLOW_NAME];
	if ($nofollow === false) {
	  $nofollow = WPSB_NOFOLLOW;
	}
    }
    if ($img == WPSB_DEFAULT_IMAGE) {
      $img = $options[WPSB_DEFAULT_IMAGE_NAME];
      if ($img === false) {
        $img = WPSB_DEFAULT_IMAGE;
      }
    }
    if ($opennewwindow == WPSB_DEFAULT_NEWWINDOW) {
      $opennewwindow = $options[WPSB_DEFAULT_NEWWINDOW_NAME];
      if ($opennewwindow === false) {
        $opennewwindow = WPSB_DEFAULT_NEWWINDOW;
      }
    }
  } // end enabled check

  // ******************************
  // do some actual work
  // ******************************
  if ($enabled) {
    // enqueue CSS only on pages with shortcode
    wp_scribe_box_styles();

    if ($content) {
      $text = wp_kses_post(force_balance_tags($content));
    } else {
      $text = '<p><a' . ($opennewwindow ? ' onclick="window.open(this.href); return false;" onkeypress="window.open(this.href); return false;" ' : ' ') . ($nofollow ? ' rel="nofollow" ' : ' ') . 'href="' . $affiliate_url . '">Scribe</a> ';
      $text .= __('shows you the language the audience prefers when searching and discussing on social networks, before you begin to create content.', WPSB_LOCAL);
      $text .= __(' Once your content is created, Scribe reveals other profitable topics and keyword phrases. Scribe analyzes your content, and tells you exactly how to gently tweak it for better search engine rankings. Scribe also analyzes your overall site content to help you execute on your go-forward content strategy.', WPSB_LOCAL) . '</p>';
      $text .= '<p><a' . ($opennewwindow ? ' onclick="window.open(this.href); return false;" onkeypress="window.open(this.href); return false;" ' : ' ') . ($nofollow ? ' rel="nofollow" ' : ' ') . 'href="' . $affiliate_url . '">Scribe</a> ';
      $text .= __('helps you crosslink your content to increase usability and time on site, identify websites for guest writing, strategic alliances, and link building, and locate social media users who\'ll want to share your content.', WPSB_LOCAL) . '</p>';
      $text .= '<p>' . __('Scribe is the ultimate optimization tool for empowered online marketing. ', WPSB_LOCAL);
      $text .= '<a' . ($opennewwindow ? ' onclick="window.open(this.href); return false;" onkeypress="window.open(this.href); return false;" ' : ' ') . ($nofollow ? ' rel="nofollow" ' : ' ') . 'href="' . $affiliate_url . '">' . __('See what Scribe can do!', WPSB_LOCAL) . '</a></p>';
    }
    // calculate image url
    $images = explode(",", WPSB_AVAILABLE_IMAGES);
    if (!in_array($img, $images)) {
      $img = $images[$options[WPSB_DEFAULT_IMAGE_NAME]];
      if (!$img) { $img = WPSB_DEFAULT_IMAGE; }
    }
    $imageurl = plugins_url(plugin_basename(dirname(__FILE__) . '/images/' . $img . '.png'));
    $imagedata = getimagesize($imageurl);
    $output = '<div id="scribe-box"' . ($rounded ? ' class="wpsb-rounded-corners"' : '') . '>';
    $output .= '<h3>' . __('Get More Traffic and Leads With Less Time and Hassle', WPSB_LOCAL) . '</h3>';
    $output .= '<a' . ($opennewwindow ? ' onclick="window.open(this.href); return false;" onkeypress="window.open(this.href); return false;" ' : ' ') . ($nofollow ? ' rel="nofollow" ' : ' ') . 'href="' . $affiliate_url . '">';
    $output .= '<img class="alignright" src="' . $imageurl . '" alt="' . __('Scribe', WPSB_LOCAL) . '" title="' . __('Scribe', WPSB_LOCAL) . '" width="' . $imagedata[0] . '" height="' . $imagedata[1] . '" /></a>';
    $output .= do_shortcode($text) . '</div>';
  } else { // plugin disabled
    $output = '<!-- ' . WPSB_PLUGIN_NAME . ': plugin is disabled. Either you did not pass a necessary setting to the plugin, or did not configure a default. Check Settings page. -->';
  }
  if ($enabled) {
    if ($show) {
      echo $output;
    } else {
      return $output;
    }
  }
} // end shortcode function
// auto insert at end of posts?
add_action('the_content', 'wpsb_insert_premise_box');
function wpsb_insert_premise_box($content) {
  if (is_single()) {
    $options = wpsb_getpluginoptions();
    if ($options[WPSB_DEFAULT_AUTO_INSERT_NAME]) {
      $content .= scribe_aff_box($options);
    }
  }
  return $content;
}
// show admin messages to plugin user
add_action('admin_notices', 'wpsb_showAdminMessages');
function wpsb_showAdminMessages() {
  // http://wptheming.com/2011/08/admin-notices-in-wordpress/
  global $pagenow;
  if (current_user_can('manage_options')) { // user has privilege
    if ($pagenow == 'options-general.php') {
			if ($_GET['page'] == WPSB_SLUG) { // on WP Scribe Box settings page
        $options = wpsb_getpluginoptions();
				if ($options != false) {
					$enabled = $options[WPSB_DEFAULT_ENABLED_NAME];
					$affiliate_url = $options[WPSB_DEFAULT_URL_NAME];
					if (!$enabled) {
						echo '<div id="message" class="error">' . WPSB_PLUGIN_NAME . ' ' . __('is currently disabled.', WPSB_LOCAL) . '</div>';
					}
					if (($affiliate_url === WPSB_DEFAULT_URL) || ($affiliate_url === false)) {
						echo '<div id="message" class="updated">' . __('WARNING: Affiliate URL missing. Please enter it below, or pass it to the shortcode or function, otherwise the plugin won\'t do anything.', WPSB_LOCAL) . '</div>';
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
  if (current_user_can('manage_options')) { // user has privilege
    if ($pagenow == 'options-general.php') {
      if ($_GET['page'] == WPSB_SLUG) { // we are on settings page
        wpsb_admin_styles();
      }
    }
  }
}
// http://bavotasan.com/2009/a-settings-link-for-your-wordpress-plugins/
// Add settings link on plugin page
add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'wp_scribe_box_plugin_settings_link' );
function wp_scribe_box_plugin_settings_link($links) { 
  $settings_link = '<a href="options-general.php?page=wp-scribe-box">' . __('Settings', WPSB_LOCAL) . '</a>'; 
  array_unshift($links, $settings_link); 
  return $links; 
}
// http://wpengineer.com/1295/meta-links-for-wordpress-plugins/
add_filter('plugin_row_meta', 'wpsb_meta_links', 10, 2);
function wpsb_meta_links($links, $file) {
  $plugin = plugin_basename(__FILE__);
  // create link
  if ($file == $plugin) {
    $links = array_merge($links,
      array(
        '<a href="http://wordpress.org/support/plugin/' . WPSB_SLUG . '">' . __('Support', WPSB_LOCAL) . '</a>',
        '<a href="http://wordpress.org/extend/plugins/' . WPSB_SLUG . '/">' . __('Documentation', WPSB_LOCAL) . '</a>',
        '<a href="http://wordpress.org/plugins/' . WPSB_SLUG . '/faq/">' . __('FAQ', WPSB_LOCAL) . '</a>'
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
    plugins_url(plugin_basename(dirname(__FILE__)) . '/css/admin.css'),
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
    plugins_url(plugin_basename(dirname(__FILE__)) . '/css/wp-scribe-box.css'), 
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
    plugins_url(plugin_basename(dirname(__FILE__)) . '/js/editor_button.js'), 
    array('quicktags'), 
    WPSB_VERSION . "_" . date('njYHis', filemtime(dirname(__FILE__) . '/js/editor_button.js')),
    true);
}
// when plugin is activated, create options array and populate with defaults
register_activation_hook(__FILE__, 'wpsb_activate');
function wpsb_activate() {
  $options = wpsb_getpluginoptions();
  update_option(WPSB_OPTION, $options);
}
// generic function that returns plugin options from DB
// if option does not exist, returns plugin defaults
function wpsb_getpluginoptions() {
  return get_option(WPSB_OPTION, array(WPSB_DEFAULT_ENABLED_NAME => WPSB_DEFAULT_ENABLED, WPSB_DEFAULT_URL_NAME => WPSB_DEFAULT_URL, WPSB_DEFAULT_ROUNDED_NAME => WPSB_ROUNDED, WPSB_DEFAULT_NOFOLLOW_NAME => WPSB_NOFOLLOW, WPSB_DEFAULT_IMAGE_NAME => WPSB_DEFAULT_IMAGE, WPSB_DEFAULT_AUTO_INSERT_NAME => WPSB_DEFAULT_AUTO_INSERT, WPSB_DEFAULT_NEWWINDOW_NAME => WPSB_DEFAULT_NEWWINDOW));
}
// function to return shortcode defaults
function wpsb_shortcode_defaults() {
  return array(
    WPSB_DEFAULT_URL_NAME => WPSB_DEFAULT_URL, 
    WPSB_DEFAULT_ROUNDED_NAME => WPSB_ROUNDED, 
    WPSB_DEFAULT_NOFOLLOW_NAME => WPSB_NOFOLLOW, 
    WPSB_DEFAULT_IMAGE_NAME => WPSB_DEFAULT_IMAGE, 
    WPSB_DEFAULT_NEWWINDOW_NAME => WPSB_DEFAULT_NEWWINDOW, 
    WPSB_DEFAULT_SHOW_NAME => WPSB_DEFAULT_SHOW
    );
}
// function to return parameter status (required or not)
function wpsb_required_parameters() {
  return array(
    'true',
    'false',
    'false',
    'false',
    'false',
    'false'
  );
}
?>
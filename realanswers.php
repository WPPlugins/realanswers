<?php
/**
 * Plugin Name: RealAnswers Answers
 * Plugin URI:
 * Description: A Wordpress Plug-in which can get questions from and post questions to RealtyBaron's RealAnswers API.
 * Author: Denzel Chia for RealtyBaron
 * Author URI:
 * Version: 2.2.0-rc3
 */
//include page class for creating questions and answers page (fake page, not from database)
include (WP_PLUGIN_DIR . '/realanswers/realanswers_create_page_class.php');
//include rest api class
include (WP_PLUGIN_DIR . '/realanswers/realanswers_rest_api.php');
//include admin page which includes some admin functions
include (WP_PLUGIN_DIR . '/realanswers/realanswers_admin.php');
//include sidebar widget
include (WP_PLUGIN_DIR . '/realanswers/realanswers_sidebar_widget.php');
//include functions
include (WP_PLUGIN_DIR . '/realanswers/realanswers_functions.php');
//include recaptcha library
//include (WP_PLUGIN_DIR . '/realanswers/recaptchalib.php');
//setup uninstall hook to remove all options
function realanswers_uninstall_options() {
  delete_option('real_apikey');
  delete_option('real_max_results');
  delete_option('real_custom_css');
  delete_option('real_captcha_public');
  delete_option('real_captcha_private');
  delete_option('real_location_count');
  delete_option('real_location_value');
}

register_uninstall_hook(__FILE__, 'realanswers_uninstall_options');
//function to get current page url to be used for
//condition check in the below function realanswers_admin_warning()
function realanswers_current_page_url() {
  $pageURL = (@$_SERVER["HTTPS"] == "on") ? "https://" : "http://";
  if ($_SERVER["SERVER_PORT"] != "80") {
    $pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
  }
  else
  {
    $pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
  }
  return $pageURL;
}

//admin warnings notice if any of the required elements missing
function realanswers_admin_warning() {
  //setup admin url to realanswers settings page
  $realanswers_settings_url = admin_url() . "options-general.php?page=realanswers_admin.php";
  //setup url to widgets.php
  $realanswers_wp_widget_url = admin_url() . "widgets.php";
  //setup empty global message variable to be used in callback function in admin_notices hook.
  global $realanswers_admin_warning_message;
  $realanswers_admin_warning_message = '';
  global $realanswers_widget_warning_message;
  $realanswers_widget_warning_message = '';
  global $realanswers_setting_warning_message;
  $realanswers_setting_warning_message = '';
  //check whether widget deployed!
  if (!is_active_widget($callback = false, $widget_id = false, $id_base = 'realanswers', $skip_inactive = true)) {
    $realanswers_widget_warning_message = "<li>Please <a href='$realanswers_wp_widget_url'>visit the Widgets Page</a> to install the plugin's sidebar widget.</li>";
  }
  //check empty api key
  $check_api_key = get_option('real_apikey');
  if (empty($check_api_key)) {
    $realanswers_admin_warning_message .= "<li>Please enter an API Key or request one using the New Account Registration Form.</li>";
    $realanswers_setting_warning_message = "<li>Please <a href='$realanswers_settings_url'>visit the Settings Page</a> to configure the plugin.</li>";
  }
  //check at least one location value has been entered
  $check_real_location_value = get_option('real_location_value');
  if (empty($check_real_location_value)) {
    $realanswers_admin_warning_message .= "<li>Please enter at least one location value, in RealAnswers Settings Page.</li>";
    $realanswers_setting_warning_message = "<li>Please <a href='$realanswers_settings_url'>visit the Settings Page</a> to configure the plugin.</li>";
  }
  function realanswers_warning() {
    //setup admin url to realanswers settings page
    $realanswers_settings_url = admin_url() . "options-general.php?page=realanswers_admin.php";
    //setup url to widgets.php
    $realanswers_wp_widget_url = admin_url() . "widgets.php";
    //setup current url
    $current_page_url = realanswers_current_page_url();
    if ($current_page_url == $realanswers_settings_url) {
      // retrieve error message if any, from global variable
      global $realanswers_admin_warning_message;
      if (!empty($realanswers_admin_warning_message)) {
        echo "<div id='realanswers-warning' class='updated fade'>";
        echo "<p><strong>RealAnswers WordPress Plugin is almost ready.";
        echo " The following needs your attention!</strong></p>";
        echo "<ol>";
        echo $realanswers_admin_warning_message;
        echo "</ol>";
        echo "</div>";
      }
    } elseif ($current_page_url == $realanswers_wp_widget_url) {
      // retrieve error message if any, from global variable
      global $realanswers_widget_warning_message;
      if (!empty($realanswers_widget_warning_message)) {
        echo "<div id='realanswers-warning' class='updated fade'>";
        echo "<p><strong>Click the <em>RealAnswers Widget</em> in <em>Available Widgets</em> and drag to <em>Sidebar 1</em></strong>.</p>";
        echo "</div>";
      }
    } else {
      // retrieve error message if any, from global variable
      global $realanswers_widget_warning_message;
      global $realanswers_setting_warning_message;
      if (!empty($realanswers_widget_warning_message) || !empty($realanswers_setting_warning_message)) {
        echo "<div id='realanswers-warning' class='updated fade'>";
        echo "<p><strong>RealAnswers WordPress Plugin is almost ready.";
        echo " The following needs your attention!</strong></p>";
        echo "<ol>";
        echo $realanswers_setting_warning_message;
        echo $realanswers_widget_warning_message;
        echo "</ol>";
        echo "</div>";
      }
    }
    //end else
  }

  //end function realanswers_warning
  add_action('admin_notices', 'realanswers_warning');
}

add_action('init', 'realanswers_admin_warning');
?>
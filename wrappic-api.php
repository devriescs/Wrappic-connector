<?php

/**
* Plugin Name: Wrappic connect
* Plugin URI: http://www.wrappic.nl/wrappic-connect
* Description: Connect your Wordpress website with the Wrappic dashboard
* Version: 1.0
* Author: Maarten de Vries
* Author URI: http://www.wrappic.nl
*/

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
require_once 'vendor/autoload.php';

// Register actions
// Settings page
if ( is_admin() ){
  add_action( 'admin_menu', 'create_wrappic_settings_page' );
}

add_action( 'admin_post_update_wrappic_settings', 'wrappic_handle_save' );
add_action( 'publish_post', 'post_published_notification', 10, 2 );


function post_published_notification( $ID, $post ) {
    
    // Return when there is no api token set
    if(!strlen(get_option('api_token')) > 0) {
        return;
    }
    
    // Get the post information
    $title = $post->post_title;
   
    $client = new GuzzleHttp\Client();
    $api_token = get_option('api_token');
    $response = $client->post('http://www.wrappic.dev/api/v1/post',
            array(
                'form_params' => [
                    'api_token' => $api_token,
                    'title' => $title,
                    ]
                )
        );
}

function create_wrappic_settings_page() {
    add_submenu_page( "options-general.php",  // Which menu parent
                  "Wrappic api",            // Page title
                  "Wrappic api",            // Menu title
                  "manage_options",       // Minimum capability (manage_options is an easy way to target administrators)
                  "wrappic-api",            // Menu slug
                  "wrappic_plugin_options"     // Callback that prints the markup
               );
}

// Print the markup for the page
function wrappic_plugin_options() {
    if ( !current_user_can( "manage_options" ) )  {
        wp_die( __( "You do not have sufficient permissions to access this page." ) );
    }
   
    if ( isset($_GET['status']) && $_GET['status']=='success') { 
    ?>
    <div id="message" class="updated notice is-dismissible">
        <p><?php _e("Settings updated!", "wrappic-api"); ?></p>
        <button type="button" class="notice-dismiss">
            <span class="screen-reader-text"><?php _e("Dismiss this notice.", "wrappic-api"); ?></span>
        </button>
    </div>
    <?php
    }
    
    ?>
    
    <form method="post" action="<?php echo admin_url( 'admin-post.php'); ?>">

   <input type="hidden" name="action" value="update_wrappic_settings" />

   <h3><?php _e("Wrappi api settings", "wrappic-api"); ?></h3>
   <p>
   <label><?php _e("Api token:", "wrappic-api"); ?></label>
   <input class="" type="text" name="api_token" size="64" value="<?php echo get_option('api_token'); ?>" />
   </p>

   <input class="button button-primary" type="submit" value="<?php _e("Opslaan", "wrappic-api"); ?>" />

    </form>
    <?php
}

function wrappic_handle_save() {
   // Get the options that were sent
   $api_token = (!empty($_POST["api_token"])) ? $_POST["api_token"] : NULL;

   // Update the values
   update_option( "api_token", $api_token, TRUE );

   // Redirect back to settings page
   $redirect_url = get_bloginfo("url") . "/wp-admin/options-general.php?page=wrappic-api&status=success";
   header("Location: ".$redirect_url);
   exit;
}
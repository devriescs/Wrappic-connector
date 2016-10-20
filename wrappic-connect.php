<?php

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

/**
* Plugin Name: Wrappic connect
* Plugin URI: http://www.wrappic.nl/wrappic-connect
* Description: Connect your Wordpress website with the Wrappic dashboard
* Version: 1.0
* Author: Maarten de Vries
* Author URI: http://www.wrappic.nl
*/

function post_published_notification( $ID, $post ) {
    // Call the Wrappic api
    $api_token = get_option('api_token');
    $url = 'http://wrappic.nl/api/v1/update';
    $args = array(
        'timeout'     => 5,
        'redirection' => 5,
        'httpversion' => '1.0',
        'user-agent'  => 'WordPress/' . $wp_version . '; ' . home_url(),
        'blocking'    => true,
        'headers'     => array(),
        'cookies'     => array(),
        'body'        => array('api_token' => $api_token),
        'compress'    => false,
        'decompress'  => true,
        'sslverify'   => false,
        'stream'      => false,
        'filename'    => null
    );
    $response = wp_remote_get( $url, $args );
}

add_action( 'publish_post', 'post_published_notification', 10, 2 );

// Settings page
if ( is_admin() ){
  add_action( 'admin_menu', 'create_wrappic_settings_page' );
  add_action( 'admin_init', 'register_settings' );
}

function create_wrappic_settings_page() {
    $page_title = 'Wrappic connect instellingen';
    $menu_title = 'Wrappic connect instellingen';
    $capability = 'edit_posts';
    $menu_slug = 'wrappic_settings';
    $function = 'wrappic_settings_page_display';
    $icon_url = '';

    add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function, $icon_url, null );
    
    // Registers settings
	add_action( 'admin_init', 'register_settings' );
}

function register_settings() {
    register_setting( 'wrappic-settings-group', 'api_token' );
}

function wrappic_settings_page_display() {
?>
<div class="wrap">
<h1>Wrappic connect instellingen</h1>

<form method="post" action="options.php">
    <?php settings_fields( 'wrappic-settings-group' ); ?>
    <?php do_settings_sections( 'wrappic-settings-group' ); ?>
    <table class="form-table">
        <tr valign="top">
        <th scope="row">API token</th>
        <td><input type="text" name="api_token" value="<?php echo esc_attr( get_option('api_token') ); ?>" /></td>
        </tr>
    </table>
    
    <?php submit_button(); ?>

</form>
</div>
<?php } ?>
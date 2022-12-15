<?php
 /*
   Plugin Name: Wootric NPS for Wordpress
   Plugin URI: https://www.gws-technologies.com/
   description: DIsplay Wootric NPS code to logged-in users
   Version: 1.0
   Author: GWS Technologies
   Author URI: https://www.gws-technologies.com/
   */

   
/**
 * Die if accessed directly
 */
defined( 'ABSPATH' ) or die( __('You can not access this file directly!', 'states-cities-and-places-for-woocommerce') );


function gws_display_wootric__nps_code() {
    if(is_user_logged_in()){
        $current_user = wp_get_current_user();
        $current_user_email = $current_user->user_email;
        $wootric_nps_account_token = get_option('wootric_nps_account_token');
        if(!empty($current_user_email) && !empty($wootric_nps_account_token)){
            $udata = get_userdata( $current_user->ID );
            $registered = $udata->user_registered;
            ?>
            <!-- begin Wootric code -->
            <script type="text/javascript">
            // window.wootric_survey_immediately = true; // Shows survey immediately for testing purposes. TODO: Comment out for production.
            window.wootricSettings = {
                email: '<?php echo $current_user_email; ?>',
                // external_id: 'abc123', // TODO: Reference field for external integrations only. Send it along with email. OPTIONAL
                created_at: <?php echo strtotime( $registered );  ?>,
                account_token: '<?php echo $wootric_nps_account_token; ?>'
            };
            </script>
            <script type="text/javascript" src="https://cdn.wootric.com/wootric-sdk.js"></script>
            <script type="text/javascript">
            // This loads the Wootric survey
            window.wootric('run');
            </script>
            <!-- end Wootric code -->
            <?php
        }
    }
}
add_action( 'wp_footer', 'gws_display_wootric__nps_code' );

function wootric_nps_register_settings() {
    add_option( 'wootric_nps_account_token', '');
    register_setting( 'wootric_nps_options_group', 'wootric_nps_account_token', 'wootric_nps_callback' );
 }
 add_action( 'admin_init', 'wootric_nps_register_settings' );

 function wootric_nps_register_options_page() {
    add_options_page('Wootric NPS options', 'Wootric NPS option', 'manage_options', 'wootric_nps', 'wootric_nps_options_page');
  }
add_action('admin_menu', 'wootric_nps_register_options_page');

function wootric_nps_options_page(){
    ?>
        <div>
        <?php screen_icon(); ?>
        <h2>Wootric NPS Settings</h2>
        <form method="post" action="options.php">
            <?php settings_fields( 'wootric_nps_options_group' ); ?>
            <table>
                <tr valign="top">
                    <th scope="row">
                        <label for="wootric_nps_account_token">
                            Your unique Account Token (e.g  NPS-12345abcdef)
                            <br /><a href="https://app.wootric.com/account_settings/edit?#!/account" target="_blank">Get it from your Wootric Account</a></label></th>
                    <td><input type="text" id="wootric_nps_account_token" name="wootric_nps_account_token" value="<?php echo get_option('wootric_nps_account_token'); ?>" /></td>
                </tr>
            </table>
            <?php  submit_button(); ?>
        </form>
        </div>
        <?php
    }
    ?>

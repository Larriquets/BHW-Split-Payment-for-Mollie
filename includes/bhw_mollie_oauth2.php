<?php

/**
 * Function to handle Mollie OAuth2 authentication during vendor onboarding process.
 * Manages and controls the OAuth2 authentication flow, initiates sessions if necessary, and logs debugging information.
 */

require_once(plugin_dir_path(__FILE__) . 'BHW-Class-MollieOAuth2Provider.php');


function bhw_mollie_oauth2() {
    $logger = wc_get_logger();
    $context = array('source' => 'bhw-mollie_oauth2');
    $logger->debug( 'BHW - onboarding-vendor - OK', $context);
    if (!session_id()) {
        if (function_exists('session_start')) {
            session_start();
            $logger->debug( 'BHW - session_start- OK', $context);
        } else {
            $logger->debug( 'BHW - session_start no está disponible.', $context);
        }
    } 

    $user_id = get_current_user_id();
    $user_meta = get_user_meta($user_id);
    $logger->debug( 'BHW - get_current_user_id : '.$user_id, $context);
    $logger->debug('BHW - User Data: ' . print_r($user_meta, true), $context);

    if (is_page_template('mollie-onboarding-vendor.php')) {
        
        $clientId    = get_option( 'mollie-payments-for-bluehouse_connect_client_id' );
        $clientSecret = get_option( 'mollie-payments-for-bluehouse_connect_customer_secret' );
        $redirectUri  = get_option( 'mollie-payments-for-bluehouse_connect_redirect_url' );

        $provider = new \Mollie\OAuth2\Client\Provider\MollieOAuth2Provider( $clientId,$clientSecret,  $redirectUri );
        $logger->debug( 'BHW - provider - v1'. print_r($provider,true), $context);

    }

}
add_action('template_redirect', 'bhw_mollie_oauth2');

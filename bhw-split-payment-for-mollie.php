<?php
/**
 * Plugin Name: BHW Split Payment for Mollie
 * Description: A plugin to split payments for Mollie payments in WooCommerce.
 * Version: 1.1
 * Author: BHW
 */


if (!defined('ABSPATH')) {
    exit;
}

require_once( __DIR__ . '/vendor/autoload.php');
require_once(plugin_dir_path(__FILE__) . 'admin/bhw-mollie-connect-settings.php');


// Include OAuth2 Mollie authentication for Vendor onboarding.
require_once(plugin_dir_path(__FILE__) . 'includes/bhw_mollie_oauth2.php');

// Include dynamic gateway filters for splitting Mollie payments in WooCommerce.
require_once(plugin_dir_path(__FILE__) . 'includes/bhw-dynamic-gateway-filters.php');















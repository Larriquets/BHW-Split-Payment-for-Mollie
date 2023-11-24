<?php
/**
 * WooCommerce settings and functions for Mollie Connect.
 *
 * This code defines settings fields and functions related to Mollie Connect
 * within WooCommerce, allowing you to configure payment settings.
 */
function add_mollie_connect_tab($tabs) {
    $tabs['mollie_connect_section'] = __('Mollie Connect Settings', 'mollie-payments-for-bluehouse');
    return $tabs;
}
add_filter('woocommerce_settings_tabs_array', 'add_mollie_connect_tab', 50);


function add_mollie_connect_settings_fields() {
    woocommerce_admin_fields([
        'mollie_connect_section' => [
            'title' => __('Mollie Connect Settings', 'mollie-payments-for-bluehouse'),
            'type' => 'title',
            'id' => 'mollie_connect_section',
        ],
        'mollie-payments-for-bluehouse_connect_client_id' => [
            'title' => __('Mollie Connect Client ID', 'mollie-payments-for-bluehouse'),
            'type' => 'text',
            'default' => '',
            'placeholder' => __('Client ID should start with app_', 'mollie-payments-for-bluehouse'),
            'id' => 'mollie-payments-for-bluehouse_connect_client_id',
        ],
        'mollie-payments-for-bluehouse_connect_customer_secret' => [
            'title' => __('Mollie Connect Customer Secret', 'mollie-payments-for-bluehouse'),
            'type' => 'text',
            'default' => '',
            'placeholder' => __('Mollie Connect App Customer Secret', 'mollie-payments-for-bluehouse'),
            'id' => 'mollie-payments-for-bluehouse_connect_customer_secret',
        ],
        'mollie-payments-for-bluehouse_connect_redirect_url' => [
            'title' => __('Mollie Connect Redirect URL', 'mollie-payments-for-bluehouse'),
            'type' => 'text',
            'default' => '',
            'placeholder' => __('Add the same URL configured in your Mollie Connect APP', 'mollie-payments-for-bluehouse'),
            'id' => 'mollie-payments-for-bluehouse_connect_redirect_url',
        ],
        'payment_release_days' => [
            'title' => __('Payment release days', 'mollie-payments-for-bluehouse'),
            'type' => 'number',
            'default' => '30',
            'custom_attributes' => ['step' => '1', 'min' => '0', 'max' => '180'],
            'placeholder' => __('Numbers of days (0 release immediately)', 'mollie-payments-for-bluehouse'),
            'desc' => 'Numbers of days to add to the purchase date in order to release the vendor\'s payment. 0 for release immediately.',
            'desc_tip' => true,
            'id' => 'payment_release_days',
        ],
        'bluehouse_fee_percent' => [
            'title' => __('Bluehouse Fee (%)', 'mollie-payments-for-bluehouse'),
            'type' => 'number',
            'default' => '10',
            'custom_attributes' => ['step' => '1', 'min' => '0', 'max' => '100'],
            'placeholder' => __('Percent', 'mollie-payments-for-bluehouse'),
            'desc' => '- Percent of fee for vendors, the amount goes to Bluehouse account.',
            'desc_tip' => true,
            'id' => 'bluehouse_fee_percent',
        ],

        'mollie_connect_section_end' => [
            'type' => 'sectionend',
            'id' => 'mollie_connect_section_end',
        ],
    ]);
}

function render_mollie_connect_settings() {
    woocommerce_admin_fields([
        'mollie_connect_section'
    ]);
}

add_action('woocommerce_sections_mollie_connect_section', 'add_mollie_connect_settings_fields');
add_action('woocommerce_sections_mollie_connect_section', 'render_mollie_connect_settings');



function save_mollie_connect_settings() {

        $connect_client_id = sanitize_text_field($_POST['mollie-payments-for-bluehouse_connect_client_id']);
        update_option('mollie-payments-for-bluehouse_connect_client_id', $connect_client_id);

        $connect_customer_secret = sanitize_text_field($_POST['mollie-payments-for-bluehouse_connect_customer_secret']);
        update_option('mollie-payments-for-bluehouse_connect_customer_secret', $connect_customer_secret);

        $connect_redirect_url = sanitize_text_field($_POST['mollie-payments-for-bluehouse_connect_redirect_url']);
        update_option('mollie-payments-for-bluehouse_connect_redirect_url', $connect_redirect_url);

        $payment_release_days = sanitize_text_field($_POST['payment_release_days']);
        update_option('payment_release_days', $payment_release_days);

        $bluehouse_fee_percent = sanitize_text_field($_POST['bluehouse_fee_percent']);
        update_option('bluehouse_fee_percent', $bluehouse_fee_percent);

        wp_redirect(admin_url('admin.php?page=wc-settings&tab=mollie_connect_section'));
        exit;
}
add_action('woocommerce_update_options_mollie_connect_section', 'save_mollie_connect_settings');

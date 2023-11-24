<?php
/**
 * Function to handle split payments for Mollie payments in WooCommerce.
 * It processes payment data, retrieves routing information, logs debugging details, and returns the modified data.
 */

require_once(plugin_dir_path(__FILE__) . 'BHW-Class-Mollie_SplitOrder.php');

function bhw_split_payments($data, $filter_name) {

    $logger = wc_get_logger();
    $context = array('source' => 'bhw-split-payment-for-mollie');
    if (isset($data['metadata']['order_id'])) {
        $order_id = $data['metadata']['order_id'];
        $order = wc_get_order($order_id);

        if ($order) {
            $routing = Mollie_SplitOrder::getOrderRouting($order);
            if (!empty($routing)) {
                $data['routing'] =  $routing;
                $logger->debug( 'BHW ROUTING : ' . print_r($data,true), $context );
                $logger->debug($mensaje, $context );

            }
        }
    }


    $mensaje = 'BHW - Filter Return : ' .  json_encode($data);

    $logger->debug( 'BHW Filter ORDER' . print_r($order,true), $context );
    $mensaje .= ' | Filter Name: ' . $filter_name;
    if (isset($order)) {
        $mensaje .= ' | Order: ' .  json_encode($order);
    }


    $logger->debug($mensaje, $context );

    return $data;
}

function create_dynamic_filters_for_gateways() {
    $available_gateways = WC()->payment_gateways->get_available_payment_gateways();

    if (!empty($available_gateways)) {
        foreach ($available_gateways as $gateway_id => $gateway) {
            $filter_name = 'woocommerce_' . $gateway_id . '_args';
            add_filter($filter_name, function ($args) use ($filter_name) {
                return bhw_split_payments($args, $filter_name);
            }, 10, 1);
        }
    }
}

add_action('init', 'create_dynamic_filters_for_gateways');

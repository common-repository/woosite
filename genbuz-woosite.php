<?php
/**
 * Plugin Name: WooSite
 * Plugin URI: https://www.genbuz.com/
 * Description: WooSite is used to connect to a parent site that has the "WooMulti" plugin installed.
 * Version: 1.7
 * Author: GenBuz
 * Author URI: https://www.genbuz.com
 * License: GPLv2 or later
 * Text Domain: woosite
 */
 


class GBWS_Register
{
  
    public function __construct(){
      
        // nothing yet
    }
    
    public function gbws_register()
    {
        /**
         * Add the custom route endpoints.
         */
        add_action( 'rest_api_init', array( $this, 'gbws_routes' ) );
        
        
        /**
         * show tracking details if set in the my-account > orders > view site section.
         */
        add_action( 'woocommerce_order_details_after_order_table', array($this, 'gbws_tracking_field_order' ), 10, 1 );


        /**
         * show tracking details in the customer email if set.
         */
        add_action( 'woocommerce_email_after_order_table', array($this, 'gbws_tracking_field_email'), 15, 4 );
    }

    
    /**
     * setup our custom routes
     */
    public function gbws_routes()
    {
    
        register_rest_route( 'gbwm/v1', '/UpdateBillingAddress', array(
            'methods' => 'POST',
            'callback' => array( $this, 'gbws_update_billing_address')
        ) );
    
        register_rest_route( 'gbwm/v1', '/UpdateShippingAddress', array(
            'methods' => 'POST',
            'callback' => array( $this, 'gbws_update_shipping_address')
        ) );

    }





    /**
     * this is the UpdateBillingAddress custom endpoint
     */
    public function gbws_update_billing_address( \WP_REST_Request $request )
    {
        // get data
        $siteID     = $request['siteID'];
        $orderID    = $request['orderID'];
    
        // if there is no siteID or orderID
        if ( empty( $siteID ) || empty( $orderID ) ) {
    
            // now return
            return rest_ensure_response( __( 'Failed','woosite' ) );
    
        }else{// if siteID and orderID exist
    
            // update the billing address for this order
            update_post_meta( $orderID, '_billing_first_name', $request['first_name'] );
            update_post_meta( $orderID, '_billing_last_name', $request['last_name'] );
            update_post_meta( $orderID, '_billing_company', $request['company'] );
            update_post_meta( $orderID, '_billing_address_1', $request['address_1'] );
            update_post_meta( $orderID, '_billing_address_2', $request['address_2'] );
            update_post_meta( $orderID, '_billing_city', $request['city'] );
            update_post_meta( $orderID, '_billing_state', $request['state'] );
            update_post_meta( $orderID, '_billing_postcode', $request['postcode'] );
            update_post_meta( $orderID, '_billing_country', $request['country'] );
    
            // now return
            return rest_ensure_response( __( 'Success','woosite' ) );
        }
    }// end gbws_update_billing_address funtion





    /**
     * this is the UpdateShippingAddress custom endpoint
     */
    public function gbws_update_shipping_address( \WP_REST_Request $request )
    {
        // get data
        $siteID     = $request['siteID'];
        $orderID    = $request['orderID'];
    
        // if there is no siteID or orderID
        if ( empty( $siteID ) || empty( $orderID ) ) {
    
            // now return
            return rest_ensure_response( __( 'Failed','woosite' ) );
    
        }else{// if siteID and orderID exist
    
            // update the shipping address for this order
            update_post_meta( $orderID, '_shipping_first_name', $request['first_name'] );
            update_post_meta( $orderID, '_shipping_last_name', $request['last_name'] );
            update_post_meta( $orderID, '_shipping_company', $request['company'] );
            update_post_meta( $orderID, '_shipping_address_1', $request['address_1'] );
            update_post_meta( $orderID, '_shipping_address_2', $request['address_2'] );
            update_post_meta( $orderID, '_shipping_city', $request['city'] );
            update_post_meta( $orderID, '_shipping_state', $request['state'] );
            update_post_meta( $orderID, '_shipping_postcode', $request['postcode'] );
            update_post_meta( $orderID, '_shipping_country', $request['country'] );
    
            // now return
            return rest_ensure_response( __( 'Success','woosite' ) );
        }
    }// end gbws_update_shipping_address funtion
    
    
    

    public function gbws_tracking_field_order($order){

        @ $tracking_number = get_post_meta($order->id , 'gbwm_tracking_number' , true );

        @ $tracking_url = get_post_meta($order->id , 'gbwm_tracking_url' , true );

        if(! empty($tracking_number) && ! empty($tracking_url)) {

        ?>

        <h2 class="woocommerce-order-details__title"><?php _e( 'Tracking Information', 'woosite' );?></h2>

        <table class="woocommerce-table woocommerce-table--order-details shop_table order_details">
            <tbody>
                <tr class="woocommerce-table__line-item order_item">

                    <td class="woocommerce-table__tracking-number tracking-number" width="50%">
                        <strong><?php _e( 'Tracking Number', 'woosite' );?></strong>
                    </td>
                    
                    <td class="woocommerce-table__tracking-number tracking-number" width="50%">
                        <strong><?php echo $tracking_number;?></strong>
                    </td>
                    
                </tr>
                
                <tr class="woocommerce-table__line-item order_item">

                    <td class="woocommerce-table__tracking-url tracking-url" width="50%">
                        <strong><?php _e( 'Tracking Link', 'woosite' );?></strong>
                    </td>
                    
                    <td class="woocommerce-table__tracking-url tracking-url" width="50%">
                        <strong><a href="<?php echo $tracking_url;?>" target="_blank"><?php echo $tracking_url;?></a></strong>
                    </td>
                    
                </tr>
            </tbody>
        </table>
    <?php
        }
    }// end gbws_tracking_field_order function

    

    public function gbws_tracking_field_email( $order, $sent_to_admin, $plain_text, $email ) {

        @ $tracking_number = get_post_meta($order->id , 'gbwm_tracking_number' , true );
        @ $tracking_url = get_post_meta($order->id , 'gbwm_tracking_url' , true );

        if(! empty($tracking_number) && ! empty($tracking_url)) {
        ?>

        <h2 style="color: #96588a; display: block; font-family: &quot;Helvetica Neue&quot;, Helvetica, Roboto, Arial, sans-serif; font-size: 18px; font-weight: bold; line-height: 130%; margin: 0 0 18px; text-align: left;"><?php _e( 'Tracking Information', 'woosite' );?></h2>

        <table id="addresses" cellspacing="0" cellpadding="0" style="width: 100%; vertical-align: top; margin-bottom: 40px; padding: 0;" border="0">
            <tbody>
                <tr>
                    <td class="td" style="text-align: left; border-top-width: 4px; color: #636363; border: 1px solid #e5e5e5; padding: 12px;" width="50%"><?php _e( 'Tracking Number', 'woosite' );?></td>
                    
                    <td class="td" style="text-align: left; border-top-width: 4px; color: #636363; border: 1px solid #e5e5e5; padding: 12px;" width="50%"><?php echo $tracking_number;?></td>
                </tr>
                <tr>
                    <td class="td" style="text-align: left; border-top-width: 4px; color: #636363; border: 1px solid #e5e5e5; padding: 12px;" width="50%"><?php _e( 'Tracking Link', 'woosite' );?></td>
                    
                    <td class="td" style="text-align: left; border-top-width: 4px; color: #636363; border: 1px solid #e5e5e5; padding: 12px;" width="50%"><a href="<?php echo $tracking_url;?>" target="_blank"><?php echo $tracking_url;?></a></td>
                </tr>
            </tbody>
        </table>

        <?php

        }

    }// end gbws_tracking_field_email function

}// end class

//start the ball rolling
$woosite = new GBWS_Register;
$woosite->gbws_register();

?>
<?php 
/**
 * @wordpress-plugin
 * Plugin Name:       Woocommerce extension
 * Plugin URI:        https://www.devnaveed.com
 * Description:       Customizes the WooCommerce  experience
 * Version:           1.0.0
 * Author:            CoSpark
 * Author URI:        https://www.devnaveed.com
 */

/**
 * Add css to woocommerce styling
 */

 function wpse_woocommerce_styling() {
    $file = 'assets/woocommerce-styling.css';
    wp_enqueue_style( 'woo-style', plugin_dir_url( __FILE__ ) . $file, array(), filemtime( plugin_dir_path( __FILE__ ) . $file ) );
    }
  add_action( 'wp_enqueue_scripts', 'wpse_woocommerce_styling' );

  
function custom_register_fields() {?>
  <p class="form-row form-row-first">
  <label for="reg_billing_first_name"><?php _e( 'First name', 'woocommerce' ); ?><span class="required">*</span></label>
  <input type="text" class="input-text" name="billing_first_name" id="reg_billing_first_name" value="<?php if ( ! empty( $_POST['billing_first_name'] ) ) esc_attr_e( $_POST['billing_first_name'] ); ?>" />
  </p>
  <p class="form-row form-row-last">
  <label for="reg_billing_last_name"><?php _e( 'Last name', 'woocommerce' ); ?><span class="required">*</span></label>
  <input type="text" class="input-text" name="billing_last_name" id="reg_billing_last_name" value="<?php if ( ! empty( $_POST['billing_last_name'] ) ) esc_attr_e( $_POST['billing_last_name'] ); ?>" />
  </p>
  <div class="clear"></div>
  <?php
}
add_action( 'woocommerce_register_form_start', 'custom_register_fields' );


/**
* Custom register fields Validating.
*/
function custom_validate_custom_register_fields( $username, $email, $validation_errors ) {
  if ( isset( $_POST['billing_first_name'] ) && empty( $_POST['billing_first_name'] ) ) {
         $validation_errors->add( 'billing_first_name_error', __( 'First name is required!', 'woocommerce' ) );
  }
  if ( isset( $_POST['billing_last_name'] ) && empty( $_POST['billing_last_name'] ) ) {
         $validation_errors->add( 'billing_last_name_error', __( 'Last name is required!.', 'woocommerce' ) );
  }
     return $validation_errors;
}
add_action( 'woocommerce_register_post', 'custom_validate_custom_register_fields', 10, 3 );

/**
* Below code save Custom register fields.
*/
function custom_save_custom_register_fields( $customer_id ) {
  
    if ( isset( $_POST['billing_first_name'] ) ) {
           //First name field which is by default
           update_user_meta( $customer_id, 'first_name', sanitize_text_field( $_POST['billing_first_name'] ) );
           // First name field which is used in WooCommerce
           update_user_meta( $customer_id, 'billing_first_name', sanitize_text_field( $_POST['billing_first_name'] ) );
    }
    if ( isset( $_POST['billing_last_name'] ) ) {
           // Last name field which is by default
           update_user_meta( $customer_id, 'last_name', sanitize_text_field( $_POST['billing_last_name'] ) );
           // Last name field which is used in WooCommerce
           update_user_meta( $customer_id, 'billing_last_name', sanitize_text_field( $_POST['billing_last_name'] ) );
    }
}
add_action( 'woocommerce_created_customer', 'custom_save_custom_register_fields' );





add_action( 'woocommerce_register_form', 'custom_add_registration_privacy_policy', 11 );
 
function custom_add_registration_privacy_policy() {

woocommerce_form_field( 'privacy_policy_reg', array(
 'type'          => 'checkbox',
 'class'         => array('form-row privacy'),
 'label_class'  => array('woocommerce-form__label woocommerce-form__label-for-checkbox checkbox'),
 'input_class'  => array('woocommerce-form__input woocommerce-form__input-checkbox input-checkbox'),
 'required'      => true,
 'label'         => 'I\'ve read and accept the <a href="/privacy-policy">Privacy Policy</a>',
));

}

// Show error if user does not tick
 
add_filter( 'woocommerce_registration_errors', 'custom_validate_privacy_registration', 10, 3 );

function custom_validate_privacy_registration( $errors, $username, $email ) {
if ( ! is_checkout() ) {
  if ( ! (int) isset( $_POST['privacy_policy_reg'] ) ) {
      $errors->add( 'privacy_policy_reg_error', __( 'Privacy Policy consent is required!', 'woocommerce' ) );
  }
}
return $errors;
}

// Rediect user to the Account details tab.
 
function custom_my_account_redirect_to_account_details(){
   if ( is_account_page() && empty( WC()->query->get_current_endpoint() ) ) {
      wp_safe_redirect( wc_get_account_endpoint_url( 'edit-account' ) );
      exit;
   }
}
//add_action( 'template_redirect', 'custom_my_account_redirect_to_account_details' );
 
function bbloomer_my_account_redirect_to_downloads(){
   if ( is_account_page() && empty( WC()->query->get_current_endpoint() ) ) {
      wp_safe_redirect( wc_get_account_endpoint_url( 'edit-account' ) );
      exit;
   }
}
// Remove Tabs from my-account
add_filter( 'woocommerce_account_menu_items', 'custom_remove_my_account_tabs' );
function custom_remove_my_account_tabs( $menu_links ){
	
//unset( $menu_links[ 'dashboard' ] );
  unset( $menu_links[ 'orders' ] );
  unset( $menu_links[ 'downloads' ] );
  unset( $menu_links[ 'edit-address' ] );
	return $menu_links;
	
}

//For adding woocommerce templates in the plugin
add_filter( 'woocommerce_locate_template', 'intercept_wc_template', 10, 3 );
/**
 *
 * @param string $template      Default template file path.
 * @param string $template_name Template file slug.
 * @param string $template_path Template file name.
 *
 * @return string The new Template file path.
 */
function intercept_wc_template( $template, $template_name, $template_path ) {

	if ( 'dashboard.php' === basename( $template ) ) {
		$template = trailingslashit( plugin_dir_path( __FILE__ ) ) . 'woocommerce/myaccount/dashboard.php';
	}

	return $template;

}

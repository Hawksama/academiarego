<?php
/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */
if ( ! defined( 'YITH_WCSC_PATH' ) ) {
	exit( 'Direct access forbidden.' );
}

/**
 * @class      YITH_Stripe_Connect_Gateway
 * @package    Yithemes
 * @since      Version 1.0.0
 * @author     Francisco Javier Mateo <francisco.mateo@yithemes.com>
 */

use \Stripe\PaymentIntent;
use \Stripe\SetupIntent;

if ( ! class_exists( 'YITH_Stripe_Connect_Gateway' ) ) {
	/**
	 * Class YITH_Stripe_Connect_Gateway
	 *
	 * @author Francisco Javier Mateo <francisco.mateo@yithemes.com>
	 */
	class YITH_Stripe_Connect_Gateway extends WC_Payment_Gateway_CC {

		/**
		 * Unique instance of the class
		 *
		 * @param YITH_Stripe_Connect_Gateway
		 */
		protected static $_instance = null;

		/**
		 * The domain of this site used to identifier the website from Stripe.
		 * @var string
		 */
		public $instance_url = '';

		/**
		 * Whether log is enabled or not
		 *
		 * @var bool Whether or not logging is enabled
		 */
		public $log_enabled = false;

		/**
		 * Logger instance
		 *
		 * @var WC_Logger Logger instance
		 */
		public $log = false;

		/**
		 * @var \YITH_Stripe_Connect_API_Handler
		 */
		public $api_handler = null;

		/**
		 * Constructor for the gateway.
		 */
		public function __construct() {
			$this->id                 = YITH_Stripe_Connect::$gateway_id;
			$this->has_fields         = false;
			$this->order_button_text  = apply_filters( 'yith_wcsc_order_button_text', _x( 'Proceed to Stripe Connect', 'Order button text on Stripe Connect Gateway', 'yith-stripe-connect-for-woocommerce' ) );
			$this->method_title       = _x( 'Stripe Connect', 'The Gateway title, no need translation :D', 'yith-stripe-connect-for-woocommerce' );
			$this->method_description = _x( 'Stripe Connect Gateway for WooCommerce', 'Stripe Connect Gateway description', 'yith-stripe-connect-for-woocommerce' );
			$this->instance_url       = preg_replace( '/http(s)?:\/\//', '', site_url() );
			$this->supports           = array(
				'products'
			);

			// Load the settings.
			$this->init_form_fields();
			$this->init_settings();

			// Define user set variables.
			$this->title             = $this->get_option( 'label-title' );
			$this->description       = $this->get_option( 'label-description' );
			$this->description       = ! empty( $this->description ) ? $this->description : __( 'Stripe Connect Gateway', 'yith-stripe-connect-for-woocommerce' );  //@since 1.0.3
			$this->test_live         = 'yes' === $this->get_option( 'test-live', 'no' );
			$this->log_enabled       = 'yes' === $this->get_option( 'log', 'no' );
			$this->public_key        = ( 'yes' == $this->test_live ) ? $this->get_option( 'api-public-test-key' ) : $this->get_option( 'api-public-live-key' ); // Switch the plublic key between test and live mode.
			$this->credit_cards_logo = $this->get_option( 'credit-cards-logo', array() );
			$this->show_name_on_card = $this->get_option( 'show-name-on-card', 'no' );
			$this->save_cards        = $this->get_option( 'save-cards', 'no' );

			if ( 'yes' == $this->save_cards ) {
				$this->supports[] = 'tokenization';
			}

			if ( $this->log_enabled ) {
				$this->log = new WC_Logger();
			}

			add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array(
				$this,
				'process_admin_options'
			) );

			// token hooks - Update token when the customer edit them from My Account Page
			add_filter( 'woocommerce_credit_card_form_fields', array( $this, 'credit_form_add_fields' ), 10, 2 );

			// scripts
			add_action( 'wp_enqueue_scripts', array( $this, 'payment_scripts' ) );
		}

		/**
		 * Load required scripts on Checkout page and wherever the gateway is needed
		 *
		 * @return void
		 */
		public function payment_scripts() {
			global $wp;

			if ( ! $this->is_available() || ! ( is_checkout() || is_wc_endpoint_url( 'add-payment-method' ) ) ) {
				return;
			}

			$debug_enabled   = defined( 'WP_DEBUG' ) ? WP_DEBUG : false;
			$prefix          = ! $debug_enabled ? '.min' : '';
			$js_dependencies = array( 'jquery', 'stripe-js', 'wc-credit-card-form' );

			wp_register_script( 'stripe-js', 'https://js.stripe.com/v3/', array( 'jquery' ), false, true );

			wp_register_script( 'yith-stripe-connect-js', YITH_WCSC_ASSETS_URL . 'js/script-yith-sc-checkout' . $prefix . '.js', $js_dependencies, YITH_WCSC_VERSION, true );

			wp_localize_script( 'yith-stripe-connect-js', 'yith_stripe_connect_info', array(
				'public_key'     => $this->public_key,
				'is_checkout'    => is_checkout(),
				'order'          => isset( $wp->query_vars['order-pay'] ) ? $wp->query_vars['order-pay'] : false,
				'refresh_intent' => wp_create_nonce( 'refresh-intent' ),
				'ajaxurl'        => admin_url( 'admin-ajax.php' ),
				'card.name'      => __( 'A valid Name on Card is required.', 'yith-stripe-connect-for-woocommerce' ),
				'card.number'    => __( 'The credit card number seems to be invalid.', 'yith-stripe-connect-for-woocommerce' ),
				'card.cvc'       => __( 'The CVC number seems to be invalid.', 'yith-stripe-connect-for-woocommerce' ),
				'card.expire'    => __( 'The expiration date seems to be invalid.', 'yith-stripe-connect-for-woocommerce' ),
				'billing.fields' => __( 'You have to add extra information to checkout.', 'yith-stripe-connect-for-woocommerce' ),
			) );

			wp_register_style( 'yith-stripe-connect-css', YITH_WCSC_ASSETS_URL . 'css/style-yith-sc-checkout.css', null, YITH_WCSC_VERSION );

			wp_enqueue_script( 'yith-stripe-connect-js' );
			wp_enqueue_style( 'yith-stripe-connect-css' );
		}

		/**
		 * Handling payment and processing the order.
		 *
		 * @param int $order_id
		 *
		 * @return array
		 * @since 1.0.0
		 */
		public function process_payment( $order_id ) {
			$order                = wc_get_order( $order_id );
			$this->_current_order = $order;
			$this->log( 'info', 'Generating payment form for order ' . $order->get_order_number() . '.' );

			return $this->process_standard_payment();
		}

		/**
		 * Performs the payment on Stripe
		 *
		 * @param $order  WC_Order
		 *
		 * @return bool|WP_Error
		 * @throws Stripe\Exception\ApiErrorException|Exception
		 * @since 1.0.0
		 */
		public function pay( $order = null ) {
			// Initializate SDK and set private key
			$this->init_stripe_connect_api();

			// get amount
			$amount = $order->get_total();

			if ( 0 == $amount ) {
				// Payment complete
				$order->payment_complete();

				return true;
			}

			// retrieve payment intent
			$intent = $this->get_intent( $order );

			if ( ! $intent ) {
				$this->log( 'error', 'No intent found for order ' . $order ? $order->get_id() : 'N/A' );

				return new WP_Error( 'stripe_error', __( 'Sorry, There was an error while processing payment; please, try again', 'yith-stripe-connect-for-woocommerce' ) );
			}

			if ( $intent->status == 'requires_confirmation' ) {
				$intent->confirm();
			}

			if ( $intent->status == 'requires_action' ) {
				do_action( 'yith_wcstripe_intent_requires_action', $intent, $order );

				$this->log( 'info', 'Intent requires actions ' . $intent->id );

				return new WP_Error( 'stripe_error', __( 'Please, validate your payment method before proceeding further; in order to do this, refresh the page and proceed at checkout as usual', 'yith-stripe-connect-for-woocommerce' ) );
			} elseif ( ! in_array( $intent->status, array( 'succeeded', 'requires_capture' ) ) ) {
				$this->log( 'error', sprintf( 'Intent doesn\'t have a valid status %s (%s)', $intent->id, $intent->status ) );

				return new WP_Error( 'stripe_error', __( 'Sorry, There was an error while processing payment; please, try again', 'yith-stripe-connect-for-woocommerce' ) );
			}

			// register intent for the order
			$order->update_meta_data( 'intent_id', $intent->id );

			// update intent data
			$this->api_handler->update_intent( $intent->id, array(
				'description'    => apply_filters( 'yith_wcsc_charge_description', sprintf( __( '%s - Order %s', 'yith-stripe-connect-for-woocommerce' ), esc_html( get_bloginfo( 'name' ) ), $order->get_order_number() ), esc_html( get_bloginfo( 'name' ) ), $order->get_order_number() ),
				'metadata'       => apply_filters( 'yith_wcstripe_connect_metadata', array(
					'order_id'    => $order->get_id(),
					'order_email' => yit_get_prop( $order, 'billing_email' ),
					'instance'    => $this->instance_url,
				), 'charge' ),
				'transfer_group' => yit_get_order_id( $order )
			) );

			// retrieve charge to use for next steps
			$charge = end( $intent->charges->data );

			// attach payment method to customer
			$customer       = $this->get_customer( $order );
			$this->customer = $customer ? $customer->id : '';

			// save card token
			$token = $this->save_token( $intent->payment_method );

			if ( $token ) {
				$order->add_payment_token( $token );
				$this->token = $intent->payment_method;
			}

			// Payment complete
			$is_payment_complete = $order->payment_complete( $charge->id );

			if ( $is_payment_complete ) {
				do_action( 'yith_wcsc_payment_complete', $order->get_id(), $charge->id );
			}

			// Add order note
			$order->add_order_note( sprintf( __( 'Stripe Connect payment approved (ID: %s)', 'yith-stripe-connect-for-woocommerce' ), $charge->id ) );

			// Remove cart
			WC()->cart->empty_cart();

			// delete session
			$this->delete_session_intent();

			// update post meta
			yit_save_prop( $order, 'yith_stripe_connect_customer_id', $customer->id );

			// Return thank you page redirect
			return true;
		}

		/**
		 * Handling payment and processing the order.
		 *
		 * @param WC_Order $order
		 *
		 * @return array
		 * @since 1.0.0
		 */
		protected function process_standard_payment( $order = null ) {
			if ( empty( $order ) ) {
				$order = $this->_current_order;
			}

			try {

				// Initializate SDK and set private key
				$this->init_stripe_connect_api();

				// retrieve payment intent
				$intent = $this->get_intent( $order );

				// no intent yet; return error
				if ( ! $intent ) {
					throw new Exception( __( 'Sorry, There was an error while processing payment; please, try again', 'yith-stripe-connect-for-woocommerce' ), null );
				}

				$payment_method = isset( $_POST['stripe_connect_payment_method'] ) ? sanitize_text_field( $_POST['stripe_connect_payment_method'] ) : false;

				if ( ! $payment_method && isset( $_POST['wc-yith-stripe-connect-payment-token'] ) && 'new' !== $_POST['wc-yith-stripe-connect-payment-token'] ) {
					$token_id = intval( $_POST['wc-yith-stripe-connect-payment-token'] );
					$token    = WC_Payment_Tokens::get( $token_id );

					if ( $token && $token->get_user_id() == get_current_user_id() && $token->get_gateway_id() == $this->id ) {
						$payment_method = $token->get_token();
					}
				}

				// it intent is missing payment method, or requires update, proceed with update
				if (
					( 'requires_payment_method' == $intent->status && $payment_method ) ||
					( ( $intent->amount != yith_wcsc_get_amount( $order->get_total(), $order->get_currency() ) || $intent->currency != strtolower( $order->get_currency() ) ) && ! in_array( $intent->status, array(
							'requires_action',
							'requires_capture',
							'succeeded',
							'canceled'
						) ) )
				) {
					// updates session intent
					$intent = $this->update_session_intent( $payment_method, $order->get_id() );
				}

				// if intent is still missing payment method, return an error
				if ( $intent->status == 'requires_payment_method' ) {
					throw new Exception( __( 'No payment method could be applied to this payment; please try again selecting another payment method', 'yith-stripe-connect-for-woocommerce' ) );
				}

				// intent requires confirmation; try to confirm it
				if ( $intent->status == 'requires_confirmation' ) {
					$intent->confirm();
				}

				// register intent for the order
				$order->update_meta_data( 'intent_id', $intent->id );

				// confirmation requires additional action; return to customer
				if ( $intent->status == 'requires_action' ) {
					$order->save();

					// manual confirm after checkout
					$this->_current_intent_secret = $intent->client_secret;

					return array(
						'result'   => 'success',
						'redirect' => $this->get_return_url( $order )
					);
				}

				// pay
				$response = $this->pay( $order );

				if ( $response === true ) {
					$response = array(
						'result'   => 'success',
						'redirect' => $this->get_return_url( $order )
					);

				} elseif ( is_a( $response, 'WP_Error' ) ) {
					throw Stripe\Exception\UnknownApiErrorException::factory( $response->get_error_message( 'stripe_error' ) );
				}

				return $response;

			} catch ( Stripe\Exception\UnknownApiErrorException $e ) {
				$body    = $e->getJsonBody();
				$message = $e->getMessage();

				if ( $body ) {
					$err = $body['error'];
					if ( isset( $this->errors[ $err['code'] ] ) ) {
						$message = $this->errors[ $err['code'] ];
					}

					$this->log( 'info', 'Stripe Error: ' . $e->getHttpStatus() . ' - ' . print_r( $e->getJsonBody(), true ) );

					// add order note
					$order->add_order_note( 'Stripe Error: ' . $e->getHttpStatus() . ' - ' . $e->getMessage() );

					// add block if there is an error on card
					if ( $err['type'] == 'card_error' ) {
						WC()->session->refresh_totals = true;
					}
				}

				wc_add_notice( $message, 'error' );

				return array(
					'result'   => 'fail',
					'redirect' => ''
				);
			} catch ( Exception $e ) {
				wc_add_notice( $e->getMessage(), 'error' );

				return array(
					'result'   => 'fail',
					'redirect' => ''
				);
			}
		}

		/**
		 * Retrieve source selected for current subscription
		 *
		 * @return string
		 */
		protected function get_source() {
			$card_id = ( isset( $_POST['wc-yith-stripe-connect-payment-token'] ) && 'new' != $_POST['wc-yith-stripe-connect-payment-token'] ) ? $_POST['wc-yith-stripe-connect-payment-token'] : false;

			if ( $card_id ) {
				$token = WC_Payment_Tokens::get( $card_id );
				if ( $token && $token->get_user_id() === get_current_user_id() ) {
					$card_id = $token->get_token();
				}
			}

			return $card_id;
		}

		/**
		 * Get token card from post
		 *
		 * @access protected
		 * @return string
		 * @author Francisco Javier Mateo
		 */
		protected function get_token() {
			$card_id = $this->get_source();

			if ( ! $card_id ) {
				if ( isset( $_POST['stripe_connect_token'] ) ) {
					$card_id = $_POST['stripe_connect_token'];
				} else {
					return 'new';
				}
			}

			return apply_filters( 'yith_stripe_connect_selected_card', $card_id );
		}

		/* === PAYMENT INTENT MANAGEMENT === */

		/**
		 * Retrieve intent for current operation; if none, creates one
		 *
		 * @param $order \WC_Order|bool Current order
		 *
		 * @return \Stripe\PaymentIntent|bool Payment intent or false on failure
		 */
		public function get_intent( $order = false ) {
			$intent_id = false;

			// check order first
			if ( $order ) {
				$intent_id = $order->get_meta( 'intent_id', true );
			}

			// then $_POST
			if ( ! $intent_id && isset( $_POST['stripe_connect_intent'] ) ) {
				$intent_id = sanitize_text_field( $_POST['stripe_connect_intent'] );
			}

			// and finally session
			if ( ! $intent_id ) {
				$intent    = $this->get_session_intent( $order->get_id() );
				$intent_id = $intent ? $intent->id : false;
			}

			if ( ! $intent_id ) {
				return false;
			}

			// retrieve intent from id
			if ( ! isset( $intent ) ) {
				$intent = $this->api_handler->get_correct_intent( $intent_id );
			}

			if ( ! $intent ) {
				return false;
			}

			return $intent;
		}

		/**
		 * Get intent for current session
		 *
		 * @return \Stripe\PaymentIntent|bool Session payment intent or false on failure
		 */
		public function get_session_intent( $order_id = false ) {
			global $wp;

			// Initialize SDK and set private key
			$this->init_stripe_connect_api();

			$session = WC()->session;

			if ( ! $session ) {
				return false;
			}

			$intent_id = $session->get( 'yith_stripe_connect_intent' );

			if ( ! $order_id && is_checkout_pay_page() ) {
				$order_id = isset( $wp->query_vars['order-pay'] ) ? $wp->query_vars['order-pay'] : false;
			}

			if ( $order_id ) {
				$order       = wc_get_order( $order_id );
				$currency    = strtolower( $order->get_currency() );
				$total       = yith_wcsc_get_amount( $order->get_total(), $currency );
				$description = apply_filters( 'yith_wcsc_charge_description', sprintf( __( '%s - Order %s', 'yith-stripe-connect-for-woocommerce' ), esc_html( get_bloginfo( 'name' ) ), $order->get_order_number() ), esc_html( get_bloginfo( 'name' ) ), $order->get_order_number() );
				$metadata    = array(
					'cart_hash'   => '',
					'order_id'    => $order_id,
					'order_email' => yit_get_prop( $order, 'billing_email' ),
				);
			} else {
				$cart = WC()->cart;
				$cart && $cart->calculate_totals();
				$total       = $cart ? yith_wcsc_get_amount( $cart->total ) : 0;
				$currency    = strtolower( get_woocommerce_currency() );
				$description = $cart ? sprintf( __( 'Payment intent for cart %s', 'yith-stripe-connect-for-woocommerce' ), $cart->get_cart_hash() ) : '';
				$metadata    = array(
					'cart_hash'   => $cart ? $cart->get_cart_hash() : '',
					'order_id'    => '',
					'order_email' => ''
				);
			}

			$is_checkout = is_checkout() || ( defined( 'YITH_STRIPE_CONNECT_DOING_CHECKOUT' ) && YITH_STRIPE_CONNECT_DOING_CHECKOUT );

			if ( ! $total || ! $is_checkout ) {
				return $this->get_session_setup_intent();
			}

			// if total don't match requirements, skip intent creation
			if ( ! $total || $total > 99999999 ) {
				$this->delete_session_intent();

				return false;
			}

			if ( $intent_id ) {
				$intent = $this->api_handler->get_intent( $intent_id );

				if ( $intent ) {

					// if intent isn't longer available, generate a new one
					if ( ! in_array( $intent->status, array(
							'requires_payment_method',
							'requires_confirmation',
							'requires_action'
						) ) && ! defined( 'WOOCOMMERCE_CHECKOUT' ) ) {
						$this->delete_session_intent( $intent );

						return $this->create_session_intent( array( 'order_id' => $order_id ) );
					}

					if ( $intent->amount != $total || $intent->currency != $currency ) {
						$intent = $this->api_handler->update_intent( $intent->id, array(
							'amount'      => $total,
							'currency'    => $currency,
							'description' => $description,
							'metadata'    => apply_filters( 'yith_wcstripe_connect_metadata', array_merge(
								array(
									'instance' => $this->instance_url
								),
								$metadata
							), 'create_payment_intent' )
						) );
					}

					return $intent;
				}
			}

			return $this->create_session_intent( array( 'order_id' => $order_id ) );
		}

		/**
		 * Get setup intent for current session
		 *
		 * @return \Stripe\SetupIntent|bool Session setup intent or false on failure
		 */
		public function get_session_setup_intent() {
			$session   = WC()->session;
			$intent_id = $session->get( 'yith_stripe_connect_setup_intent' );

			if ( $intent_id ) {
				$intent = $this->api_handler->get_setup_intent( $intent_id );

				if ( $intent ) {
					// if intent isn't longer available, generate a new one
					if ( ! in_array( $intent->status, array(
						'requires_payment_method',
						'requires_confirmation',
						'requires_action'
					) ) ) {
						$this->delete_session_setup_intent( $intent );

						return $this->create_session_setup_intent();
					}

					return $intent;
				}
			}

			return $this->create_session_setup_intent();
		}

		/**
		 * Create a new intent for current session
		 *
		 * @param $args array array of argument to use for intent creation. Following a list of accepted params<br/>
		 *              [
		 *              'amount' // total to pay
		 *              'currency' // order currency
		 *              'description' // transaction description; will be modified after confirm
		 *              'metadata' // metadata for the transaction; will be modified after confirm
		 *              'setup_future_usage' // default to 'off_session', to reuse in renews when needed
		 *              'customer' // stripe customer id for current user, if any
		 *              ]
		 *
		 * @return \Stripe\PaymentIntent|bool Generate payment intent, or false on failure
		 */
		public function create_session_intent( $args = array() ) {
			global $wp;

			$customer_id = false;
			$order_id    = false;

			if ( is_user_logged_in() ) {
				$customer_id = YITH_Stripe_Connect_Customer()->get_customer_id( get_current_user_id() );
			}

			if ( isset( $args['order_id'] ) ) {
				$order_id = $args['order_id'];
				unset( $args['order_id'] );
			} elseif ( is_checkout_pay_page() ) {
				$order_id = isset( $wp->query_vars['order-pay'] ) ? $wp->query_vars['order-pay'] : false;
			}

			if ( $order_id ) {
				$order       = wc_get_order( $order_id );
				$currency    = $order->get_currency();
				$total       = yith_wcsc_get_amount( $order->get_total(), $currency );
				$description = apply_filters( 'yith_wcsc_charge_description', sprintf( __( '%s - Order %s', 'yith-stripe-connect-for-woocommerce' ), esc_html( get_bloginfo( 'name' ) ), $order->get_order_number() ), esc_html( get_bloginfo( 'name' ) ), $order->get_order_number() );
				$metadata    = array(
					'order_id'    => $order_id,
					'order_email' => yit_get_prop( $order, 'billing_email' ),
					'cart_hash'   => ''
				);
			} else {
				$cart        = WC()->cart;
				$total       = $cart ? yith_wcsc_get_amount( $cart->total ) : 0;
				$currency    = strtolower( get_woocommerce_currency() );
				$description = $cart ? sprintf( __( 'Payment intent for cart %s', 'yith-stripe-connect-for-woocommerce' ), $cart->get_cart_hash() ) : '';
				$metadata    = array(
					'cart_hash'   => $cart ? $cart->get_cart_hash() : '',
					'order_id'    => '',
					'order_email' => ''
				);
			}

			//Guest user
			if ( ! $customer_id && $order_id ) {
				$order    = wc_get_order( $order_id );
				$customer = $this->get_customer( $order );
				if ( $customer ) {
					$customer_id = $customer->id;
				}
			}

			$defaults = apply_filters( 'yith_stripe_connect_create_payment_intent', array_merge(
				array(
					'amount'              => $total,
					'currency'            => $currency,
					'description'         => $description,
					'metadata'            => apply_filters( 'yith_wcstripe_connect_metadata', array_merge(
						array(
							'instance' => $this->instance_url
						),
						$metadata
					), 'create_payment_intent' ),
					'setup_future_usage'  => 'off_session',
					'capture_method'      => 'automatic',
					'confirmation_method' => 'manual'
				),
				$customer_id ? array(
					'customer' => $customer_id
				) : array()
			) );

			$args = wp_parse_args( $args, $defaults );

			// Initialize SDK and set private key
			$this->init_stripe_connect_api();

			$session = WC()->session;

			try {
				$intent = $this->api_handler->create_intent( $args );
			} catch ( Exception $e ) {
				return false;
			}

			if ( ! $intent ) {
				return false;
			}

			if ( $session ) {
				$session->set( 'yith_stripe_connect_intent', $intent->id );
			}

			return $intent;
		}

		/**
		 * Create a new setup intent for current session
		 *
		 * @param $args array array of argument to use for intent creation. Following a list of accepted params<br/>
		 *              [
		 *              'metadata' // metadata for the transaction; will be modified after confirm
		 *              'usage' // default to 'off_session', to reuse in renews when needed
		 *              'customer' // stripe customer id for current user, if any
		 *              ]
		 *
		 * @return \Stripe\PaymentIntent|bool Generate payment intent, or false on failure
		 */
		public function create_session_setup_intent( $args = array() ) {
			$customer_id = false;

			if ( is_user_logged_in() ) {
				$customer_id = YITH_Stripe_Connect_Customer()->get_customer_id( get_current_user_id() );
			}

			$defaults = apply_filters( 'yith_wcstripe_connect_create_payment_intent', array_merge(
				array(
					'metadata' => apply_filters( 'yith_wcstripe_connect_metadata', array(
						'instance' => $this->instance_url
					), 'create_setup_intent' ),
					'usage'    => 'off_session',
				),
				$customer_id ? array(
					'customer' => $customer_id
				) : array()
			) );

			$args = wp_parse_args( $args, $defaults );

			// Initialize SDK and set private key
			$this->init_stripe_connect_api();

			$session = WC()->session;

			$intent = $this->api_handler->create_setup_intent( $args );

			if ( ! $intent ) {
				return false;
			}

			$session->set( 'yith_stripe_connect_setup_intent', $intent->id );

			return $intent;
		}

		/**
		 * Update session intent, registering new cart total and currency, and configuring a payment method if needed
		 *
		 * @param $token int|bool Selected token id, or null if new payment method is used
		 * @param $order int|bool Current order id, or null if cart should be used
		 *
		 * @return PaymentIntent|SetupIntent|bool Updated intent, or false on failure
		 * @throws Exception
		 */
		public function update_session_intent( $token = false, $order = false ) {
			// retrieve intent; this will automatically update total and currency
			$intent = $this->get_session_intent( $order );

			if ( ! $intent ) {
				throw new Exception( __( 'There was an error with payment process; please try again later', 'yith-stripe-connect-for-woocommerce' ) );
			}

			if ( ! $token ) {
				return $intent;
			}

			// prepare payment method to use for update
			if ( is_int( $token ) ) {
				if ( ! is_user_logged_in() ) {
					throw new Exception( __( 'You must login before using a registered card', 'yith-stripe-connect-for-woocommerce' ) );
				}

				$token = WC_Payment_Tokens::get( $token );

				if ( ! $token || $token->get_user_id() != get_current_user_id() ) {
					throw new Exception( __( 'The card you\'re trying to use isn\'t valid; please, try again with another payment method', 'yith-stripe-connect-for-woocommerce' ) );
				}

				$payment_method = $token->get_token();
			} elseif ( is_string( $token ) ) {
				$payment_method = $token;
			}

			// if a payment method was provided, try to bind it to payment intent
			if ( $payment_method ) {
				$result = $this->api_handler->update_correct_intent( $intent->id, array(
					'payment_method' => $payment_method
				) );

				// check if update was successful
				if ( ! $result ) {
					throw new Exception( __( 'The card you\'re trying to use isn\'t valid; please, try again with another payment method', 'yith-stripe-connect-for-woocommerce' ) );
				}

				// update intent object that will be returned
				$intent = $result;
			}

			return $intent;
		}

		/**
		 * Removes intent from current session
		 * Method is intended to cancel session, but will also cancel PaymentIntent on Stripe, if object is passed as param
		 *
		 * @param $intent \Stripe\PaymentIntent|bool Payment intent to cancel, or false if it is not required
		 *
		 * @return void
		 */
		public function delete_session_intent( $intent = false ) {
			// Initialize SDK and set private key
			$this->init_stripe_connect_api();

			$session = WC()->session;
			$session->set( 'yith_stripe_connect_intent', '' );

			if ( $intent && isset( $intent->status ) && ! in_array( $intent->status, array(
					'succeeded',
					'cancelled'
				) ) ) {
				try {
					$intent->cancel();
				} catch ( Exception $e ) {
					return;
				}
			}
		}

		/**
		 * Removes intent from current session
		 * Method is intended to cancel session, but will also cancel SetupIntent on Stripe, if object is passed as param
		 *
		 * @param $intent \Stripe\setupIntent|bool Setup intent to cancel, or false if it is not required
		 *
		 * @return void
		 */
		public function delete_session_setup_intent( $intent = false ) {
			// Initialize SDK and set private key
			$this->init_stripe_connect_api();

			$session = WC()->session;
			$session->set( 'yith_stripe_connect_setup_intent', '' );

			if ( $intent && isset( $intent->status ) && ! in_array( $intent->status, array(
					'succeeded',
					'cancelled'
				) ) ) {
				try {
					$intent->cancel();
				} catch ( Exception $e ) {
					return;
				}
			}
		}

		/* === TOKENS HANDLING === */

		/**
		 * Get customer of Stripe account or create a new one if not exists
		 *
		 * @param $order WC_Order
		 *
		 * @return \Stripe\Customer
		 *
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 * @since  1.1.0
		 */
		public function get_customer( $order ) {

			$this->init_stripe_connect_api();

			if ( is_int( $order ) ) {
				$order = wc_get_order( $order );
			}

			$current_order_id = ( isset( $this->_current_order ) && $this->_current_order instanceof WC_Order ) ? $this->_current_order->get_id() : false;
			$order_id         = $order->get_id();

			if ( $current_order_id == $order_id && ! empty( $this->_current_customer ) ) {
				return $this->_current_customer;
			}

			$user_id        = is_user_logged_in() ? $order->get_user_id() : false;
			$local_customer = is_user_logged_in() ? YITH_Stripe_Connect_Customer()->get_usermeta_info( $user_id ) : false;

			try {
				$customer = isset( $local_customer['id'] ) ? $this->api_handler->get_customer( $local_customer['id'] ) : false;
			} catch ( Exception $e ) {
				$customer = false;
			}

			// get existing
			if ( $customer ) {
				if ( $current_order_id == $order_id ) {
					$this->_current_customer = $customer;
				}

				return $customer;
			}

			// create new one
			$user = is_user_logged_in() ? $order->get_user() : false;

			if ( is_user_logged_in() ) {
				$description = $user->user_login . ' (#' . $order->get_user_id() . ' - ' . $user->user_email . ') ' . yit_get_prop( $order, 'billing_first_name' ) . ' ' . yit_get_prop( $order, 'billing_last_name' );
			} else {
				$description = yit_get_prop( $order, 'billing_email' ) . ' (' . __( 'Guest', 'yith-stripe-connect-for-woocommerce' ) . ' - ' . yit_get_prop( $order, 'billing_email' ) . ') ' . yit_get_prop( $order, 'billing_first_name' ) . ' ' . yit_get_prop( $order, 'billing_last_name' );
			}

			$params = array(
				'email'       => yit_get_prop( $order, 'billing_email' ),
				'description' => $description,
				'metadata'    => apply_filters( 'yith_wcstripe_connect_metadata', array(
					'user_id'  => is_user_logged_in() ? $order->get_user_id() : false,
					'instance' => $this->instance_url
				), 'create_customer' )
			);

			$customer    = $this->api_handler->create_customer( $params );
			$this->token = $customer->invoice_settings->default_payment_method;

			// update user meta
			if ( is_user_logged_in() ) {
				YITH_Stripe_Connect_Customer()->update_usermeta_info( $user_id, array(
					'id'             => $customer->id,
					'default_source' => $customer->invoice_settings->default_payment_method
				) );
			}

			if ( $current_order_id == $order_id ) {
				$this->_current_customer = $customer;
			}

			return $customer;

		}

		/**
		 * Save the token on db.
		 *
		 * @param string $payment_method_id
		 *
		 * @return bool|WC_Payment_Token|WC_Payment_Token_CC
		 * @throws Stripe\Exception\ApiErrorException
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 * @since  1.1.0
		 */
		public function save_token( $payment_method_id = null ) {

			if ( ! is_user_logged_in() || ! $this->save_cards ) {
				return false;
			}

			$this->init_stripe_connect_api();

			$user           = wp_get_current_user();
			$local_customer = YITH_Stripe_Connect_Customer()->get_usermeta_info( $user->ID );
			$customer       = ! empty( $local_customer['id'] ) ? $this->api_handler->get_customer( $local_customer['id'] ) : false;
			$payment_method = $this->api_handler->get_payment_method( $payment_method_id );

			if ( ! $payment_method ) {
				return false;
			}

			if ( $customer && $payment_method->customer != $customer->id ) {
				try {
					$payment_method->attach( array(
						'customer' => $customer->id
					) );
				} catch ( Exception $e ) {
					return false;
				}

				$this->api_handler->update_customer( $customer, array(
					'invoice_settings' => array(
						'default_payment_method' => $payment_method_id
					)
				) );

				$customer->sources->data[] = $payment_method->card;
			} elseif ( ! $customer ) {
				$params = array(
					'payment_method' => $payment_method_id,
					'email'          => $user->billing_email,
					'description'    => substr( $user->user_login . ' (#' . $user->ID . ' - ' . $user->user_email . ') ' . $user->billing_first_name . ' ' . $user->billing_last_name, 0, 350 ),
					'metadata'       => apply_filters( 'yith_wcstripe_metadata', array(
						'user_id'  => $user->ID,
						'instance' => $this->instance_url
					), 'create_customer' )
				);

				$customer = $this->api_handler->create_customer( $params );
			}

			$already_registered        = false;
			$already_registered_tokens = WC_Payment_Tokens::get_customer_tokens( $user->ID, $this->id );
			$registered_token          = false;

			if ( ! empty( $already_registered_tokens ) ) {
				foreach ( $already_registered_tokens as $registered_token ) {
					/**
					 * @var $registered_token \WC_Payment_Token
					 */
					$registered_fingerprint = $registered_token->get_meta( 'fingerprint', true );

					if ( $registered_fingerprint == $payment_method->card->fingerprint || $registered_token->get_token() == $payment_method_id ) {
						$already_registered = true;
						break;
					}
				}
			}

			if ( ! $already_registered ) {
				// save card
				$token = new WC_Payment_Token_CC();
				$token->set_token( $payment_method_id );
				$token->set_gateway_id( $this->id );
				$token->set_user_id( $user->ID );
				$token->set_card_type( strtolower( $payment_method->card->brand ) );
				$token->set_last4( $payment_method->card->last4 );
				$token->set_expiry_month( ( str_pad( $payment_method->card->exp_month, 2, '0', STR_PAD_LEFT ) ) );
				$token->set_expiry_year( $payment_method->card->exp_year );
				$token->set_default( true );
				$token->add_meta_data( 'fingerprint', $payment_method->card->fingerprint );
				$token->add_meta_data( 'confirmed', true );

				if ( ! $token->save() ) {
					throw Stripe\Exception\UnknownApiErrorException::factory( __( 'Credit card info not valid', 'yith-stripe-connect-for-woocommerce' ) );
				}

				// backward compatibility
				if ( $customer ) {
					YITH_Stripe_Connect_Customer()->update_usermeta_info( $customer->metadata->user_id, array(
						'id'             => $customer->id,
						'default_source' => $customer->invoice_settings->default_payment_method
					) );
				}

				//DO_ACTION : yith_wcstripe_connect_created_card : Do action after that the cart is created : cart id and customer are the arguments
				do_action( 'yith_wcstripe_connect_created_card', $payment_method_id, $customer );

				return $token;
			} else {
				$registered_token->set_default( true );
				$registered_token->save();

				return $registered_token;
			}
		}

		/**
		 * Attach payment method to customer
		 *
		 * @param $customer          string|Stripe\Customer Customer to update
		 * @param $payment_method_id string Payment method to save
		 *
		 * @return bool Status of the operation
		 *
		 * @throws Exception
		 */
		public function attach_payment_method( $customer, $payment_method_id ) {

			try {
				$customer       = $this->api_handler->get_customer( $customer );
				$payment_method = $this->api_handler->get_payment_method( $payment_method_id );

				$payment_method->attach( array(
					'customer' => $customer->id
				) );
			} catch ( Exception $e ) {
				return false;
			}

			$this->api_handler->update_customer( $customer, array(
				'invoice_settings' => array(
					'default_payment_method' => $payment_method_id
				)
			) );

			return true;
		}

		/**
		 * Add payment method via my account page.
		 *
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 * @since  1.1.0
		 */
		public function add_payment_method() {
			try {
				// Initializate SDK and set private key
				$this->init_stripe_connect_api();

				$intent = $this->get_intent();

				if ( ! $intent ) {
					throw new Exception( __( 'Sorry, There was an error while registering payment method; please, try again', 'yith-stripe-connect-for-woocommerce' ) );
				} elseif ( $intent->status == 'requires_action' ) {
					do_action( 'yith_stripe_connect_setup_intent_requires_action', $intent, get_current_user_id() );

					throw new Exception( __( 'Please, validate your payment method before proceeding further; in order to do this, refresh the page and proceed at checkout as usual', 'yith-stripe-connect-for-woocommerce' ) );
				} elseif ( ! in_array( $intent->status, array( 'succeeded', 'requires_capture' ) ) ) {
					throw new Exception( __( 'Sorry, There was an error while registering payment method; please, try again', 'yith-stripe-connect-for-woocommerce' ) );
				}

				$token = $this->save_token( $intent->payment_method );

				return apply_filters( 'yith_stripe_connect_add_payment_method_result', array(
					'result'   => 'success',
					'redirect' => wc_get_endpoint_url( 'payment-methods' ),
				), $token );

			} catch ( Exception $e ) {
				wc_add_notice( $e->getMessage(), 'error' );

				return false;
			}
		}

		/* === UTILITY METHODS === */

		/**
		 * Get return url for payment intent
		 *
		 * @param $order \WC_Order Order
		 *
		 * @return string Return url
		 */
		public function get_return_url( $order = null ) {
			$redirect = parent::get_return_url( $order );

			if ( ! $order || empty( $this->_current_intent_secret ) ) {
				return $redirect;
			}

			// Put the final thank you page redirect into the verification URL.
			$verification_url = add_query_arg(
				array(
					'order'       => $order->get_id(),
					'redirect_to' => rawurlencode( $redirect ),
				),
				WC_AJAX::get_endpoint( 'yith_stripe_connect_verify_intent' )
			);

			// Combine into a hash.
			$redirect = sprintf( '#yith-stripe-connect-confirm-pi-%s:%s', $this->_current_intent_secret, $verification_url );

			return $redirect;
		}

		/**
		 * Add custom fields to CC form on checkout
		 *
		 * @param $fields array Array of available fields
		 * @param $id     string Gateway ID
		 *
		 * @return array Array of filtered fields
		 */
		public function credit_form_add_fields( $fields, $id ) {
			$cvc_field = '<p class="form-row form-row-last validate-required" >
			<label for="' . esc_attr( $this->id ) . '-card-cvc">' . esc_html__( 'Card code', 'woocommerce' ) . ' <span class="required">*</span></label>
    
			<input id="' . esc_attr( $this->id ) . '-card-cvc" class="input-text wc-credit-card-form-card-cvc" inputmode="numeric" autocomplete="off" autocorrect="no" autocapitalize="no" spellcheck="no" type="tel" maxlength="4" placeholder="' . esc_attr__( 'CVC', 'woocommerce' ) . '" ' . $this->field_name( 'card-cvc' ) . ' style="width:100px" />
			</p>';

			$default_fields = array(
				'card-number-field' => '<p class="form-row form-row-wide validate-required ">
				<label for="' . esc_attr( $this->id ) . '-card-number">' . esc_html__( 'Card number', 'woocommerce' ) . ' <span class="required">*</span></label>
				<input id="' . esc_attr( $this->id ) . '-card-number" class="input-text wc-credit-card-form-card-number" inputmode="numeric" autocomplete="cc-number" autocorrect="no" autocapitalize="no" spellcheck="no" type="tel" placeholder="&bull;&bull;&bull;&bull; &bull;&bull;&bull;&bull; &bull;&bull;&bull;&bull; &bull;&bull;&bull;&bull;" ' . $this->field_name( 'card-number' ) . ' />
				
				</p>',
				'card-expiry-field' => '<p class="form-row form-row-first validate-required">
				<label for="' . esc_attr( $this->id ) . '-card-expiry">' . esc_html__( 'Expiry (MM/YY)', 'woocommerce' ) . ' <span class="required">*</span></label>
				<input id="' . esc_attr( $this->id ) . '-card-expiry" class="input-text wc-credit-card-form-card-expiry" inputmode="numeric" autocomplete="cc-exp" autocorrect="no" autocapitalize="no" maxlength="7" spellcheck="no" type="tel" placeholder="' . esc_attr__( 'MM / YY', 'woocommerce' ) . '" ' . $this->field_name( 'card-expiry' ) . ' />
				</p>',
			);

			if ( $this->show_name_on_card == 'yes' ) {
				$default_fields = array_merge( array(
					'card-name-field' => '<p class="form-row form-row-wide">
						<label for="' . esc_attr( $this->id ) . '-card-name">' . apply_filters( 'yith_wccs_name_on_card_label', __( 'Name on Card', 'yith-stripe-connect-for-woocommerce' ) ) . ' <span class="required">*</span></label>
						
						<input id="' . esc_attr( $this->id ) . '-card-name" class="input-text wc-credit-card-form-card-name" type="text" autocomplete="off" placeholder="' . __( 'Name on Card', 'yith-stripe-connect-for-woocommerce' ) . '" ' . $this->field_name( 'card-name' ) . ' />

						</p>'
				), $default_fields );
			}
			if ( ! $this->supports( 'credit_card_form_cvc_on_saved_method' ) ) {
				$default_fields['card-cvc-field'] = $cvc_field;
			}

			return $default_fields;
		}

		/**
		 * Return the gateway icons.
		 *
		 * @return string
		 */
		public function get_icon() {
			$icon_html = apply_filters( 'yith_wc_stripe_connect_credit_cards_logos', '', $this->credit_cards_logo );
			$width     = apply_filters( 'yith_wc_stripe_connect_credit_cards_logos_width', '40px' );
			foreach ( $this->credit_cards_logo as $logo_card ) {
				$icon_html .= '<img class="yith_wcsc_icon" src="' . YITH_WCSC_ASSETS_URL . 'images/' . esc_attr( $logo_card ) . '.svg" alt="' . $logo_card . '" width="' . $width . '" />';
			}

			return $icon_html;
		}

		/**
		 * Log to txt file
		 *
		 * @param $message
		 *
		 * @since 1.0.0
		 */
		public function log( $level, $message ) {
			if ( isset( $this->log, $this->log_enabled ) && $this->log_enabled ) {
				$this->log->log( $level, $message, array( 'source' => 'stripe-connect', '_legacy' => true ) );
			}
		}

		/**
		 * Initialise Gateway Settings Form Fields.
		 */
		public function init_form_fields() {
			$this->form_fields = include( YITH_WCSC_OPTIONS_PATH . 'settings-sc-gateway.php' );
		}

		/**
		 * Init api class
		 *
		 * @return void
		 */
		public function init_stripe_connect_api() {
			if ( is_a( $this->api_handler, 'YITH_Stripe_Connect_API_Handler' ) ) {
				return;
			}
			$this->api_handler = YITH_Stripe_Connect_API_Handler::instance();
		}

		/**
		 * Remove the checkbox from checkout.
		 *
		 * @return bool
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 * @since  1.1.0
		 */
		public function save_payment_method_checkbox() {
			return false;
		}

		/**
		 * Return unique instance of the class
		 *
		 * @return YITH_Stripe_Connect_Gateway
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}

			return self::$_instance;
		}

	}

}
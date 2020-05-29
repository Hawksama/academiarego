<?php
/*
* This file belongs to the YITH Framework.
*
* This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://www.gnu.org/licenses/gpl-3.0.txt
*/
if ( ! defined( 'YITH_WCSC_VERSION' ) ) {
	exit( 'Direct access forbidden.' );
}

use \Stripe\Stripe;
use \Stripe\Charge;
use \Stripe\Account;
use \Stripe\OAuth;
use \Stripe\Customer;
use Stripe\StripeObject;
use \Stripe\PaymentIntent;
use \Stripe\PaymentMethod;
use \Stripe\SetupIntent;

/**
 *
 *
 * @class      YITH_Stripe_Connect_API_Handler
 * @package    Yithemes
 * @since      Version 1.0.0
 * @author     Your Inspiration Themes
 *
 */
if ( ! class_exists( 'YITH_Stripe_Connect_API_Handler' ) ) {

	/**
	 * Class YITH_Stripe_Connect_API_Handler
	 *
	 * @author Francisco Javier Mateo <francisco.mateo@yithemes.com>
	 */
	class YITH_Stripe_Connect_API_Handler {

		/**
		 * Plugin version
		 *
		 * @var string
		 * @since 1.0
		 */
		public $version = YITH_WCSC_VERSION;

		/**
		 * StripeObject Instance
		 *
		 * @var YITH_Stripe_Connect_API_Handler
		 * @since  1.0
		 * @access protected
		 */
		protected static $_instance = null;

		/**
		 * Current environment (yes -> dev, no -> prod)
		 *
		 * @var string
		 */
		public $_test_live = null;

		/**
		 * Current environment (dev|prod)
		 *
		 * @var string
		 */
		public $_env = null;

		/**
		 * Main Instance
		 *
		 * @var YITH_Stripe_Connect_Gateway
		 * @since  1.0
		 * @access protected
		 */
		protected $_stripe_connect_gateway = null;

		/**
		 * Construct
		 *
		 * @author Francisco Mateo
		 * @since  1.0
		 */
		public function __construct() {
			require_once( YITH_WCSC_VENDOR_PATH . 'autoload.php' );

			// Gets all Payments Gateways defined on WooCommerce.
			$payment_gateways = WC()->payment_gateways->payment_gateways();
			// Filter the Gateways and get our YITH Stripe Connect Gateway. We get the Gateway object to gets better their data, settings for example.ff
			$this->_stripe_connect_gateway = $payment_gateways['yith-stripe-connect'];

			$this->_test_live = $this->_stripe_connect_gateway->get_option( 'test-live' );
			$this->_env       = ( $this->_test_live == 'yes' ) ? 'dev' : 'prod';

			$this->init_handler();
		}

		/**
		 * Main plugin Instance
		 *
		 * @return YITH_Stripe_Connect_API_Handler Main instance
		 * @author Francisco Mateo
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}

			return self::$_instance;
		}

		/**
		 * Set correct API key for current configuration
		 *
		 * @return void
		 */
		public function init_handler() {
			$secret_api_key = ( 'yes' == $this->_test_live ) ? $this->_stripe_connect_gateway->get_option( 'api-secret-test-key' ) : $this->_stripe_connect_gateway->get_option( 'api-secret-live-key' );
			Stripe::setAppInfo( 'YITH Stripe Connect for WooCommerce', YITH_WCSC_VERSION, 'https://yithemes.com' );
			Stripe::setApiVersion( YITH_WCSC_API_VERSION );
			Stripe::setApiKey( $secret_api_key );
		}

		/* === ACCOUNT RELATED API === */

		/**
		 * Creates a connected account on Stripe
		 *
		 * @param $args array Array of parameters to use for account creation
		 *
		 * @return StripeObject|bool Created account or false on failure
		 */
		public function create_account( $args = array() ) {
			try {
				$acct = Account::create( $args );
			} catch ( Exception $e ) {
				return false;
			}

			return $acct;
		}

		/**
		 * Retrieves a connected account by ID
		 *
		 * @param $id string Account id
		 *
		 * @return StripeObject|bool Retrieved account or false on failure
		 */
		public function retrieve_account( $id ) {
			try {
				$acct = Account::retrieve( $id );

				return $acct;
			} catch ( Exception $e ) {
				return false;
			}
		}

		/**
		 * Create a charges
		 *
		 * @param $args array Array of parameters to use for charge creation
		 *
		 * @return StripeObject|bool Charge object or false on failure
		 */
		public function create_charge( $args = array() ) {
			try {
				$charge = Charge::create( $args );
			} catch ( Exception $e ) {
				return array( 'error_charge' => $e->getMessage() );
			}

			return $charge;
		}

		/**
		 * Retrieves a charge object
		 *
		 * @param $id string charge id
		 *
		 * @return StripeObject|bool Retrieved charge or false on failure
		 */
		public function retrieve_charge( $id ) {
			try {
				$charge = Charge::retrieve( $id );

				return $charge;
			} catch ( Exception $e ) {
				return false;
			}
		}

		/**
		 * Create a transfer
		 *
		 * @param $args array Array of parameters to use for Transfer creation
		 *
		 * @return StripeObject|bool Transfer created or false on failure
		 */
		public function create_transfer( $args = array() ) {
			try {
				$transfer = \Stripe\Transfer::create( $args );
			} catch ( Exception $e ) {
				return array( 'error_transfer' => $e->getMessage() );
			}

			return $transfer;
		}

		/**
		 * Authorizes an account for the application
		 *
		 * @param $stripe_user_email string Email used to register account on Stripe
		 *
		 * @return StripeObject|bool Connected account or false on failure
		 */
		public function authorize_account( $stripe_user_email ) {
			try {
				$client_id       = $this->_stripe_connect_gateway->get_option( 'api-' . $this->_env . '-client-id' );
				$user_authorized = OAuth::authorizeUrl( array(
					'client_id'   => $client_id,
					'stripe_user' => $stripe_user_email,
				) );
				$this->_stripe_connect_gateway->log( 'info', 'Authorize Account: Account with client_id:"' . $client_id . '" and stripe_user_email:"' . $stripe_user_email . '" authorized' );

				return $user_authorized;
			} catch ( Exception $e ) {
				$this->_stripe_connect_gateway->log( 'error', 'Authorize Account: Could not be authorize account...' . $e->getMessage() );

				return false;
			}
		}

		/**
		 * Retrieves link for OAuth connection
		 *
		 * @return string|bool Connection url or false on failure
		 */
		public function get_OAuth_link() {
			try {
				$args       = array(
					'client_id'    => $this->_stripe_connect_gateway->get_option( 'api-' . $this->_env . '-client-id' ),
					'redirect_uri' => wc_get_page_permalink( 'myaccount' ) . 'stripe-connect',
					'scope'        => 'read_write'
				);
				$OAuth_link = OAuth::authorizeUrl( $args );
			} catch ( Exception $e ) {
				return false;
			}

			return $OAuth_link;
		}

		/**
		 * Retrieves unique token after OAuth connection
		 *
		 * @param $code string Code returned by Stripe after OAuth connection
		 *
		 * @return string|bool Unique authorization code for the user, or false on failure
		 */
		public function get_OAuth_token( $code ) {
			try {
				$client_id = $this->_stripe_connect_gateway->get_option( 'api-' . $this->_env . '-client-id' );
				$args      = array(
					'client_id'  => $client_id,
					'code'       => $code,
					'grant_type' => 'authorization_code',
				);
				$token     = OAuth::token( $args );

				return $token;
			} catch ( Exception $e ) {
				return false;
			}
		}

		/**
		 * Deauthorize the account
		 *
		 * @param $stripe_user_id string Id of the user to deauthorize
		 *
		 * @return StripeObject|bool Stripe customer or false on failure
		 */
		public function deauthorize_account( $stripe_user_id ) {
			try {
				$client_id         = $this->_stripe_connect_gateway->get_option( 'api-' . $this->_env . '-client-id' );
				$user_deauthorized = OAuth::deauthorize( array(
					'client_id'      => $client_id,
					'stripe_user_id' => $stripe_user_id
				), array()
				);

				$this->_stripe_connect_gateway->log( 'info', 'Deauthorize Account: Account with client_id:"' . $client_id . '" deauthorized' );

				return $user_deauthorized;
			} catch ( Exception $e ) {
				if ( $e instanceof \Stripe\Exception\OAuth\InvalidClientException ) {
					$this->_stripe_connect_gateway->log( 'warning', 'Deauthorize Account: Account with client_id:"' . $client_id . '" have been deauthorized previously' );

					return $e;
				}
				$this->_stripe_connect_gateway->log( 'error', 'Deauthorize Account: Could not be deauthorize account...' . $e->getMessage() );

				return false;
			}
		}

		/**
		 * New customer
		 *
		 * @param $params
		 *
		 * @return Customer
		 * @throws \Stripe\Exception\ApiErrorException
		 * @since 1.0.0
		 */
		public function create_customer( $params ) {
			return Customer::create( $params );
		}

		/**
		 * Retrieve customer
		 *
		 * @param $customer string|Customer Customer object or ID
		 *
		 * @return Customer
		 * @throws \Stripe\Exception\ApiErrorException
		 * @since 1.0.0
		 */
		public function get_customer( $customer ) {
			if ( is_a( $customer, '\Stripe\Customer' ) ) {
				return $customer;
			}

			return Customer::retrieve( $customer );
		}

		/* === CARD RELATED API === */

		/**
		 * Update customer
		 *
		 * @param $customer Customer object or ID
		 * @param $params
		 *
		 * @return Customer
		 * @throws \Stripe\Exception\ApiErrorException
		 * @since 1.0.0
		 */
		public function update_customer( $customer, $params ) {
			$customer = $this->get_customer( $customer );

			// edit
			foreach ( $params as $key => $value ) {
				$customer->{$key} = $value;
			}

			// save
			return $customer->save();
		}

		/**
		 * Create a card
		 *
		 * @param $customer Customer object or ID
		 * @param $token
		 *
		 * @return Customer
		 *
		 * @throws \Stripe\Exception\ApiErrorException
		 * @since 1.0.0
		 */
		public function create_card( $customer, $token, $type = 'card' ) {
			$customer = $this->get_customer( $customer );

			$result = $customer->sources->create(
				array(
					$type => $token
				)
			);

			do_action( 'yith_wcstripe_connect_card_created', $customer, $token, $type );

			return $result;
		}

		/**
		 * Retrieve a card object for the customer
		 *
		 * @param $customer Customer object or ID
		 * @param $card_id
		 *
		 * @return Customer
		 *
		 * @since 1.0.0
		 */
		public function get_card( $customer, $card_id, $params = array() ) {
			$card = $customer->sources->retrieve( $card_id, $params );

			return $card;
		}

		/**
		 * Se the default card for the customer
		 *
		 * @param $customer Customer object or ID
		 * @param $card_id
		 *
		 * @return Customer
		 *
		 * @throws \Stripe\Exception\ApiErrorException
		 * @since 1.0.0
		 */
		public function set_default_card( $customer, $card_id ) {
			$result = $this->update_customer( $customer, array(
				'default_source' => $card_id
			) );

			do_action( 'yith_wcstripe_connect_card_set_default', $customer, $card_id );

			return $result;
		}

		/**
		 *  Remove a source from a customer.
		 *
		 * @param $customer Customer object or ID
		 * @param $source_id
		 *
		 * @return Customer
		 *
		 * @throws \Stripe\Exception\ApiErrorException
		 * @since 1.1.0
		 */
		public function delete_source( $customer_id, $source_id ) {
			$customer = $this->get_customer( $customer_id );
			/**@var \Stripe\Source $source */
			$source = $customer->sources->retrieve( $source_id );
			$source->detach();

			return $customer;
		}

		/* === PAYMENT INTENTS METHODS === */

		/**
		 * Retrieve a payment intent object on stripe, using id passed as argument
		 *
		 * @param $payment_intent_id int Payment intent id
		 *
		 * @return \Stripe\StripeObject|bool Payment intent or false
		 */
		public function get_intent( $payment_intent_id ) {
			try {
				return PaymentIntent::retrieve( $payment_intent_id );
			} catch ( \Stripe\Exception\ApiErrorException $e ) {
				return false;
			}
		}

		/**
		 * Create a payment intent object on stripe, using parameters passed as argument
		 *
		 * @param $params array Array of parameters used to create Payment intent
		 *
		 * @return \Stripe\StripeObject|bool Brand new payment intent or false on failure
		 * @throws \Stripe\Exception\ApiErrorException
		 */
		public function create_intent( $params ) {
			return PaymentIntent::create( $params, array(
				'idempotency_key' => self::generateRandomString( 24, $params )
			) );
		}

		/**
		 * Update a payment intent object on stripe, using parameters passed as argument
		 *
		 * @param $params array Array of parameters used to update Payment intent
		 *
		 * @return \Stripe\StripeObject|bool Updated payment intent or false on failure
		 */
		public function update_intent( $payment_intent_id, $params ) {
			try {
				return PaymentIntent::update( $payment_intent_id, $params );
			} catch ( \Stripe\Exception\ApiErrorException $e ) {
				return false;
			}
		}

		/**
		 * Retrieve a payment method object on stripe, using id passed as argument
		 *
		 * @param $payment_method_id int Payment method id
		 *
		 * @return \Stripe\StripeObject|bool Payment intent or false
		 */
		public function get_payment_method( $payment_method_id ) {
			try {
				return PaymentMethod::retrieve( $payment_method_id );
			} catch ( \Stripe\Exception\ApiErrorException $e ) {
				return false;
			}
		}

		/**
		 * Detach a payment method from the customer
		 *
		 * @param $payment_method_id string Payment method id
		 *
		 * @return StripeObject|bool Detached payment method, or false on failure
		 */
		public function delete_payment_method( $payment_method_id ) {
			try {
				return PaymentMethod::retrieve( $payment_method_id )->detach();
			} catch ( \Stripe\Exception\ApiErrorException $e ) {
				return false;
			}
		}

		/**
		 * Retrieve a setup intent object on stripe, using id passed as argument
		 *
		 * @param $payment_intent_id int Setup intent id
		 *
		 * @return \Stripe\StripeObject|bool Setup intent or false
		 */
		public function get_setup_intent( $setup_intent_id ) {
			try {
				return SetupIntent::retrieve( $setup_intent_id );
			} catch ( \Stripe\Exception\ApiErrorException $e ) {
				return false;
			}
		}

		/**
		 * Create a payment intent object on stripe, using parameters passed as argument
		 *
		 * @param $params array Array of parameters used to create Payment intent
		 *
		 * @return \Stripe\StripeObject|bool Brand new payment intent or false on failure
		 */
		public function create_setup_intent( $params ) {
			try {
				return SetupIntent::create( $params, array(
					'idempotency_key' => self::generateRandomString( 24, $params )
				) );
			} catch ( \Stripe\Exception\ApiErrorException $e ) {
				return false;
			}
		}

		/**
		 * Update a setup intent object on stripe, using parameters passed as argument
		 *
		 * @param $params array Array of parameters used to update Payment intent
		 *
		 * @return \Stripe\StripeObject|bool Updated payment intent or false on failure
		 */
		public function update_setup_intent( $setup_intent_id, $params ) {
			try {
				return SetupIntent::update( $setup_intent_id, $params );
			} catch ( \Stripe\Exception\ApiErrorException $e ) {
				return false;
			}
		}

		/**
		 * Retrieve a PaymentIntent or a SetupIntent, depending on the id that it receives
		 *
		 * @param $id string Id of the intent that method should retrieve
		 *
		 * @return \Stripe\StripeObject|bool Intent or false on failure
		 */
		public function get_correct_intent( $id ) {
			try {
				if ( strpos( $id, 'seti' ) !== false ) {
					return $this->get_setup_intent( $id );
				} else {
					return $this->get_intent( $id );
				}
			} catch ( Exception $e ) {
				return false;
			}
		}

		/**
		 * Update a PaymentIntent or a SetupIntent, depending on the id that it receives
		 *
		 * @param $id     string Id of the intent that method should retrieve
		 * @param $params array Array of parameters that should be used to update intent
		 *
		 * @return \Stripe\StripeObject|bool Intent or false on failure
		 */
		public function update_correct_intent( $id, $params ) {
			try {
				if ( strpos( $id, 'seti' ) !== false ) {
					return $this->update_setup_intent( $id, $params );
				} else {
					return $this->update_intent( $id, $params );
				}
			} catch ( Exception $e ) {
				return false;
			}
		}

		/**
		 * Genereate a semi-random string
		 *
		 * @since 1.0.0
		 */
		protected static function generateRandomString( $length = 24, $params = [] ) {
			if ( isset( $params['metadata']['order_id'] ) ) {
				$randomString = md5( $params['metadata']['order_id'] );
			} else {
				$characters       = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTU';
				$charactersLength = strlen( $characters );
				$randomString     = '';
				for ( $i = 0; $i < $length; $i ++ ) {
					$randomString .= $characters[ rand( 0, $charactersLength - 1 ) ];
				}
			}

			return $randomString;
		}

		/**
		 * @param null $options
		 *
		 * @return Exception|\Stripe\Balance|\Stripe\Exception\ApiConnectionException
		 * @throws \Stripe\Exception\ApiErrorException
		 * @since 2.0.4
		 */
		public function get_balance( $options = null ) {

			try {
				return \Stripe\Balance::retrieve( $options );
			} catch ( \Stripe\Exception\ApiConnectionException $e ) {
				return $e;
			}
		}

		/**
		 * Get balance transaction
		 *
		 * @param $transaction_id int Balance transaction id
		 *
		 * @return \Stripe\BalanceTransaction Object
		 * @throws \Stripe\Exception\ApiErrorException
		 * @since 2.0.4
		 */
		public function get_balance_transaction( $transaction_id ) {

			try {
				return \Stripe\BalanceTransaction::retrieve( $transaction_id );
			} catch ( \Stripe\Exception\ApiConnectionException $e ) {
				return $e;
			}
		}
	}
}

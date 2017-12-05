<?php
/**
 * Plugin Name: Dude Facebook events
 * Plugin URI: https://github.com/digitoimistodude/dude-facebook-events
 * Description: Fetches upcoming events for Facebook page.
 * Version: 0.1.0
 * Author: Digitoimisto Dude Oy, Timi Wahalahti
 * Author URI: https://www.dude.fi
 * Requires at least: 4.4
 * Tested up to: 4.7.3
 *
 * Text Domain: dude-facebook-events
 * Domain Path: /languages
 */

if( !defined( 'ABSPATH' )  )
	exit();

Class Dude_Facebook_Events {
  private static $_instance = null;

  /**
   * Construct everything and begin the magic!
   *
   * @since   0.1.0
   * @version 0.1.0
   */
  public function __construct() {
    // Add actions to make magic happen
    add_action( 'init', array( $this, 'load_plugin_textdomain' ) );
  } // end function __construct

  /**
   *  Prevent cloning
   *
   *  @since   0.1.0
   *  @version 0.1.0
   */
  public function __clone () {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'dude-facebook-events' ) );
	} // end function __clone

  /**
   *  Prevent unserializing instances of this class
   *
   *  @since   0.1.0
   *  @version 0.1.0
   */
  public function __wakeup() {
    _doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'dude-facebook-events' ) );
  } // end function __wakeup

  /**
   *  Ensure that only one instance of this class is loaded and can be loaded
   *
   *  @since   0.1.0
   *  @version 0.1.0
	 *  @return  Main instance
   */
  public static function instance() {
    if( is_null( self::$_instance ) ) {
      self::$_instance = new self();
    }

    return self::$_instance;
  } // end function instance

  /**
   *  Load plugin localisation
   *
   *  @since   0.1.0
   *  @version 0.1.0
   */
  public function load_plugin_textdomain() {
    load_plugin_textdomain( 'dude-facebook-events', false, dirname( plugin_basename( __FILE__ ) ).'/languages/' );
  } // end function load_plugin_textdomain

	public function get_events( $fbid = '' ) {
		if( empty( $fbid ) )
			return;

		$transient_name = apply_filters( 'dude-facebook-events/events_transient', 'dude-fb-events-'.$fbid, $fbid );
		$events = get_transient( $transient_name );
	  if( !empty( $events ) || false != $events )
	    return $events;

		$parameters = array(
			'access_token'	=> apply_filters( 'dude-facebook-events/parameters/access_token', '' ),
      'limit'					=> apply_filters( 'dude-facebook-events/parameters/limit', '10' ),
		);

		$response = self::_call_api( $fbid, apply_filters( 'dude-facebook-events/api_call_parameters', $parameters ) );
		if( $response === FALSE )
			return;

		$response = apply_filters( 'dude-facebook-events/events', json_decode( $response['body'], true ) );
    $response = $response['data'];

    foreach( $response as $key => $event ) {
      if( strtotime( 'now' ) > strtotime( $event['start_time'] ) ) {
        unset( $response[ $key ] );
      }

      unset( $response[ $key ]['place'] );
      unset( $response[ $key ]['timezone'] );
    }

		set_transient( $transient_name, $response, apply_filters( 'dude-facebook-events/events_transient_lifetime', '600' ) );

		return $response;
	} // end function get_events

	private function _call_api( $fbid = '', $parameters = array() ) {
		if( empty( $fbid ) )
			return false;

		if( empty( $parameters ) )
			return false;

    $parameters = http_build_query( $parameters );
		$response = wp_remote_get( 'https://graph.facebook.com/v2.8/'.$fbid.'/events/?'.$parameters );

		if( $response['response']['code'] !== 200 ) {
			self::_write_log( 'response status code not 200 OK, was '.$response['response']['code'].', fbid: '.$fbid );
			return false;
		}

		return $response;
	} // end function _call_api

	private function _write_log ( $log )  {
    if( true === WP_DEBUG ) {
      if( is_array( $log ) || is_object( $log ) ) {
        error_log( print_r( $log, true ) );
      } else {
        error_log( $log );
      }
    }
  } // end _write_log
} // end class Dude_Facebook_Events

function dude_facebook_events() {
  return new Dude_Facebook_Events();
} // end function dude_facebook_events

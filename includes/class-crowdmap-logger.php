<?php


/**
 * This class can be occasionally used to write errors to the
 * PHP error_log. 
 * 
 * @link       http://guestaba.com
 * @since      1.0.0
 * @package    CrowdMap
 * @subpackage CrowdMap/includes
 * @author     Wes Kempfer <wkempferjr@tnotw.com>
 */
 
class CrowdMap_Logger {
	
	/*
	 * Write specified error to the error if WP_DEBUG is true. 
	 * 
	 * @param string an_error
	 * @return void
	 */
	
	public static function log_error ( $an_error ) {

	    function log_error ( $an_error )  {
	        if ( true === WP_DEBUG ) {
	            if ( is_array( $an_error ) || is_object( $an_error) ) {
	                error_log( print_r( $an_error, true ) );
	            } else {
	                error_log( $an_error );
	            }
	        }
	    }
	}
	
}

?>